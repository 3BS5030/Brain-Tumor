<?php

namespace App\Infrastructure\Prediction;

use App\Domain\Prediction\Contracts\BrainTumorPredictorInterface;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PythonBrainTumorPredictor implements BrainTumorPredictorInterface
{
    public function predict(string $imagePath): array
    {
        if (! is_file($imagePath)) {
            throw new RuntimeException('Prediction image not found: '.$imagePath);
        }

        $response = Http::timeout((int) config('brain_tumor.service_timeout_seconds', 120))
            ->attach('scan', file_get_contents($imagePath), basename($imagePath))
            ->post(rtrim((string) config('brain_tumor.service_url'), '/').'/predict');

        if (! $response->successful()) {
            throw new RuntimeException('Prediction service error: '.$response->status().' '.$response->body());
        }

        $output = $response->json();

        if (! is_array($output) || ! isset($output['predicted_label'], $output['confidence'], $output['scores'])) {
            throw new RuntimeException('Invalid prediction response from Python service.');
        }

        return [
            'predicted_label' => (string) $output['predicted_label'],
            'confidence' => (float) $output['confidence'],
            'scores' => (array) $output['scores'],
        ];
    }
}
