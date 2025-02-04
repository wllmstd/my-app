<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminManageController;
use Illuminate\Support\Facades\Auth;

// Root route pointing to the login page
Route::get('/', function () {
    return view('login'); // This points to login.blade.php (no change needed here)
})->name('login');

// POST route to handle login form submission
Route::post('/', [AuthController::class, 'login'])->name('login.post');

// Registration Routes
Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup'); // Show the registration form
Route::post('/signup', [AuthController::class, 'signup'])->name('signup.post'); // Handle registration submission

// Protected Home Route (redirect here after login)
Route::get('/admindashboard', function () {
    return view('admin.admindashboard'); // Updated to point to 'admin/admindashboard.blade.php'
})->name('admindashboard')->middleware('auth'); // Add middleware to protect the home page

// Logout Route
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');


// Admin Routes (Admin management pages)
Route::get('/adminmanage', [AdminManageController::class, 'index'])->name('adminmanage');
Route::get('/adminmanage/edit/{id}', [AdminManageController::class, 'edit'])->name('adminmanage.edit');
Route::delete('/adminmanage/delete/{id}', [AdminManageController::class, 'destroy'])->name('adminmanage.delete');
Route::put('/adminmanage/edit/{id}', [AdminManageController::class, 'update'])->name('adminmanage.update');
//Adding the User
Route::post('/adminmanage/store', [AdminManageController::class, 'store'])->name('adminmanage.store');