@extends('layouts.auth')

@push('styles')
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
    </style>
@endpush

@section('body_class', 'hold-transition')

@section('content')
<div class="container py-5">
    <div class="text-center text-white mb-4">
        <h2 class="font-weight-bold">
            <i class="fas fa-crown mr-2"></i> Choose Your Plan
        </h2>
        <p>Select a subscription plan to activate your school dashboard</p>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Current subscription status --}}
    @if($currentSubscription)
        @if($currentSubscription->isInGracePeriod())
            <div class="alert alert-warning text-center mb-4">
                <i class="fas fa-exclamation-triangle mr-1"></i>
                Your subscription expired on
                <strong>{{ $currentSubscription->expires_at->format('M d, Y') }}</strong>.
                You are in a <strong>7-day grace period</strong>.
                Renew now to avoid losing access.
            </div>
        @else
            <div class="alert alert-info text-center mb-4">
                <i class="fas fa-info-circle mr-1"></i>
                Current plan: <strong>{{ $currentSubscription->plan->name }}</strong> —
                Expires: <strong>{{ $currentSubscription->expires_at->format('M d, Y') }}</strong>
                ({{ $currentSubscription->daysUntilExpiry() }} days remaining)
            </div>
        @endif
    @endif

    {{-- Student count warning --}}
    @foreach($plans as $plan)
        @if($studentCount > $plan->max_students && $plan->max_students < 999999)
            {{-- Will show warning on the specific plan card --}}
        @endif
    @endforeach

    <div class="row justify-content-center">
        @foreach($plans as $plan)
            @php
                $isCurrentPlan = $currentSubscription?->plan_id === $plan->id && $currentSubscription->isActive();
                $exceedsLimit  = $studentCount > $plan->max_students && $plan->max_students < 999999;
            @endphp
            <div class="col-12 col-sm-6 col-md-3 mb-4">
                <div class="card shadow {{ $isCurrentPlan ? 'border-success' : 'border-0' }}"
                    style="border-radius: 12px; {{ $isCurrentPlan ? 'border: 2px solid #28a745 !important;' : '' }}">

                    @if($isCurrentPlan)
                        <div class="card-header bg-success text-white text-center py-2"
                            style="border-radius: 10px 10px 0 0;">
                            <small class="font-weight-bold">
                                <i class="fas fa-check-circle mr-1"></i> Current Plan
                            </small>
                        </div>
                    @endif

                    <div class="card-body text-center p-4">
                        <h4 class="font-weight-bold">{{ $plan->name }}</h4>
                        <div class="my-3">
                            <span class="h2 font-weight-bold text-primary">
                                ₦{{ number_format($plan->price) }}
                            </span>
                            <small class="text-muted d-block">per term (3 months)</small>
                        </div>

                        <hr>

                        <ul class="list-unstyled text-left mb-4">
                            <li class="mb-2">
                                <i class="fas fa-users text-primary mr-2"></i>
                                Up to
                                <strong>
                                    {{ $plan->max_students >= 999999 ? 'Unlimited' : number_format($plan->max_students) }}
                                </strong>
                                students
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-calendar text-primary mr-2"></i>
                                {{ $plan->duration_days }} days access
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success mr-2"></i>
                                All features included
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success mr-2"></i>
                                Email support
                            </li>
                        </ul>

                        @if($exceedsLimit)
                            <div class="alert alert-warning py-2 text-sm">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                You have {{ $studentCount }} students. Consider upgrading.
                            </div>
                        @endif

                        <form action="{{ route('subscription.initiate') }}" method="POST">
                            @csrf
                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                            <button type="submit"
                                class="btn btn-block {{ $isCurrentPlan ? 'btn-success' : 'btn-primary' }}"
                                style="border-radius: 8px;">
                                @if($isCurrentPlan)
                                    <i class="fas fa-sync mr-1"></i> Renew Plan
                                @else
                                    <i class="fas fa-arrow-right mr-1"></i> Subscribe
                                @endif
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="text-center mt-3">
        <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-light btn-sm">
                <i class="fas fa-sign-out-alt mr-1"></i> Logout
            </button>
        </form>
    </div>
</div>
@endsection
