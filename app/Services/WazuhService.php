<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Throwable;

class WazuhService
{
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

}
