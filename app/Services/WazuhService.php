<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Throwable;

class WazuhService
{
    public static function criticalLast24h(): array
    {
        return Cache::remember('wazuh_critical_24h', 60, function () {
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
                                ['range' => ['rule.level' => ['gte' => 15]]],
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
    public static function highLast24h(): array
    {
        return Cache::remember('wazuh_high_24h', 60, function () {
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
                                ['range' => ['rule.level' => ['gte' => 12,'lte' => 14]]],
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
    public static function mediumLast24h(): array
    {
        return Cache::remember('wazuh_medium_24h', 60, function () {
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
                                ['range' => ['rule.level' => ['gte' => 7,'lte' => 11]]],
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

    public static function lowLast24h(): array
    {
        return Cache::remember('wazuh_low_24h', 60, function () {
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
                                ['range' => ['rule.level' => ['gte' => 0,'lte' => 6]]],
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
}
