<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Client;
use App\Models\User;
use App\Models\Invoice;

class DashboardController extends Controller
{
    public function index()
    {
        // dd(Service::count());
        return Inertia::render('Dashboard', [
            'totalClients' => Client::count(),
            'totalUsers' => User::count(),
            'totalServices' => Service::count(),
        ]);
    }
}
