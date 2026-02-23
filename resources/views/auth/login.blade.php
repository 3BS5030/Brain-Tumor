@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="hospital-card p-4 p-md-5 reveal-up">
            <h1 class="h4 mb-3">{{ __('messages.login_title') }}</h1>
            <p class="text-secondary mb-4">{{ __('messages.login_subtitle') }}</p>

            <form method="post" action="{{ route('login.perform') }}" class="d-grid gap-3">
                @csrf
                <div>
                    <label class="form-label">{{ __('messages.email') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
                    @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="form-label">{{ __('messages.password') }}</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">{{ __('messages.remember_me') }}</label>
                </div>
                <button type="submit" class="btn btn-hospital">{{ __('messages.login') }}</button>
            </form>

            <p class="mt-4 mb-0">{{ __('messages.no_account') }} <a href="{{ route('register.form') }}">{{ __('messages.create_one') }}</a></p>
        </div>
    </div>
</div>
@endsection
