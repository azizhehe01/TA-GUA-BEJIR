<?php

namespace App\Http\Controllers;

use App\Services\WazuhService;

class DashboardController extends Controller
{
    public function index()
    {
        $critical = WazuhService::criticalLast24h();
        $critical = WazuhService::criticalLast24h();
        $high     = WazuhService::highLast24h();
        $medium   = WazuhService::mediumLast24h();
        $low      = WazuhService::lowLast24h();
        $agents   = WazuhService::activeAgents();
        $growth   = WazuhService::alertGrowth24h();

        $total = collect([$critical, $high, $medium, $low])
            ->where('ok', true)
            ->sum('count');

        $healthy = ($low['count'] ?? 0) + ($medium['count'] ?? 0);

        $healthPercent = $total > 0
            ? round(($healthy / $total) * 100, 1)
            : 0;
    
        return view('dashboard', compact('critical', 'high', 'medium', 'low', 'total', 'healthy', 'healthPercent', 'agents', 'growth'));
    }
}
