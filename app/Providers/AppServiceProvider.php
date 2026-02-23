<?php

namespace App\Providers;

use App\Domain\Prediction\Contracts\BrainTumorPredictorInterface;
use App\Domain\Prediction\Contracts\PredictionHistoryRepositoryInterface;
use App\Infrastructure\Persistence\Prediction\EloquentPredictionHistoryRepository;
use App\Infrastructure\Prediction\PythonBrainTumorPredictor;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(BrainTumorPredictorInterface::class, PythonBrainTumorPredictor::class);
        $this->app->bind(PredictionHistoryRepositoryInterface::class, EloquentPredictionHistoryRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
