@extends('layouts.app')

@section('content')
<div class="hero-wrap position-relative overflow-hidden rounded-4 p-4 p-md-5 mb-4">
    <div class="hero-glow hero-glow-a"></div>
    <div class="hero-glow hero-glow-b"></div>
    <div class="row g-4 align-items-center">
        <div class="col-lg-8">
            <span class="badge soft-badge px-3 py-2 mb-3 reveal-up">{{ __('messages.about_badge') }}</span>
            <h1 class="display-6 fw-bold mb-3 reveal-up" style="animation-delay:.1s;">{{ __('messages.about_title') }}</h1>
            <p class="lead text-secondary mb-0 reveal-up" style="animation-delay:.2s;">{{ __('messages.about_subtitle') }}</p>
        </div>
        <div class="col-lg-4 text-lg-end reveal-up" style="animation-delay:.3s;">
            <a href="{{ route('scan.index') }}" class="btn btn-hospital btn-lg px-4">{{ __('messages.go_to_scan') }}</a>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4 reveal-up" style="animation-delay:.1s;">
        <div class="hospital-card p-4 h-100">
            <h2 class="h6 mb-2">{{ __('messages.about_card_model_title') }}</h2>
            <p class="text-secondary mb-0 small">{{ __('messages.about_card_model_desc') }}</p>
        </div>
    </div>
    <div class="col-md-4 reveal-up" style="animation-delay:.2s;">
        <div class="hospital-card p-4 h-100">
            <h2 class="h6 mb-2">{{ __('messages.about_card_arch_title') }}</h2>
            <p class="text-secondary mb-0 small">{{ __('messages.about_card_arch_desc') }}</p>
        </div>
    </div>
    <div class="col-md-4 reveal-up" style="animation-delay:.3s;">
        <div class="hospital-card p-4 h-100">
            <h2 class="h6 mb-2">{{ __('messages.about_card_history_title') }}</h2>
            <p class="text-secondary mb-0 small">{{ __('messages.about_card_history_desc') }}</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7 reveal-up" style="animation-delay:.14s;">
        <div class="hospital-card p-4 p-md-5 h-100">
            <h2 class="h5 mb-3">{{ __('messages.about_flow_title') }}</h2>
            <ol class="about-steps mb-0">
                <li>{{ __('messages.about_flow_step_1') }}</li>
                <li>{{ __('messages.about_flow_step_2') }}</li>
                <li>{{ __('messages.about_flow_step_3') }}</li>
                <li>{{ __('messages.about_flow_step_4') }}</li>
                <li>{{ __('messages.about_flow_step_5') }}</li>
            </ol>
        </div>
    </div>
    <div class="col-lg-5 reveal-up" style="animation-delay:.26s;">
        <div class="hospital-card p-4 p-md-5 h-100">
            <h2 class="h5 mb-3">{{ __('messages.about_stack_title') }}</h2>
            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="badge soft-badge">Laravel 12</span>
                <span class="badge soft-badge">Bootstrap 5</span>
                <span class="badge soft-badge">PyTorch</span>
                <span class="badge soft-badge">Flask API</span>
                <span class="badge soft-badge">MySQL / SQLite</span>
            </div>
            <p class="text-secondary mb-0">{{ __('messages.about_stack_desc') }}</p>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .about-steps {
        margin: 0;
        padding-inline-start: 1.2rem;
    }
    .about-steps li {
        margin-bottom: .75rem;
        color: var(--hospital-text);
    }
</style>
@endpush
