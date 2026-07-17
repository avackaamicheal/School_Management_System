@extends('layouts.app')
@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">School Profile</h1>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('school.profile.update', $school->slug) }}" method="POST"
                enctype="multipart/form-data">
                @csrf @method('PUT')

                <div class="row">
                    {{-- Left: Basic Info --}}
                    <div class="col-md-6">
                        <div class="card card-outline card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Basic Information</h3>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <label for="logo" style="cursor: pointer;">
                                        <img src="{{ $school->logo ? asset('storage/' . $school->logo) : asset('dist/img/AdminLTELogo.png') }}"
                                            id="logo-preview" alt="School Logo"
                                            style="width: 100px; height: 100px; object-fit: contain; border: 2px dashed #ddd; border-radius: 8px; padding: 5px;">
                                        <div class="text-muted small mt-1">Click to upload logo</div>
                                    </label>
                                    <input type="file" name="logo" id="logo" class="d-none" accept="image/*"
                                        onchange="document.getElementById('logo-preview').src = URL.createObjectURL(this.files[0])">
                                </div>
                                <div class="form-group">
                                    <label>School Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', $school->name) }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control"
                                        value="{{ old('email', $school->email) }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Phone Number</label>
                                    <input type="text" name="phone_number" class="form-control"
                                        value="{{ old('phone_number', $school->phone_number) }}">
                                </div>
                                <div class="form-group">
                                    <label>Address</label>
                                    <textarea name="address" class="form-control"
                                        rows="3">{{ old('address', $school->address) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right: Admin Profile --}}
                    <div class="col-md-6">
                        <div class="card card-outline card-info">
                            <div class="card-header">
                                <h3 class="card-title">Admin Profile</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Your Name <span class="text-danger">*</span></label>
                                    <input type="text" name="admin_name" class="form-control"
                                        value="{{ old('admin_name', $admin->name) }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Your Email <span class="text-danger">*</span></label>
                                    <input type="email" name="admin_email" class="form-control"
                                        value="{{ old('admin_email', $admin->email) }}" required>
                                </div>
                                <hr>
                                <div class="form-group">
                                    <label>New Password</label>
                                    <input type="password" name="admin_password" class="form-control"
                                        placeholder="Leave blank to keep current">
                                </div>
                                <div class="form-group">
                                    <label>Confirm Password</label>
                                    <input type="password" name="admin_password_confirmation" class="form-control"
                                        placeholder="Confirm new password">
                                </div>
                                <hr>
                                <div class="form-group">
                                    <label>Profile Picture</label>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $admin->avatar ? asset('storage/' . $admin->avatar) : asset('dist/img/user2-160x160.jpg') }}"
                                            id="avatar-preview" alt="Avatar"
                                            style="width: 60px; height: 60px; object-fit: cover; border-radius: 50%; border: 2px solid #ddd;">
                                        <div class="ml-3">
                                            <input type="file" name="avatar" accept="image/*"
                                                onchange="document.getElementById('avatar-preview').src = URL.createObjectURL(this.files[0])">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Quick Stats --}}
                        <div class="card card-outline card-success">
                            <div class="card-header">
                                <h3 class="card-title">Quick Stats</h3>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span><i class="fas fa-user-graduate mr-2 text-info"></i> Total Students</span>
                                        <strong>
                                            {{ \App\Models\User::where('school_id', $school->id)->role('Student')->count() }}
                                        </strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span><i class="fas fa-chalkboard-teacher mr-2 text-success"></i> Total Teachers</span>
                                        <strong>
                                            {{ \App\Models\User::where('school_id', $school->id)->role('Teacher')->count() }}
                                        </strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span><i class="fas fa-layer-group mr-2 text-warning"></i> Total Classes</span>
                                        <strong>
                                            {{ \App\Models\ClassLevel::where('school_id', $school->id)->count() }}
                                        </strong>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span><i class="fas fa-book mr-2 text-danger"></i> Total Subjects</span>
                                        <strong>
                                            {{ \App\Models\Subject::where('school_id', $school->id)->count() }}
                                        </strong>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-footer text-right">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Changes
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
