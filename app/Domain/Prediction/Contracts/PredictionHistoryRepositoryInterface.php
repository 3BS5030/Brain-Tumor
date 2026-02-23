<?php

namespace App\Domain\Prediction\Contracts;

use App\Models\PredictionHistory;
use Illuminate\Support\Collection;

interface PredictionHistoryRepositoryInterface
{
    public function createForUser(int $userId, array $payload): PredictionHistory;

    /** @return Collection<int, PredictionHistory> */
    public function listByUser(int $userId): Collection;
}
