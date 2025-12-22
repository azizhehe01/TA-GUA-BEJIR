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

}
