<?php

namespace Tests\Feature;

use App\Models\Settings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SettingsPerformanceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the performance of settings update.
     */
    public function test_settings_update_performance()
    {
        // specific settings similar to HomePageController
        $settingsMap = [];
        for ($i = 0; $i < 50; $i++) {
            $settingsMap["key_$i"] = ["setting_key_$i", 'string', "Description $i"];
        }

        $validated = [];
        for ($i = 0; $i < 50; $i++) {
            $validated["key_$i"] = "Value $i";
        }

        // Measure queries
        DB::enableQueryLog();

        $settingsToUpdate = [];
        foreach ($settingsMap as $key => $config) {
            $value = $validated[$key] ?? '';
            $settingsToUpdate[] = [
                'key' => $config[0],
                'value' => $value,
                'type' => $config[1],
                'group' => 'homepage',
                'description' => $config[2],
            ];
        }

        Settings::setMany($settingsToUpdate);

        Settings::clearCache();

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        // We expect 1 query for upsert + queries for clearing cache (which might be optimized or not)
        // Settings::clearCache() does pluck('key') -> 1 query.
        // setMany does 1 upsert.
        // total 2 queries ideally.

        dump("Query count: " . $queryCount);

        // Assert that we have a low number of queries
        $this->assertLessThan(5, $queryCount);
    }
}
