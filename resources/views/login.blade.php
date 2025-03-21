<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"> <!-- Link to Laravel's CSS -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}"> 
    <link rel="icon" type="image/png" href="{{ asset('images/gecologo.png') }}" sizes="512x512">

    
    <!-- Bootstrap Icons (for the eye icon) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

</head>
<body>
    <div class="login-container">
        <h1>Login</h1>
        <form method="POST" action="{{ route('login.post') }}"> <!-- Changed route to 'login.post' -->
            @csrf <!-- CSRF token for security -->
            
            <!-- Email Field -->
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" 
                    required value="{{ session()->has('errors') ? '' : old('email') }}" 
                    autocomplete="off">
                @error('email') <!-- Error handling for email -->
                    <div style="color: red;">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password Field -->
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" 
                    required autocomplete="new-password">
                    <i class="bi bi-eye-slash toggle-password" onclick="togglePassword()"></i>
                @error('password') <!-- Error handling for password -->
                    <div style="color: red;">{{ $message }}</div>
                @enderror
            </div>


            <!-- Submit Button -->
            <div class="form-group">
                <button type="submit">Login</button>
            </div>
        </form>

        @if(session('error')) <!-- Display login error message -->
            <div style="color: red; text-align: center;">{{ session('error') }}</div>
        @endif
    </div>

    <script>
        function togglePassword() {
            var passwordField = document.getElementById("password");
            var icon = document.querySelector(".toggle-password");

            if (passwordField.type === "password") {
                passwordField.type = "text"; // Show password
                icon.classList.remove("bi-eye-slash"); // Remove closed eye
                icon.classList.add("bi-eye"); // Show open eye
            } else {
                passwordField.type = "password"; // Hide password
                icon.classList.remove("bi-eye"); // Remove open eye
                icon.classList.add("bi-eye-slash"); // Show closed eye
            }
        }

    </script>
</body>


</html>
