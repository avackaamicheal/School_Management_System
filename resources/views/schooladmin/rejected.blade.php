@extends('layouts.minimal')
@section('content')
    <div class="container py-5">
        <div class="row justify-content-center mt-5">
            <div class="col-md-7">
                <div class="card shadow-sm" style="border-top: 4px solid #dc3545; border-radius: 12px;">
                    <div class="card-body p-5 text-center">
                        <i class="fas fa-ban fa-4x text-danger mb-4"></i>
                        <h2 class="font-weight-bold">School Deactivated</h2>
                        <p class="text-muted mb-4" style="font-size: 1.1em;">
                            <strong>{{ $school->name }}</strong> has been deactivated
                            by the platform administrator.
                        </p>
                        @if ($school->rejection_reason)
                            <div class="alert alert-danger text-left">
                                <h6 class="font-weight-bold">
                                    <i class="fas fa-exclamation-circle mr-1"></i> Reason:
                                </h6>
                                <p class="mb-0">{{ $school->rejection_reason }}</p>
                            </div>
                        @endif
                        <hr>
                        <p class="text-muted">
                            Please contact our support team to resolve this.
                        </p>
                        <a href="mailto:support@axia.com" class="btn btn-outline-primary mr-2">
                            <i class="fas fa-envelope mr-1"></i> Contact Support
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
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
