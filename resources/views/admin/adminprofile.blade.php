@extends('layouts.app')

@section('title', 'Admin Profile')

@section('content')
<link rel="stylesheet" href="{{ asset('css/admin/adminprofile.css') }}">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}
                <button type="button" class="custom-close" data-bs-dismiss="alert" aria-label="Close">&times;</button>

            </div>
            @endif

            <div class="card shadow-lg">
                <div class="card-header" style="font-weight: 900 !important;">
                    Admin Profile
                </div>

                <div class="card-body">
                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <!-- Left Column: Profile Image & Upload Button -->
                            <div class="col-md-4 text-center">
                                <div class="profile-img-container">
                                    <img id="profilePreview" src="{{ asset('storage/' . Auth::user()->image) }}"
                                        alt="Profile Image" class="rounded-circle img-fluid border" width="300"
                                        height="300"
                                        onerror="this.onerror=null; this.src='{{ asset('storage/profile_images/default.png') }}';">

                                    <div class="mt-3">
                                        <label for="profileImageInput" class="btn btn-outline-p">
                                            <i class="fa fa-plus"></i> Upload New
                                        </label>
                                        <input type="file" name="image" id="profileImageInput" class="d-none"
                                            accept="image/*">
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: User Details -->
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="first_name">First Name</label>
                                        <input type="text" name="first_name" class="form-control"
                                            value="{{ $user->first_name }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="last_name">Last Name</label>
                                        <input type="text" name="last_name" class="form-control"
                                            value="{{ $user->last_name }}" required>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ $user->email }}"
                                        required>
                                </div>

                                <div class="mt-3">
                                    <label for="department">Department</label>
                                    <input type="text" name="department" class="form-control"
                                        value="{{ $user->department }}" required>
                                </div>

                                <div class="mt-3">
                                    <label for="password">New Password</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="password" name="password" id="password" class="form-control"
                                                placeholder="Enter new password" autocomplete="new-password"
                                                onfocus="this.value=''" onblur="if(this.value===''){ this.value=''; }">
                                        </div>

                                        <div class="col-md-6">
                                            <input type="password" name="password_confirmation"
                                                id="password_confirmation" class="form-control"
                                                placeholder="Confirm new password">
                                        </div>
                                    </div>
                                    <small id="passwordMatchError" class="text-danger d-none">Passwords do not
                                        match.</small>
                                </div>


                                <div class="text-center mt-4">
                                    <button type="submit" class="btn btn-primary w-50">Update Profile</button>
                                </div>
                            </div>
                        </div> <!-- End of Row -->
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('profileImageInput').addEventListener('change', function(event) {
    let reader = new FileReader();
    reader.onload = function() {
        document.getElementById('profilePreview').src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
});


document.addEventListener("DOMContentLoaded", function() {
    let password = document.getElementById("password");
    let confirmPassword = document.getElementById("password_confirmation");
    let errorText = document.getElementById("passwordMatchError");

    function validatePasswords() {
        if (password.value !== confirmPassword.value && confirmPassword.value !== "") {
            errorText.classList.remove("d-none");
        } else {
            errorText.classList.add("d-none");
        }
    }

    password.addEventListener("input", validatePasswords);
    confirmPassword.addEventListener("input", validatePasswords);
});
</script>

@endsection