<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $recentLogs = AuditLog::where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard.index', compact('user', 'recentLogs'));
    }
}
