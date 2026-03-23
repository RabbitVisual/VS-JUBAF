<?php

namespace Modules\Worship\Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Modules\Worship\App\Models\AcademyCourse;
use Modules\Worship\App\Models\AcademyModule;
use Modules\Worship\App\Models\AcademyLesson;
use Modules\Worship\App\Models\AcademyProgress;
use Modules\Worship\App\Models\WorshipInstrument;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AcademyProgressTest extends TestCase
{
    use RefreshDatabase;

    public function test_musician_can_view_course_and_complete_lesson()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $instrument = WorshipInstrument::create(['name' => 'Guitar', 'slug' => 'guitar', 'category' => 'harmonia']);

        $course = AcademyCourse::create([
            'title' => 'Guitar Mastery',
            'slug' => 'guitar-mastery-' . uniqid(),
            'instrument_id' => $instrument->id,
            'level' => 'intermediate',
            'description' => 'Become a pro.',
            'status' => 'published',
        ]);

        $module = AcademyModule::create([
            'course_id' => $course->id,
            'title' => 'Module 1',
            'order' => 0,
        ]);

        $lesson = AcademyLesson::create([
            'module_id' => $module->id,
            'title' => 'Lesson 1: Scales',
            'slug' => 'lesson-1',
            'order' => 1,
        ]);

        $response = $this->get(route('worship.member.academy.index'));
        $response->assertStatus(200);
        $response->assertSee('Guitar Mastery');

        $response = $this->get(route('worship.member.academy.classroom', $course->id));
        $response->assertStatus(200);
        $response->assertSee('Academia', false);

        $response = $this->postJson('/api/v1/worship/academy/lessons/' . $lesson->id . '/complete');
        $response->assertStatus(200);
        $response->assertJsonPath('data.progress', 100);

        $this->assertDatabaseHas('worship_academy_progress', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
        ]);

        $response = $this->get(route('worship.member.academy.index'));
        $response->assertStatus(200);
        $response->assertSee('100');
    }
}
