@extends('layouts.auth')

@section('title', 'Axia SMS | Confirm Password')

@section('content')
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="{{ url('/') }}" class="h1"><b>Axia</b>SMS</a>
            </div>
            <div class="card-body">
                <p class="login-box-msg">{{ __('Confirm Password') }}</p>

                <p>{{ __('Please confirm your password before continuing.') }}</p>

                <form method="POST" action="{{ route('password.confirm') }}">
                    @csrf

                    <div class="input-group mb-3">
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="{{ __('Password') }}">
                        <div class="input-group-append">
                            <div class="input-group-text"><span class="fas fa-lock"></span></div>
                        </div>
                        @error('password')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block">{{ __('Confirm Password') }}</button>
                        </div>
                    </div>

                    @if (Route::has('password.request'))
                        <p class="mb-0 mt-3">
                            <a href="{{ route('password.request') }}">{{ __('Forgot Your Password?') }}</a>
                        </p>
                    @endif
                </form>
            </div>
        </div>
    </div>
@endsection
