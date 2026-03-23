<?php

namespace Modules\Marketplace\Services;

use App\Models\Settings;
use Illuminate\Support\Facades\Cache;

class MarketplacePolicyService
{
    public const CACHE_KEY = 'marketplace_policies';
    public const CACHE_TTL = 86400; // 24 hours

    public static function getCached(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return [
                'delivery' => Settings::get('marketplace_policy_delivery', '') ?: '',
                'returns' => Settings::get('marketplace_policy_returns', '') ?: '',
                'terms' => Settings::get('marketplace_policy_terms', '') ?: '',
            ];
        });
    }

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
