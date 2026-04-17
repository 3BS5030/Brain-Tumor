<?php

namespace App\Infrastructure\Prediction;

use App\Domain\Prediction\Contracts\BrainTumorPredictorInterface;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class PythonBrainTumorPredictor implements BrainTumorPredictorInterface
{
    public function predict(string $imagePath): array
    {
        if (! is_file($imagePath)) {
            throw new RuntimeException('Prediction image not found: '.$imagePath);
        }

        $serviceUrl = rtrim((string) config('brain_tumor.service_url'), '/').'/predict';

        try {
            $response = Http::timeout((int) config('brain_tumor.service_timeout_seconds', 120))
                ->attach('scan', file_get_contents($imagePath), basename($imagePath))
                ->post($serviceUrl);
        } catch (ConnectionException $exception) {
            Log::error('Brain tumor prediction service connection failed.', [
                'service_url' => $serviceUrl,
                'image_path' => $imagePath,
                'message' => $exception->getMessage(),
            ]);

            throw new RuntimeException('Prediction service is unavailable.', previous: $exception);
        }

        if (! $response->successful()) {
            Log::error('Brain tumor prediction service returned an error response.', [
                'service_url' => $serviceUrl,
                'status' => $response->status(),
                'body' => $response->body(),
                'image_path' => $imagePath,
            ]);

            throw new RuntimeException('Prediction service error: '.$response->status().' '.$response->body());
        }

        $output = $response->json();

        if (! is_array($output) || ! isset($output['predicted_label'], $output['confidence'], $output['scores'])) {
            Log::error('Brain tumor prediction service returned an invalid payload.', [
                'service_url' => $serviceUrl,
                'payload' => $response->body(),
                'image_path' => $imagePath,
            ]);

            throw new RuntimeException('Invalid prediction response from Python service.');
        }

        return [
            'predicted_label' => (string) $output['predicted_label'],
            'confidence' => (float) $output['confidence'],
            'scores' => (array) $output['scores'],
        ];
    }
}
