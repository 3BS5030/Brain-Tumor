<?php

namespace App\Application\Prediction\UseCases;

use App\Domain\Prediction\Contracts\BrainTumorPredictorInterface;
use App\Domain\Prediction\Contracts\PredictionHistoryRepositoryInterface;
use App\Models\PredictionHistory;
use Illuminate\Support\Facades\Storage;

class PredictBrainTumorForUser
{
    public function __construct(
        private readonly BrainTumorPredictorInterface $predictor,
        private readonly PredictionHistoryRepositoryInterface $historyRepository,
    ) {
    }

    public function execute(int $userId, string $relativeImagePath): PredictionHistory
    {
        $absoluteImagePath = Storage::disk('public')->path($relativeImagePath);
        $result = $this->predictor->predict($absoluteImagePath);

        return $this->historyRepository->createForUser($userId, [
            'image_path' => $relativeImagePath,
            'predicted_class' => $result['predicted_label'],
            'confidence' => $result['confidence'],
            'raw_scores' => $result['scores'],
        ]);
    }
}
