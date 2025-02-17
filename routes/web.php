<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\SupportDashboardController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\AdminManageController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SupportManageController;
use App\Http\Controllers\UserManageController;
use App\Http\Controllers\RequestController;



// Root route pointing to the login page
Route::get('/', function () {
    return view('login');
})->name('login');

// Handle login request
Route::post('/', [AuthController::class, 'login'])->name('login.post');

// Registration Routes
Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
Route::post('/signup', [AuthController::class, 'signup'])->name('signup.post');

Route::get('/admindashboard', [AdminDashboardController::class, 'index'])
    ->name('admindashboard')
    ->middleware('auth');




Route::get('/supportdashboard', function () {
    return view('support.supportdashboard');
})->name('supportdashboard')->middleware('auth');

Route::get('/userdashboard', function () {
    return view('user.userdashboard');
})->name('userdashboard')->middleware('auth');


// Logout Route
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Admin Routes (Admin management pages)
Route::middleware(['auth'])->group(function () {
    Route::get('/adminmanage', [AdminManageController::class, 'index'])->name('adminmanage');
    Route::delete('/adminmanage/delete/{id}', [AdminManageController::class, 'destroy'])->name('adminmanage.delete');
    Route::get('/adminmanage/edit/{id}', [AdminManageController::class, 'edit'])->name('adminmanage.edit');
    Route::put('/adminmanage/save-edited/{id}', [AdminManageController::class, 'saveEdited'])->name('adminmanage.saveEdited');
    Route::post('/adminmanage/store', [AdminManageController::class, 'store'])->name('adminmanage.store');
});

Route::get('/admin/users/count', [AdminDashboardController::class, 'getUserCount'])
    ->name('admin.users.count')
    ->middleware('auth');
Route::get('/admin/users/department-count', [AdminDashboardController::class, 'getDepartmentCounts']);


// Support Routes (Support management pages)
Route::middleware(['auth'])->group(function () {
    Route::get('/supportmanage', [SupportManageController::class, 'index'])->name('supportmanage');
    Route::delete('/supportmanage/delete/{id}', [SupportManageController::class, 'destroy'])->name('supportmanage.delete');
    Route::get('/supportmanage/edit/{id}', [SupportManageController::class, 'edit'])->name('supportmanage.edit');
    Route::put('/supportmanage/save-edited/{id}', [SupportManageController::class, 'saveEdited'])->name('supportmanage.saveEdited');
    Route::post('/supportmanage/store', [SupportManageController::class, 'store'])->name('supportmanage.store');

    Route::get('/supportmanage/addprofile', [SupportManageController::class, 'create'])->name('support.addprofile');
});


// User Routes (User management pages)
Route::middleware(['auth'])->group(function () {
    Route::get('/usermanage', [UserManageController::class, 'index'])->name('usermanage');
    Route::get('/usermanage/addrequest', [UserManageController::class, 'create'])->name('user.addrequest');
    Route::post('/requests', [RequestController::class, 'store'])->name('requests.store');
});


// Request Routes
Route::get('/requests/edit/{id}', [RequestController::class, 'edit'])->name('requests.edit');
Route::match(['PUT', 'POST'], '/requests/update/{id}', [RequestController::class, 'saveEdited'])->name('requests.update');
Route::delete('/requests/delete/{id}', [RequestController::class, 'destroy'])->name('requests.destroy');
Route::get('/requests', [RequestController::class, 'index'])->name('requests.index');
Route::delete('/requests/{id}', [RequestController::class, 'destroy'])->name('requests.destroy');
Route::post('/requests/{id}/delete-attachment', [RequestController::class, 'deleteAttachment'])->name('requests.deleteAttachment');