@extends('layouts.app')
@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Add New Bursar</h1>
                </div>
                <div class="col-sm-6 text-left text-md-right mt-2">
                    <a href="{{ route('bursars.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Bursars
                    </a>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('bursars.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-5">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Account Info</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>First Name <span class="text-danger">*</span></label>
                                            <input type="text" name="first_name" class="form-control"
                                                value="{{ old('first_name') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Last Name <span class="text-danger">*</span></label>
                                            <input type="text" name="last_name" class="form-control"
                                                value="{{ old('last_name') }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ old('email') }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Temporary Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password" class="form-control" required>
                                    <small class="text-muted">Min. 8 characters.</small>
                                </div>
                                <div class="form-group">
                                    <label>Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7">
                        <div class="card card-outline card-info">
                            <div class="card-header">
                                <h3 class="card-title">Bursar Information</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Employee ID</label>
                                    <input type="text" class="form-control" value="Auto-generated on save" readonly disabled>
                                    <small class="text-muted">
                                        Auto-generated, e.g. <code>SMC-BUR-2026-0001</code>.
                                    </small>
                                </div>
                                <div class="form-group">
                                    <label>Phone Number <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control"
                                        value="{{ old('phone') }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Address</label>
                                    <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                                </div>
                            </div>
                            <div class="card-footer text-center text-md-right">
                                <a href="{{ route('bursars.index') }}" class="btn btn-secondary mb-1 mr-2">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary mb-1">
                                    Create Bursar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection
