<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;


class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count(); // Count total users in the system

        // Count users by department
        $departmentCounts = User::selectRaw("department, COUNT(*) as count")
            ->whereIn('department', ['Talent Acquisition', 'Admin', 'Profiler'])
            ->groupBy('department')
            ->pluck('count', 'department');

        return view('admin.admindashboard', compact('totalUsers', 'departmentCounts'));
    }

    public function getDepartmentCounts()
    {
        // Fetch department user counts for AJAX request
        $departmentCounts = User::selectRaw("department, COUNT(*) as count")
            ->whereIn('department', ['Talent Acquisition', 'Admin', 'Profiler'])
            ->groupBy('department')
            ->pluck('count', 'department');

        return response()->json($departmentCounts);
    }

    public function getUserCount()
    {
        $totalUsers = User::count();
        return response()->json(['totalUsers' => $totalUsers]);
    }
}
