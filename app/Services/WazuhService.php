<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Throwable;

class WazuhService
{
    // untuk menghitung jumlah alert berdasarkan level dengan caching
    private static function countByLevel(
        int $min,
        int $max,
        string $cacheKey
    ): array {
        return Cache::remember($cacheKey, 60, function () use ($min, $max) {
            try {
                $response = Http::withOptions([
                    'verify'  => false,
                    'timeout' => 2,
                ])
                ->withBasicAuth(
                    config('wazuh.username'),
                    config('wazuh.password')
                )
                ->post(config('wazuh.endpoint').'/wazuh-alerts-*/_count', [
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['range' => ['rule.level' => ['gte' => $min, 'lte' => $max]]],
                                ['range' => ['timestamp' => ['gte' => 'now-24h', 'lte' => 'now']]]
                            ]
                        ]
                    ]
                ]);

                return [
                    'ok'    => true,
                    'count' => $response->json()['count'] ?? 0,
                ];
            } catch (Throwable $e) {
                return [
                    'ok'    => false,
                    'count' => null,
                    'error' => 'Wazuh tidak dapat diakses',
                ];
            }
        });
    }

    // untuk menghitung perbandingan jumlah alert 24jam sekarang vs 24jam sebelumnya
    private static function countByLevelBetween(
        int $min,
        int $max,
        string $from,
        string $to
    ): array {
        try {
            $response = Http::withOptions([
                'verify'  => false,
                'timeout' => 2,
            ])
            ->withBasicAuth(
                config('wazuh.username'),
                config('wazuh.password')
            )
            ->post(config('wazuh.endpoint').'/wazuh-alerts-*/_count', [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['range' => ['rule.level' => ['gte' => $min, 'lte' => $max]]],
                            ['range' => ['timestamp' => ['gte' => $from, 'lte' => $to]]]
                        ]
                    ]
                ]
            ]);

            return [
                'ok'    => true,
                'count' => $response->json()['count'] ?? 0,
            ];
        } catch (Throwable $e) {
            return [
                'ok'    => false,
                'count' => null,
            ];
        }
    }

    // untuk mengambil token API Wazuh digunakan untuk login wazuh api
    private static function apiToken(): ?string
    {
        try {
            $response = Http::withOptions([
                'verify'  => false,
                'timeout' => 5,
            ])
            ->withBasicAuth(
                config('wazuh.api_username'),
                config('wazuh.api_password')
            )
            ->post(config('wazuh.api_endpoint') . '/security/user/authenticate');
            
            return $response->json('data.token');
        } catch (Throwable $e) {
            return null;
        }
    }

    public static function alertGrowth24h(): array
    {
        return Cache::remember('wazuh_alert_growth_24h', 60, function () {
        
            $todayRes = self::countByLevelBetween(0, 99, 'now-24h', 'now');
            $yesterdayRes = self::countByLevelBetween(0, 99, 'now-48h', 'now-24h');
        
            if (! $todayRes['ok'] || ! $yesterdayRes['ok']) {
                return [
                    'ok' => false,
                ];
            }
        
            $today = $todayRes['count'];
            $yesterday = $yesterdayRes['count'];
        
            if ($yesterday == 0 && $today > 0) {
                $percent = 100;
            } elseif ($yesterday > 0) {
                $percent = (($today - $yesterday) / $yesterday) * 100;
            } else {
                $percent = 0;
            }
        
            return [
                'ok'        => true,
                'today'     => $today,
                'yesterday' => $yesterday,
                'percent'   => round($percent, 1),
                'up'        => $today >= $yesterday,
            ];
        });
    }
    public static function activeAgents(): array
    {
        return Cache::remember('wazuh_agents_active', 60, function () {

            $token = self::apiToken();

            if (! $token) {
                return [
                    'ok'    => false,
                    'count' => null,
                    'error' => 'Gagal autentikasi Wazuh API',
                ];
            }

            try {
                $response = Http::withOptions([
                    'verify'  => false,
                    'timeout' => 2,
                ])
                ->withHeaders([
                    'Authorization' => "Bearer {$token}",
                ])
                ->get(config('wazuh.api_endpoint') . '/agents', [
                    'status' => 'active',
                    'limit'  => 1000,
                ]);

                return [
                    'ok'    => true,
                    'count' => $response->json()['data']['total_affected_items'] ?? 0,
                ];
            } catch (Throwable $e) {
                return [
                    'ok'    => false,
                    'count' => null,
                    'error' => 'Gagal mengambil data agent aktif',
                ];
            }
        });
    }
    public static function criticalLast24h(): array
    {
        return self::countByLevel(15, 99, 'wazuh_critical_24h');
    }
    
    public static function highLast24h(): array
    {
        return self::countByLevel(12, 14, 'wazuh_high_24h');
    }
    
    public static function mediumLast24h(): array
    {
        return self::countByLevel(7, 11, 'wazuh_medium_24h');
    }
    
    public static function lowLast24h(): array
    {
        return self::countByLevel(0, 6, 'wazuh_low_24h');
    }

    // untuk alert trends dalam 7 hari terakhir
    public static function alertsLast7Days(): array
    {
        return Cache::remember('wazuh_alerts_7days', 300, function () {
        
            try {
                $response = Http::withOptions([
                    'verify'  => false,
                    'timeout' => 3,
                ])
                ->withBasicAuth(
                    config('wazuh.username'),
                    config('wazuh.password')
                )
                ->post(config('wazuh.endpoint') . '/wazuh-alerts-*/_search', [
                    'size' => 0,
                    'query' => [
                        'bool' => [
                            'must' => [
                                [
                                    'range' => [
                                        'timestamp' => [
                                            'gte' => 'now-7d/d',
                                            'lte' => 'now'
                                        ]
                                    ]
                                ],
                                [
                                    'range' => [
                                        'rule.level' => [
                                            'gte' => 0,
                                            'lte' => 99
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'aggs' => [
                        'alerts_per_day' => [
                            'date_histogram' => [
                                'field' => 'timestamp',
                                'calendar_interval' => '1d',
                                'format' => 'EEE',
                                'time_zone' => '+07:00',
                                'min_doc_count' => 0
                            ]
                        ]
                    ]
                ]);
            
                $buckets = $response->json('aggregations.alerts_per_day.buckets') ?? [];
            
                $labels = [];
                $values = [];
            
                foreach ($buckets as $bucket) {
                    $labels[] = $bucket['key_as_string'];
                    $values[] = $bucket['doc_count'];
                }
            
                return [
                    'ok'     => true,
                    'labels' => $labels,
                    'values' => $values,
                ];
            
            } catch (Throwable $e) {
                return [
                    'ok'     => false,
                    'labels' => [],
                    'values' => [],
                    'error'  => 'Gagal mengambil data alert 7 hari'
                ];
            }
        });
    }
}
