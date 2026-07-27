@extends('layouts.auth')

@section('title', 'Register Your School | Axia SMS')

@push('styles')
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .register-card { border-radius: 15px; border: none; }
        .register-header { background: linear-gradient(135deg, #667eea, #764ba2); border-radius: 15px 15px 0 0; }
    </style>
@endpush

@section('body_class', 'py-5')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9">

                {{-- Header --}}
                <div class="text-center text-white mb-4">
                    <h2 class="font-weight-bold">
                        <i class="fas fa-school mr-2"></i> Axia School Management
                    </h2>
                    <p class="mb-0">Register your school and get started today</p>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card register-card shadow-lg">
                    <div class="register-header p-4">
                        <h4 class="text-white font-weight-bold mb-0">
                            <i class="fas fa-pen mr-2"></i> School Registration
                        </h4>
                        <small class="text-white-50">
                            Fill in the details below to register your school
                        </small>
                    </div>

                    <form action="{{ route('school.register.store') }}" method="POST">
                        @csrf
                        <div class="card-body p-4">
                            <div class="row">

                                {{-- School Details --}}
                                <div class="col-md-6">
                                    <h5 class="font-weight-bold text-primary mb-3">
                                        <i class="fas fa-school mr-1"></i> School Information
                                    </h5>

                                    <div class="form-group">
                                        <label>School Name <span class="text-danger">*</span></label>
                                        <input type="text" name="school_name"
                                            class="form-control"
                                            value="{{ old('school_name') }}"
                                            placeholder="e.g. Saint Murumba College"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label>School Email <span class="text-danger">*</span></label>
                                        <input type="email" name="school_email"
                                            class="form-control"
                                            value="{{ old('school_email') }}"
                                            placeholder="info@school.com"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label>Phone Number <span class="text-danger">*</span></label>
                                        <input type="text" name="school_phone"
                                            class="form-control"
                                            value="{{ old('school_phone') }}"
                                            placeholder="e.g. 08012345678"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label>School Address <span class="text-danger">*</span></label>
                                        <textarea name="school_address"
                                            class="form-control" rows="2"
                                            placeholder="Full school address"
                                            required>{{ old('school_address') }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label>Principal's Name <span class="text-danger">*</span></label>
                                        <input type="text" name="principal_name"
                                            class="form-control"
                                            value="{{ old('principal_name') }}"
                                            placeholder="e.g. Mr. John Smith"
                                            required>
                                    </div>
                                </div>

                                {{-- Admin Account --}}
                                <div class="col-md-6">
                                    <h5 class="font-weight-bold text-success mb-3">
                                        <i class="fas fa-user-shield mr-1"></i> Your Admin Account
                                    </h5>

                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        This will be your login account to manage the school.
                                    </div>

                                    <div class="form-group">
                                        <label>Your Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="admin_name"
                                            class="form-control"
                                            value="{{ old('admin_name') }}"
                                            placeholder="e.g. Jane Smith"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label>Your Email Address <span class="text-danger">*</span></label>
                                        <input type="email" name="admin_email"
                                            class="form-control"
                                            value="{{ old('admin_email') }}"
                                            placeholder="you@email.com"
                                            required>
                                    </div>
                                    <div class="form-group">
                                        <label>Password <span class="text-danger">*</span></label>
                                        <input type="password" name="admin_password"
                                            class="form-control" required>
                                        <small class="text-muted">Minimum 8 characters.</small>
                                    </div>
                                    <div class="form-group">
                                        <label>Confirm Password <span class="text-danger">*</span></label>
                                        <input type="password" name="admin_password_confirmation"
                                            class="form-control" required>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="card-footer bg-light d-flex justify-content-between align-items-center">
                            <a href="{{ route('login') }}" class="text-muted">
                                <i class="fas fa-arrow-left mr-1"></i> Back to Login
                            </a>
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="fas fa-paper-plane mr-1"></i> Submit Registration
                            </button>
                        </div>
                    </form>
                </div>

                <div class="text-center text-white mt-3">
                    <small>
                        Already have an account?
                        <a href="{{ route('login') }}" class="text-white font-weight-bold">
                            Login here
                        </a>
                    </small>
                </div>

            </div>
        </div>
    </div>
@endsection
