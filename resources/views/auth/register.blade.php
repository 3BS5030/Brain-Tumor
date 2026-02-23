@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7 col-lg-6">
        <div class="hospital-card p-4 p-md-5 reveal-up">
            <h1 class="h4 mb-3">{{ __('messages.register_title') }}</h1>
            <p class="text-secondary mb-4">{{ __('messages.register_subtitle') }}</p>

            <form method="post" action="{{ route('register.perform') }}" class="d-grid gap-3">
                @csrf
                <div>
                    <label class="form-label">{{ __('messages.name') }}</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
                    @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="form-label">{{ __('messages.email') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
                    @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="form-label">{{ __('messages.password') }}</label>
                    <input type="password" name="password" class="form-control" required>
                    @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="form-label">{{ __('messages.confirm_password') }}</label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-hospital">{{ __('messages.register') }}</button>
            </form>

            <p class="mt-4 mb-0">{{ __('messages.already_registered') }} <a href="{{ route('login.form') }}">{{ __('messages.sign_in') }}</a></p>
        </div>
    </div>
</div>
@endsection
