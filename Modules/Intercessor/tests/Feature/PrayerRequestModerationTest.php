<?php

namespace Modules\Intercessor\Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Modules\Intercessor\App\Models\PrayerCategory;
use Modules\Intercessor\App\Models\PrayerRequest;
use Modules\Intercessor\App\Notifications\PrayerRequestApproved;
use Tests\TestCase;

class PrayerRequestModerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sends_notification_when_prayer_request_is_approved()
    {
        Notification::fake();

        // Setup Admin
        $adminRole = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);

        // Setup User
        $userRole = Role::firstOrCreate(['slug' => 'membro'], ['name' => 'Member']);
        $user = User::factory()->create(['role_id' => $userRole->id]);

        // Setup Category and Request
        $category = PrayerCategory::create(['name' => 'General', 'is_active' => true]);
        $prayerRequest = PrayerRequest::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Test Request',
            'description' => 'Test Description',
            'privacy_level' => 'public',
            'urgency_level' => 'normal',
            'is_anonymous' => false,
            'status' => 'pending',
        ]);

        // Act
        $response = $this->actingAs($admin)
            ->post(route('admin.intercessor.moderation.approve', $prayerRequest));

        // Assert
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('active', $prayerRequest->fresh()->status);

        Notification::assertSentTo(
            $user,
            PrayerRequestApproved::class,
            function ($notification, $channels) use ($prayerRequest) {
                return $notification->request->id === $prayerRequest->id;
            }
        );
    }
}
