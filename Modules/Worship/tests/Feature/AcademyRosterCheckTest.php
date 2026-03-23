<?php

namespace Modules\Worship\Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Modules\Worship\App\Models\WorshipSetlist;
use Modules\Worship\App\Models\WorshipInstrument;
use Modules\Worship\App\Models\AcademyCourse;
use Modules\Worship\App\Models\AcademyModule;
use Modules\Worship\App\Models\AcademyLesson;
use Modules\Worship\App\Models\AcademyProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AcademyRosterCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_musician_can_be_rostered_after_completing_course_lesson()
    {
        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Admin', 'description' => 'Administrator']
        );
        $admin = User::factory()->create(['role_id' => $adminRole->id]);
        $user = User::factory()->create();
        $this->actingAs($admin);

        $instrument = WorshipInstrument::create(['name' => 'Bass', 'slug' => 'bass', 'category' => 'harmonia']);

        $course = AcademyCourse::create([
            'title' => 'Bass Basics',
            'slug' => 'bass-basics-' . uniqid(),
            'instrument_id' => $instrument->id,
            'level' => 'beginner',
            'status' => 'published',
        ]);

        $module = AcademyModule::create([
            'course_id' => $course->id,
            'title' => 'Module 1',
            'order' => 0,
        ]);

        $lesson = AcademyLesson::create([
            'module_id' => $module->id,
            'title' => 'Lesson 1',
            'slug' => 'lesson-1',
            'order' => 1,
        ]);

        $setlist = WorshipSetlist::create([
            'title' => 'Sunday',
            'scheduled_at' => now(),
            'leader_id' => $admin->id,
            'status' => 'draft',
        ]);

        AcademyProgress::create([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'completed_at' => now(),
            'score' => 100,
        ]);

        $response = $this->post(route('worship.admin.rosters.store', $setlist), [
            'user_id' => $user->id,
            'instrument_id' => $instrument->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('worship_rosters', [
            'user_id' => $user->id,
            'setlist_id' => $setlist->id,
        ]);
    }
}
