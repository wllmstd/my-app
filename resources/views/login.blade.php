<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"> <!-- Link to Laravel's CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .login-container h1 {
            margin-bottom: 20px;
            color: #333;
            font-size: 24px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form-group button {
            width: 100%;
            padding: 10px;
            background-color: #1d4ed8;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .form-group button:hover {
            background-color: #2563eb;
        }

        .signup-link {
            text-align: center;
            margin-top: 15px;
        }

        .signup-link a {
            color: #1d4ed8;
            text-decoration: none;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }
    </style>
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
                @error('password') <!-- Error handling for password -->
                    <div style="color: red;">{{ $message }}</div>
                @enderror
            </div>


            <!-- Submit Button -->
            <div class="form-group">
                <button type="submit">Login</button>
            </div>
        </form>

        <!-- Sign Up Link -->
        <div class="signup-link">
            Don't have an account? <a href="{{ route('signup') }}">Sign up</a>
        </div>

        @if(session('error')) <!-- Display login error message -->
            <div style="color: red; text-align: center;">{{ session('error') }}</div>
        @endif
    </div>
</body>
</html>
