<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): RedirectResponse
    {
        $user = Auth::user();
        return match (true) {
            $user->hasRole('admin') => redirect()->route('admin.dashboard'),
            $user->hasRole('guru')  => redirect()->route('guru.dashboard'),
            $user->hasRole('ks')    => redirect()->route('ks.dashboard'),
            default                 => redirect()->route('login'),
        };
    }
}
