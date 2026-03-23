<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use Modules\HomePage\App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Modules\Admin\App\Jobs\SendNewsletterJob;

class NewsletterPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup Admin User
        $role = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);
        $this->adminUser = User::factory()->create(['role_id' => $role->id]);
    }

    public function test_benchmark_synchronous_sending()
    {
        // This test was the baseline. Now with the code change, it should be very fast
        // because it just dispatches a job (or pushes to fake queue if we faked it).
        // But here we want to see the integration speed.

        // Seed 500 subscribers
        $subscribers = [];
        for ($i = 0; $i < 500; $i++) {
            $subscribers[] = [
                'email' => "subscriber{$i}@example.com",
                'name' => "Subscriber {$i}",
                'is_active' => true,
                'is_confirmed' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        NewsletterSubscriber::insert($subscribers);

        // We use Queue::fake() to ensure we are testing the Controller's dispatch speed,
        // effectively simulating an async queue driver.
        Queue::fake();

        $start = microtime(true);

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.homepage.newsletter.send'), [
                'subject' => 'Test Subject',
                'content' => 'Test Content'
            ]);

        $duration = (microtime(true) - $start) * 1000; // ms

        $response->assertRedirect();

        echo "\nExecution time for 500 subscribers (Queued): {$duration} ms\n";
    }

    public function test_newsletter_sending_is_queued()
    {
        Queue::fake();

        $response = $this->actingAs($this->adminUser)
            ->post(route('admin.homepage.newsletter.send'), [
                'subject' => 'Test Subject',
                'content' => 'Test Content'
            ]);

        $response->assertRedirect();

        Queue::assertPushed(SendNewsletterJob::class);
    }

    public function test_job_sends_emails()
    {
        Mail::fake();

        // Create subscribers
        $subscribers = [];
        for ($i = 0; $i < 10; $i++) {
            $subscribers[] = [
                'email' => "subscriber_job_{$i}@example.com",
                'name' => "Subscriber Job {$i}",
                'is_active' => true,
                'is_confirmed' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        NewsletterSubscriber::insert($subscribers);

        $job = new SendNewsletterJob('Subject', 'Content');
        $job->handle();

        Mail::assertQueued(\App\Mail\NewsletterSubscriptionMail::class, 10);
    }
}
