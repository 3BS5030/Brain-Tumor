<?php

namespace App\Infrastructure\Persistence\Prediction;

use App\Domain\Prediction\Contracts\PredictionHistoryRepositoryInterface;
use App\Models\PredictionHistory;
use Illuminate\Support\Collection;

class EloquentPredictionHistoryRepository implements PredictionHistoryRepositoryInterface
{
    public function createForUser(int $userId, array $payload): PredictionHistory
    {
        return PredictionHistory::query()->create([
            'user_id' => $userId,
            ...$payload,
        ]);
    }

    public function listByUser(int $userId): Collection
    {
        return PredictionHistory::query()
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }
}
