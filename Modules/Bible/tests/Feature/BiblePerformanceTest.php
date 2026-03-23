<?php

namespace Modules\Bible\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Modules\Bible\App\Models\BibleVersion;
use Modules\Bible\App\Models\Book;
use Modules\Bible\App\Models\Chapter;
use Modules\Bible\App\Models\Verse;
use Tests\TestCase;
use Modules\Bible\App\Http\Controllers\MemberPanel\BibleController;

class BiblePerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_favorites_query_performance()
    {
        // 1. Setup Data
        $user = User::factory()->create();
        $this->actingAs($user);

        $version = BibleVersion::create([
            'name' => 'Test Version',
            'abbreviation' => 'TV',
            'is_active' => true,
        ]);

        $book = Book::create([
            'bible_version_id' => $version->id,
            'name' => 'Test Book',
            'book_number' => 1,
            'testament' => 'old',
        ]);

        $chapter = Chapter::create([
            'book_id' => $book->id,
            'chapter_number' => 1,
        ]);

        // Create 20 verses
        $verses = [];
        for ($i = 1; $i <= 20; $i++) {
            $verses[] = Verse::create([
                'chapter_id' => $chapter->id,
                'verse_number' => $i,
                'text' => "Verse text $i",
            ]);
        }

        // Attach favorites with color
        foreach ($verses as $index => $verse) {
            DB::table('bible_favorites')->insert([
                'user_id' => $user->id,
                'verse_id' => $verse->id,
                'color' => $index % 2 == 0 ? 'red' : 'blue',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Measure Performance
        DB::enableQueryLog();

        $controller = new BibleController();
        $response = $controller->favorites();

        $log = DB::getQueryLog();
        $queryCount = count($log);

        // Assert based on expectation.
        // Optimized: Expecting ~4 queries (1 main + 3 eager load).
        $this->assertLessThan(10, $queryCount, "Query count should be low after optimization.");

        $favorites = $response->getData()['favorites'];
        $this->assertCount(20, $favorites);
        $this->assertEquals('red', $favorites[0]->favorite_color);
        $this->assertEquals('blue', $favorites[1]->favorite_color);

        // Return query count for the calling code (not possible in PHPUnit test execution flow directly to shell, but printed via echo)
    }
}
