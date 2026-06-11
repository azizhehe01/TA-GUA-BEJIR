<?php

namespace App\Http\Controllers;

use App\Services\WazuhService;
use App\Models\FimAnalysisResult;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $critical = WazuhService::criticalLast24h();
        $high     = WazuhService::highLast24h();
        $medium   = WazuhService::mediumLast24h();
        $low      = WazuhService::lowLast24h();
        $agents   = WazuhService::activeAgents();
        $growth   = WazuhService::alertGrowth24h();
        $chart7d  = WazuhService::alertsLast7Days();

        $total = collect([$critical, $high, $medium, $low])
            ->where('ok', true)
            ->sum('count');

        $healthy = ($low['count'] ?? 0) + ($medium['count'] ?? 0);

        $healthPercent = $total > 0
            ? round(($healthy / $total) * 100, 1)
            : 0;

        return view('dashboard', compact(
            'critical',
            'high',
            'medium',
            'low',
            'total',
            'healthy',
            'healthPercent',
            'agents',
            'growth',
            'chart7d'
        ));
    }

    public function fim(Request $request)
    {
        $date = $request->query('date', now()->toDateString());
        $classification = $request->query('classification');

        $baseQuery = FimAnalysisResult::whereDate('analysis_date', $date);

        $summary = [
            'total' => (clone $baseQuery)->count(),
            'aman' => (clone $baseQuery)->where('classification', 'aman')->count(),
            'mencurigakan' => (clone $baseQuery)->where('classification', 'mencurigakan')->count(),
            'berbahaya' => (clone $baseQuery)->where('classification', 'berbahaya')->count(),
            'llm' => (clone $baseQuery)->where('analysis_source', 'llm')->count(),
            'rule_based' => (clone $baseQuery)->where('analysis_source', 'rule_based')->count(),
        ];

        $eventsQuery = FimAnalysisResult::whereDate('analysis_date', $date);

        if ($classification) {
            $eventsQuery->where('classification', $classification);
        }

        $events = $eventsQuery
            ->orderByDesc('risk_score')
            ->orderByDesc('event_timestamp')
            ->paginate(10)
            ->withQueryString();

        return view('fim-dashboard', compact(
            'date',
            'classification',
            'summary',
            'events'
        ));
    }
}