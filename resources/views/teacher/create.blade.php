@extends('layouts.app')
@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Add New Teacher</h1>
                </div>
                <div class="col-sm-6 text-left text-md-right mt-2">
                    <a href="{{ route('teachers.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Teachers
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

            <form action="{{ route('teachers.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4">
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
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Middle Name</label>
                                            <input type="text" name="middle_name" class="form-control"
                                                value="{{ old('middle_name') }}">
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
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="card card-outline card-info">
                            <div class="card-header">
                                <h3 class="card-title">Personal Information</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Phone Number</label>
                                            <input type="text" name="phone" class="form-control"
                                                value="{{ old('phone') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Date of Birth</label>
                                            <input type="date" name="date_of_birth" class="form-control"
                                                value="{{ old('date_of_birth') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Gender</label>
                                            <select name="gender" class="form-control">
                                                <option value="">-- Select --</option>
                                                @foreach(['Male', 'Female', 'Other'] as $gender)
                                                    <option value="{{ $gender }}"
                                                        {{ old('gender') == $gender ? 'selected' : '' }}>
                                                        {{ $gender }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Marital Status</label>
                                            <select name="marital_status" class="form-control">
                                                <option value="">-- Select --</option>
                                                @foreach(['Single', 'Married', 'Divorced', 'Widowed'] as $status)
                                                    <option value="{{ $status }}"
                                                        {{ old('marital_status') == $status ? 'selected' : '' }}>
                                                        {{ $status }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Address</label>
                                            <textarea name="address" class="form-control"
                                                rows="2">{{ old('address') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card card-outline card-success">
                            <div class="card-header">
                                <h3 class="card-title">Professional Information</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Qualification</label>
                                            <input type="text" name="qualification" class="form-control"
                                                value="{{ old('qualification') }}"
                                                placeholder="e.g. B.Sc, M.Ed">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="form-group">
                                            <label>Hire Date</label>
                                            <input type="date" name="hire_date" class="form-control"
                                                value="{{ old('hire_date') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-center text-md-right">
                                <a href="{{ route('teachers.index') }}" class="btn btn-secondary mb-1 mr-2">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary mb-1">
                                    Create Teacher
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
