<?php

namespace App\Http\Controllers;

use App\Application\Prediction\UseCases\PredictBrainTumorForUser;
use App\Domain\Prediction\Contracts\PredictionHistoryRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly PredictionHistoryRepositoryInterface $historyRepository,
    ) {
    }

    public function index(Request $request): View
    {
        $history = $this->historyRepository->listByUser((int) $request->user()->id);

        return view('dashboard.index', [
            'history' => $history,
        ]);
    }

    public function predict(Request $request, PredictBrainTumorForUser $useCase): RedirectResponse
    {
        $validated = $request->validate([
            'scan' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ]);

        $path = $validated['scan']->store('predictions', 'public');

        $prediction = $useCase->execute((int) $request->user()->id, $path);

        return redirect()
            ->route('dashboard.index')
            ->with('prediction_id', $prediction->id);
    }
}
