<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\{
    AuthController, AdminDashboardController, SupportDashboardController, UserDashboardController,
    AdminManageController, SupportManageController, UserManageController, RequestController,
    AdminProfileController, SupportProfileController, UserProfileController
};
use App\Http\Middleware\RoleMiddleware;



// ===============================
// PUBLIC ROUTE FOR PROFILE IMAGES
// ===============================
Route::get('/profile-image/{filename}', function ($filename) {
    if (Storage::disk('public')->exists("profile_images/{$filename}")) {
        return response()->file(storage_path("app/public/profile_images/{$filename}"));
    }
    abort(404, "Image not found.");
})->middleware(['auth']); // Only logged-in users can access


// ===============================
// AUTHENTICATION ROUTES
// ===============================
Route::get('/', function () {
    return view('login');
})->name('login');

Route::post('/', [AuthController::class, 'login'])->name('login.post');

// Logout Route
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');


// ===============================
// ADMIN ROUTES (Only for Admin Role)
// ===============================
Route::middleware(['auth', RoleMiddleware::class . ':Admin'])->group(function () {
    Route::get('/admindashboard', [AdminDashboardController::class, 'index'])->name('admindashboard');

    Route::prefix('adminmanage')->group(function () {
        Route::get('/', [AdminManageController::class, 'index'])->name('adminmanage');
        Route::post('/store', [AdminManageController::class, 'store'])->name('adminmanage.store');
        Route::get('/edit/{id}', [AdminManageController::class, 'edit'])->name('adminmanage.edit');
        Route::put('/save-edited/{id}', [AdminManageController::class, 'saveEdited'])->name('adminmanage.saveEdited');
        Route::delete('/delete/{id}', [AdminManageController::class, 'destroy'])->name('adminmanage.delete');
    });

    Route::prefix('admin/profile')->group(function () {
        Route::get('/', [AdminProfileController::class, 'index'])->name('admin.profile');
        Route::post('/update', [AdminProfileController::class, 'update'])->name('admin.profile.update');
        Route::get('/image', [AdminProfileController::class, 'getProfileImage'])->name('admin.profile.image');
    });

    // Admin Charts
    Route::get('/admin/users/count', [AdminDashboardController::class, 'getUserCount'])->name('admin.users.count');
    Route::get('/admin/users/department-count', [AdminDashboardController::class, 'getDepartmentCounts']);
});

// ===============================
// SUPPORT ROUTES (Only for Support Role)
// ===============================
Route::middleware(['auth', RoleMiddleware::class . ':Profiler'])->group(function () {
    Route::get('/supportdashboard', fn() => view('support.supportdashboard'))->name('supportdashboard');
    Route::prefix('supportmanage')->group(function () {
        Route::get('/', [SupportManageController::class, 'index'])->name('supportmanage');
        Route::post('/store', [SupportManageController::class, 'store'])->name('supportmanage.store');
        Route::get('/edit/{id}', [SupportManageController::class, 'edit'])->name('supportmanage.edit');
        Route::put('/save-edited/{id}', [SupportManageController::class, 'saveEdited'])->name('supportmanage.saveEdited');
        Route::delete('/delete/{id}', [SupportManageController::class, 'destroy'])->name('supportmanage.delete');
        Route::get('/addprofile', [SupportManageController::class, 'create'])->name('support.addprofile');
    });

    Route::prefix('support/profile')->group(function () {
        Route::get('/', [SupportProfileController::class, 'index'])->name('support.supportprofile');
        Route::post('/update', [SupportProfileController::class, 'update'])->name('support.profile.update');
    });

    // Support Request Management
    Route::post('/requests/accept/{id}', [SupportManageController::class, 'acceptRequest']);
    Route::post('/requests/upload-format/{id}', [SupportManageController::class, 'uploadFormat']);
    Route::post('/requests/delete-file/{id}', [SupportManageController::class, 'deleteFile']);
    Route::post('/requests/forward/{id}', [SupportManageController::class, 'forwardRequest']);
    Route::get('/requests/check-files/{id}', [SupportManageController::class, 'checkFiles']);
});

// ===============================
// USER ROUTES (Only for Authenticated Users)
// ===============================
Route::middleware(['auth', RoleMiddleware::class . ':Talent Acquisition'])->group(function () {
    Route::get('/userdashboard', fn() => view('user.userdashboard'))->name('userdashboard');
    Route::prefix('usermanage')->group(function () {
        Route::get('/', [UserManageController::class, 'index'])->name('usermanage');
        Route::get('/addrequest', [UserManageController::class, 'create'])->name('user.addrequest');
    });
    Route::prefix('user/profile')->group(function () {
        Route::get('/', [UserProfileController::class, 'index'])->name('user.userprofile');
        Route::post('/update', [UserProfileController::class, 'update'])->name('user.profile.update');
    });

    Route::post('/requests', [RequestController::class, 'store'])->name('requests.store');
});

// ===============================
// REQUEST ROUTES
// ===============================
Route::prefix('requests')->group(function () {
    Route::get('/', [RequestController::class, 'index'])->name('requests.index');
    Route::get('/edit/{id}', [RequestController::class, 'edit'])->name('requests.edit');
    Route::match(['PUT', 'POST'], '/update/{id}', [RequestController::class, 'saveEdited'])->name('requests.update');
    Route::delete('/delete/{id}', [RequestController::class, 'destroy'])->name('requests.destroy');
    Route::post('/{id}/delete-attachment', [RequestController::class, 'deleteAttachment'])->name('requests.deleteAttachment');
    Route::post('/{id}/complete', [RequestController::class, 'markAsComplete'])->name('requests.complete');
    Route::post('/{id}/revise', [RequestController::class, 'requestRevision'])->name('requests.revise');
    Route::get('/{id}/details', [RequestController::class, 'getRequestWithProfiler']);
    Route::get('/{id}/full-details', [RequestController::class, 'getRequestDetails']);
    Route::post('/requests/{id}/mark-as-done', [SupportManageController::class, 'markAsComplete'])->name('requests.markAsComplete');

});

// ===============================
// USER DASHBOARD CHARTS
// ===============================
Route::prefix('user')->group(function () {
    Route::get('/request-counts', [UserDashboardController::class, 'getRequestCounts'])->name('user.request.counts');
    Route::get('/request-status-counts', [UserDashboardController::class, 'getRequestStatusCounts'])->name('user.request.status.counts');
    Route::get('/format-counts', [UserDashboardController::class, 'getFormatCounts'])->name('user.request.format.counts');
    Route::get('/attachments-count', [UserDashboardController::class, 'getTotalAttachments'])->name('user.request.attachments.count');
});

// ===============================
// FILE DOWNLOAD ROUTE
// ===============================
Route::get('/download/{filename}', function ($filename) {
    $filePath = 'uploads/' . $filename;

    if (Storage::disk('public')->exists($filePath)) {
        return Response::download(storage_path("app/public/{$filePath}"));
    }

    abort(404, "File not found.");
    
});


