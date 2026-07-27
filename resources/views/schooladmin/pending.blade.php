@extends('layouts.auth')

@push('styles')
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
    </style>
@endpush

@section('body_class', 'hold-transition')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center mt-5">
        <div class="col-md-7">
            <div class="card shadow-sm" style="border-top: 4px solid #ffc107; border-radius: 12px;">
                <div class="card-body p-5 text-center">
                    <i class="fas fa-hourglass-half fa-4x text-warning mb-4"></i>
                    <h2 class="font-weight-bold">Awaiting Approval</h2>
                    <p class="text-muted mb-4" style="font-size: 1.1em;">
                        Your school registration for
                        <strong>{{ $school->name }}</strong>
                        has been submitted and is currently under review.
                    </p>
                    <div class="alert alert-warning text-left">
                        <h6 class="font-weight-bold">
                            <i class="fas fa-info-circle mr-1"></i> What happens next?
                        </h6>
                        <ul class="mb-0">
                            <li>Our team will review your registration details</li>
                            <li>You will receive an email notification once a decision is made</li>
                            <li>Approval typically takes 1-2 business days</li>
                        </ul>
                    </div>
                    <hr>
                    <p class="text-muted">
                        Registered email: <strong>{{ auth()->user()->email }}</strong>
                    </p>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
