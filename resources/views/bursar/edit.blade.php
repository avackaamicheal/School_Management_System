@extends('layouts.app')
@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Edit Bursar</h1>
                    </div>
                    <div class="col-sm-6">
                        <a href="{{ route('bursars.index') }}" class="btn btn-secondary float-right">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                @if ($errors->any())
                    <div class="alert alert-danger">{{ $errors->first() }}</div>
                @endif

                <form action="{{ route('bursars.update', $bursar->id) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="row">
                        <div class="col-md-5">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Account Info</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Full Name</label>
                                        <input type="text" name="name" class="form-control"
                                            value="{{ old('name', $bursar->name) }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Email Address</label>
                                        <input type="email" name="email" class="form-control"
                                            value="{{ old('email', $bursar->email) }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>New Password <small class="text-muted">(leave blank to keep
                                                current)</small></label>
                                        <input type="password" name="password" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Confirm Password</label>
                                        <input type="password" name="password_confirmation" class="form-control">
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
                                        <input type="text" class="form-control"
                                            value="{{ $bursar->bursarProfile?->employee_id ?? 'N/A' }}" readonly disabled>
                                        <small class="text-muted">Auto-generated, cannot be changed.</small>
                                    </div>
                                    <div class="form-group">
                                        <label>Phone Number</label>
                                        <input type="text" name="phone" class="form-control"
                                            value="{{ old('phone', $bursar->bursarProfile?->phone) }}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Address</label>
                                        <textarea name="address" class="form-control"
                                            rows="2">{{ old('address', $bursar->bursarProfile?->address) }}</textarea>
                                    </div>
                                </div>
                                <div class="card-footer text-center text-md-right">
                                    <a href="{{ route('bursars.index') }}" class="btn btn-secondary mb-1 mr-2">
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary mb-1">
                                        Update Bursar
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
