@extends('layouts.auth')

@section('title', 'Axia SMS | Register')

@section('body_class', '')

@section('content')
    <div class="multi-step-wrapper">
        <div class="multi-step-container">
            <div class="auth-sidebar">
                <svg class="sidebar-bg" xmlns="http://www.w3.org/2000/svg" width="274" height="568" fill="none" viewBox="0 0 274 568" preserveAspectRatio="xMidYMid slice">
                    <mask id="a" width="274" height="568" x="0" y="0" maskUnits="userSpaceOnUse" style="mask-type:alpha">
                        <rect width="274" height="568" fill="#fff" rx="10"/>
                    </mask>
                    <g mask="url(#a)">
                        <path fill="#6259FF" fill-rule="evenodd" d="M-34.692 543.101C3.247 632.538 168.767 685.017 211.96 612.52c43.194-72.497-66.099-85.653-104.735-160.569-38.635-74.916-68.657-121.674-124.482-104.607-55.824 17.068-55.375 106.32-17.436 195.757Z" clip-rule="evenodd"/>
                        <path fill="#F9818E" fill-rule="evenodd" d="M233.095 601.153c60.679-28.278 92.839-143.526 41.875-171.528-50.965-28.003-57.397 47.579-108.059 75.987-50.662 28.408-82.14 50.207-69.044 88.241 13.096 38.034 74.549 35.578 135.228 7.3Z" clip-rule="evenodd"/>
                        <path stroke="#fff" stroke-linecap="round" stroke-linejoin="bevel" stroke-width="5" d="m165.305 469.097 10.607-10.806M209.461 474.581l-12.506-10.503M187.56 488.991l-6.908 14.798"/>
                        <path fill="#FFAF7E" d="M.305 546.891c37.003 0 67-29.997 67-67s-29.997-67-67-67-67 29.997-67 67 29.997 67 67 67Z"/>
                    </g>
                </svg>
                <div class="sidebar-text">
                    <div class="sidebar-icon"><i class="fas fa-school"></i></div>
                    <h3>Axia SMS</h3>
                    <p>School Management System</p>
                </div>
            </div>
            <div class="auth-content">
                <div class="page-header">
                    <h2>Create Account</h2>
                    <p>Join Axia School Management</p>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Your full name" required autofocus>
                        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="you@email.com" required>
                        @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimum 8 characters" required>
                        @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat your password" required>
                    </div>
                    <button type="submit" class="btn-submit">Register</button>
                </form>

                <div class="auth-links" style="margin-top:2rem;">
                    <a href="{{ route('login') }}">Already have an account? Sign in</a>
                </div>
            </div>
        </div>
    </div>
@endsection
