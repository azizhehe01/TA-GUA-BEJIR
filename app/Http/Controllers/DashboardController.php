<?php

namespace App\Http\Controllers;

use App\Services\WazuhService;

class DashboardController extends Controller
{
    public function index()
    {
        $critical = WazuhService::criticalLast24h();
        $high     = WazuhService::highLast24h();
        $medium   = WazuhService::mediumLast24h();
        $low      = WazuhService::lowLast24h();
    
        return view('dashboard', compact('critical', 'high', 'medium', 'low'));
    }
}
