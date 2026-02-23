<?php

namespace App\Domain\Prediction\Contracts;

interface BrainTumorPredictorInterface
{
    /**
     * @return array{predicted_label:string, confidence:float, scores:array<string,float>}
     */
    public function predict(string $imagePath): array;
}
