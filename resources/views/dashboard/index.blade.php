@extends('layouts.app')

@section('content')
<div class="row g-4">
    <div class="col-lg-5 reveal-up">
        <div class="hospital-card p-4">
            <h2 class="h5">{{ __('messages.prediction_title') }}</h2>
            <p class="text-secondary small mb-4">{{ __('messages.prediction_subtitle') }}</p>

            <form id="scanForm" method="post" action="{{ route('dashboard.predict') }}" enctype="multipart/form-data" class="d-grid gap-3">
                @csrf
                <input id="scanInput" type="file" name="scan" class="form-control" accept=".jpg,.jpeg,.png" required>
                @error('scan') <div class="text-danger small">{{ $message }}</div> @enderror
                <button id="scanButton" class="btn btn-hospital" type="submit">{{ __('messages.run_prediction') }}</button>
            </form>

            <div class="mt-4 small text-secondary">
                <div>{{ __('messages.configured_labels') }}</div>
                <div class="mt-1">
                    @foreach(config('brain_tumor.class_labels') as $label)
                        <span class="badge soft-badge me-1 mb-1">{{ $label }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        @php
            $latest = session('prediction_id') ? $history->firstWhere('id', session('prediction_id')) : $history->first();
        @endphp

        <div class="hospital-card p-4 mb-4 reveal-up" style="animation-delay:.12s;">
            <h2 class="h5 mb-3">{{ __('messages.latest_result') }}</h2>
            @if($latest)
                <div class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <img src="{{ asset('storage/' . $latest->image_path) }}" class="img-fluid rounded" alt="MRI scan">
                    </div>
                    <div class="col-md-8">
                        <p class="mb-1"><strong>{{ __('messages.class') }}:</strong> {{ $latest->predicted_class }}</p>
                        <p class="mb-3"><strong>{{ __('messages.confidence') }}:</strong> {{ number_format($latest->confidence * 100, 2) }}%</p>
                        @foreach($latest->raw_scores as $label => $score)
                            <div class="mb-2 score-item" style="--w: {{ $score * 100 }}%;">
                                <div class="d-flex justify-content-between small">
                                    <span>{{ $label }}</span><span>{{ number_format($score * 100, 2) }}%</span>
                                </div>
                                <div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ round($score * 100) }}">
                                    <div class="progress-bar bg-info score-bar" style="width: var(--w)"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <p class="text-secondary mb-0">{{ __('messages.no_predictions') }}</p>
            @endif
        </div>

        <div class="hospital-card p-4 reveal-up" style="animation-delay:.22s;">
            <h2 class="h5 mb-3">{{ __('messages.your_prediction_history') }}</h2>
            @if($history->isEmpty())
                <p class="text-secondary mb-0">{{ __('messages.history_empty') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                        <tr>
                            <th>{{ __('messages.scan') }}</th>
                            <th>{{ __('messages.class') }}</th>
                            <th>{{ __('messages.confidence') }}</th>
                            <th>{{ __('messages.date') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($history as $entry)
                            <tr>
                                <td><img src="{{ asset('storage/' . $entry->image_path) }}" alt="scan" width="56" class="rounded"></td>
                                <td>{{ $entry->predicted_class }}</td>
                                <td>{{ number_format($entry->confidence * 100, 2) }}%</td>
                                <td>{{ $entry->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<div id="scanOverlay" class="scan-overlay d-none" aria-live="polite">
    <div class="scan-panel text-center">
        <div class="scanner-disc mx-auto mb-3">
            <div class="scanner-sweep"></div>
        </div>
        <h3 class="h5 mb-2">{{ __('messages.scan_overlay_title') }}</h3>
        <p id="scanStatus" class="text-secondary small mb-3">{{ __('messages.scan_step_1') }}</p>
        <div class="progress mb-2" style="height: 10px;">
            <div id="scanProgress" class="progress-bar progress-bar-striped progress-bar-animated bg-info" style="width: 8%"></div>
        </div>
        <small class="text-secondary">{{ __('messages.scan_overlay_wait') }}</small>
    </div>
</div>
@endsection

@push('styles')
<style>
    .score-bar { animation: barGrow 1s ease; }
    .scan-overlay {
        position: fixed;
        inset: 0;
        background: rgba(236, 246, 255, .86);
        backdrop-filter: blur(4px);
        display: grid;
        place-items: center;
        z-index: 2000;
    }
    body.dark-mode .scan-overlay {
        background: rgba(10, 18, 35, .76);
    }
    .scan-panel {
        width: min(420px, 92vw);
        background: var(--hospital-card);
        border: 1px solid var(--hospital-border);
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 10px 30px rgba(11,114,133,.18);
        animation: revealUp .35s ease;
    }
    .scanner-disc {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 3px solid rgba(11,114,133,.2);
        background: radial-gradient(circle, rgba(116,192,252,.32) 0%, rgba(116,192,252,.08) 65%, transparent 70%);
        position: relative;
        overflow: hidden;
    }
    .scanner-sweep {
        position: absolute;
        inset: -10%;
        background: conic-gradient(transparent 0deg, transparent 250deg, rgba(11,114,133,.65) 300deg, rgba(11,114,133,.12) 360deg);
        animation: spin 1.8s linear infinite;
    }
    @keyframes barGrow { from { width: 0; } }
</style>
@endpush

@push('scripts')
<script>
    (function () {
        const form = document.getElementById('scanForm');
        const overlay = document.getElementById('scanOverlay');
        const progress = document.getElementById('scanProgress');
        const statusText = document.getElementById('scanStatus');
        const button = document.getElementById('scanButton');
        const input = document.getElementById('scanInput');

        if (!form || !overlay || !progress || !statusText || !button || !input) {
            return;
        }

        const statuses = [
            @json(__('messages.scan_step_1')),
            @json(__('messages.scan_step_2')),
            @json(__('messages.scan_step_3')),
            @json(__('messages.scan_step_4')),
            @json(__('messages.scan_step_5'))
        ];

        form.addEventListener('submit', function () {
            if (!input.files || input.files.length === 0) {
                return;
            }

            overlay.classList.remove('d-none');
            button.disabled = true;
            let p = 8;
            let step = 0;

            const interval = setInterval(function () {
                p = Math.min(p + Math.random() * 14, 94);
                progress.style.width = p.toFixed(0) + '%';
                statusText.textContent = statuses[Math.min(step, statuses.length - 1)];
                step += 1;
            }, 700);

            window.addEventListener('beforeunload', function () {
                clearInterval(interval);
            });
        });
    })();
</script>
@endpush
