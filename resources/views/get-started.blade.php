@extends('layouts.app')

@section('content')
<div class="hero-wrap position-relative overflow-hidden rounded-4 p-4 p-md-5 mb-4">
    <div class="hero-glow hero-glow-a"></div>
    <div class="hero-glow hero-glow-b"></div>
    <div class="row align-items-center g-4">
        <div class="col-lg-6">
            <span class="badge soft-badge px-3 py-2 mb-3">{{ __('messages.hero_badge') }}</span>
            <h1 class="display-6 fw-bold mb-3 reveal-up">{{ __('messages.hero_title') }}</h1>
            <p class="lead text-secondary mb-4 reveal-up" style="animation-delay:.12s;">{{ __('messages.hero_subtitle') }}</p>
            <div class="d-flex flex-wrap gap-2 reveal-up" style="animation-delay:.24s;">
                @auth
                    <a href="{{ route('scan.index') }}" class="btn btn-hospital btn-lg px-4">{{ __('messages.go_to_scan') }}</a>
                @else
                    <a href="{{ route('scan.index') }}" class="btn btn-hospital btn-lg px-4">{{ __('messages.go_to_scan') }}</a>
                    <a href="{{ route('about') }}" class="btn btn-soft btn-lg px-4">{{ __('messages.about_nav') }}</a>
                    <a href="{{ route('login') }}" class="btn btn-outline-info btn-lg px-4">{{ __('messages.sign_in') }}</a>
                @endauth
            </div>
        </div>
        <div class="col-lg-6">
            <div class="scan-visual mx-auto reveal-up" style="animation-delay:.18s;">
                <div class="scan-ring"></div>
                <div class="scan-ring scan-ring-2"></div>
                <div class="scan-core">
                    <div class="scan-line"></div>
                    <div class="scan-grid"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4 reveal-up" style="animation-delay:.15s;"><div class="hospital-card p-4 h-100"><h2 class="h6">{{ __('messages.fast_screening') }}</h2><p class="text-secondary mb-0 small">{{ __('messages.fast_screening_desc') }}</p></div></div>
    <div class="col-md-4 reveal-up" style="animation-delay:.22s;"><div class="hospital-card p-4 h-100"><h2 class="h6">{{ __('messages.patient_history') }}</h2><p class="text-secondary mb-0 small">{{ __('messages.patient_history_desc') }}</p></div></div>
    <div class="col-md-4 reveal-up" style="animation-delay:.29s;"><div class="hospital-card p-4 h-100"><h2 class="h6">{{ __('messages.clinical_theme') }}</h2><p class="text-secondary mb-0 small">{{ __('messages.clinical_theme_desc') }}</p></div></div>
</div>
@endsection
