<?php

namespace App\Http\Controllers;

use App\Application\Prediction\UseCases\PredictBrainTumorForUser;
use App\Domain\Prediction\Contracts\PredictionHistoryRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

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

        try {
            $path = $validated['scan']->store('predictions', 'public');
            $prediction = $useCase->execute((int) $request->user()->id, $path);
        } catch (Throwable $exception) {
            Log::error('Brain tumor prediction request failed.', [
                'user_id' => $request->user()?->id,
                'filename' => $validated['scan']->getClientOriginalName(),
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return redirect()
                ->route('dashboard.index')
                ->withInput()
                ->with('prediction_error', $exception->getMessage());
        }

        return redirect()
            ->route('dashboard.index')
            ->with('prediction_id', $prediction->id);
    }
}
