<?php

namespace Modules\Worship\Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Modules\Worship\App\Models\WorshipSetlist;
use Modules\Worship\App\Models\WorshipSong;
use Modules\Worship\App\Models\WorshipSetlistItem;
use Modules\Worship\App\Enums\MusicalKey;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StageViewTest extends TestCase
{
    // We use DatabaseTransactions or RefreshDatabase depending on setup.
    // Since I'm using sqlite file I just created, RefreshDatabase is good.
    use RefreshDatabase;

    public function test_stage_view_loads_and_transposes_correctly()
    {
        // 1. Create User and Login
        $user = User::factory()->create();
        $this->actingAs($user);

        // 2. Create Song in G
        $song = WorshipSong::create([
            'title' => 'Test Song',
            'artist' => 'Test Artist',
            'bpm' => 120,
            'time_signature' => '4/4',
            'original_key' => MusicalKey::G,
            'content_chordpro' => '[G]Hello [C]World',
        ]);

        // 3. Create Setlist
        $setlist = WorshipSetlist::create([
            'title' => 'Sunday Service',
            'scheduled_at' => now(),
            'leader_id' => $user->id,
            'status' => 'draft' // Ensure status allows viewing if there's restriction (usually there isn't for leader)
        ]);

        // 4. Create Item with Override Key A (G -> A = +2 semitones)
        WorshipSetlistItem::create([
            'setlist_id' => $setlist->id,
            'song_id' => $song->id,
            'order' => 1,
            'override_key' => MusicalKey::A,
        ]);

        // 5. Hit Route
        $response = $this->get(route('worship.member.stage.view', $setlist));

        // 6. Assertions
        $response->assertStatus(200);
        $response->assertSee('Sunday Service'); // Setlist title
        $response->assertSee('Test Song'); // Song title

        // Check for Transposed Chords in HTML
        // [G] -> [A]
        // [C] -> [D]
        // The ChordProEngine renders chords in <span class="chord">...</span>
        // So we expect <span class="chord">A</span> and <span class="chord">D</span>
        $response->assertSee('<span class="chord">A</span>', false);
        $response->assertSee('<span class="chord">D</span>', false);

        // Should NOT see original chords
        $response->assertDontSee('<span class="chord">G</span>', false);
        $response->assertDontSee('<span class="chord">C</span>', false);

        // Check Alpine Data
        // x-data="worshipStageViewer(120, ...)"
        // We verify that the setlist items JSON contains key: 'A'
        // Blade {{ }} escapes output, so quotes become &quot;
        $response->assertSee('&quot;key&quot;:&quot;A&quot;', false);
    }
}
