<?php

namespace Modules\Bible\Tests\Unit;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Bible\App\Models\BiblePlan;
use Modules\Bible\App\Models\BiblePlanDay;
use Modules\Bible\App\Models\BiblePlanSubscription;
use Modules\Bible\App\Models\BibleUserBadge;
use Modules\Bible\App\Models\UserReadingLog;
use Modules\Bible\App\Services\BadgeService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BadgeServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private BiblePlan $plan;

    private BiblePlanSubscription $subscription;

    private BadgeService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->plan = BiblePlan::create([
            'title' => 'Plano Teste',
            'slug' => 'plano-teste',
            'type' => 'sequential',
            'duration_days' => 365,
            'is_active' => true,
            'is_church_plan' => false,
        ]);
        for ($n = 1; $n <= 35; $n++) {
            BiblePlanDay::create(['plan_id' => $this->plan->id, 'day_number' => $n]);
        }
        $this->subscription = BiblePlanSubscription::create([
            'user_id' => $this->user->id,
            'plan_id' => $this->plan->id,
            'start_date' => Carbon::today()->subDays(40),
            'projected_end_date' => Carbon::today()->addDays(325),
            'current_day_number' => 1,
        ]);
        $this->service = app(BadgeService::class);
    }

    #[Test]
    public function it_awards_bereano_da_semana_when_7_consecutive_days_on_time(): void
    {
        $start = Carbon::parse($this->subscription->start_date);
        for ($dayNum = 1; $dayNum <= 7; $dayNum++) {
            $expectedDate = $start->copy()->addDays($dayNum - 1);
            $day = BiblePlanDay::where('plan_id', $this->plan->id)->where('day_number', $dayNum)->first();
            UserReadingLog::create([
                'user_id' => $this->user->id,
                'subscription_id' => $this->subscription->id,
                'plan_day_id' => $day->id,
                'day_number' => $dayNum,
                'completed_at' => $expectedDate,
            ]);
        }
        $day7 = BiblePlanDay::where('plan_id', $this->plan->id)->where('day_number', 7)->first();
        $this->service->evaluateAfterCompletion($this->subscription, $day7);

        $this->assertDatabaseHas('bible_user_badges', [
            'user_id' => $this->user->id,
            'badge_key' => BibleUserBadge::BADGE_BEREANO_SEMANA,
            'subscription_id' => $this->subscription->id,
        ]);
    }

    #[Test]
    public function it_awards_fiel_ao_pacto_at_30_days_and_does_not_duplicate(): void
    {
        for ($dayNum = 1; $dayNum <= 30; $dayNum++) {
            $day = BiblePlanDay::where('plan_id', $this->plan->id)->where('day_number', $dayNum)->first();
            UserReadingLog::create([
                'user_id' => $this->user->id,
                'subscription_id' => $this->subscription->id,
                'plan_day_id' => $day->id,
                'day_number' => $dayNum,
                'completed_at' => Carbon::today(),
            ]);
        }
        $day30 = BiblePlanDay::where('plan_id', $this->plan->id)->where('day_number', 30)->first();
        $this->service->evaluateAfterCompletion($this->subscription, $day30);

        $count = BibleUserBadge::where('subscription_id', $this->subscription->id)
            ->where('badge_key', BibleUserBadge::BADGE_FIEL_AO_PACTO)
            ->count();
        $this->assertSame(1, $count);

        $this->service->evaluateAfterCompletion($this->subscription, $day30);
        $count = BibleUserBadge::where('subscription_id', $this->subscription->id)
            ->where('badge_key', BibleUserBadge::BADGE_FIEL_AO_PACTO)
            ->count();
        $this->assertSame(1, $count);
    }

    #[Test]
    public function it_awards_leitor_do_corpo_only_for_church_plan_at_15_days(): void
    {
        $this->plan->update(['is_church_plan' => true]);
        $this->subscription->load('plan');
        for ($dayNum = 1; $dayNum <= 15; $dayNum++) {
            $day = BiblePlanDay::where('plan_id', $this->plan->id)->where('day_number', $dayNum)->first();
            UserReadingLog::create([
                'user_id' => $this->user->id,
                'subscription_id' => $this->subscription->id,
                'plan_day_id' => $day->id,
                'day_number' => $dayNum,
                'completed_at' => Carbon::today(),
            ]);
        }
        $day15 = BiblePlanDay::where('plan_id', $this->plan->id)->where('day_number', 15)->first();
        $this->service->evaluateAfterCompletion($this->subscription, $day15);

        $this->assertDatabaseHas('bible_user_badges', [
            'user_id' => $this->user->id,
            'badge_key' => BibleUserBadge::BADGE_LEITOR_DO_CORPO,
            'subscription_id' => $this->subscription->id,
        ]);
    }

    #[Test]
    public function it_does_not_award_leitor_do_corpo_when_plan_is_not_church_plan(): void
    {
        $this->plan->update(['is_church_plan' => false]);
        $this->subscription->load('plan');
        for ($dayNum = 1; $dayNum <= 15; $dayNum++) {
            $day = BiblePlanDay::where('plan_id', $this->plan->id)->where('day_number', $dayNum)->first();
            UserReadingLog::create([
                'user_id' => $this->user->id,
                'subscription_id' => $this->subscription->id,
                'plan_day_id' => $day->id,
                'day_number' => $dayNum,
                'completed_at' => Carbon::today(),
            ]);
        }
        $day15 = BiblePlanDay::where('plan_id', $this->plan->id)->where('day_number', 15)->first();
        $this->service->evaluateAfterCompletion($this->subscription, $day15);

        $this->assertDatabaseMissing('bible_user_badges', [
            'subscription_id' => $this->subscription->id,
            'badge_key' => BibleUserBadge::BADGE_LEITOR_DO_CORPO,
        ]);
    }

    #[Test]
    public function it_does_not_award_fiel_ao_pacto_before_30_days(): void
    {
        for ($dayNum = 1; $dayNum <= 29; $dayNum++) {
            $day = BiblePlanDay::where('plan_id', $this->plan->id)->where('day_number', $dayNum)->first();
            UserReadingLog::create([
                'user_id' => $this->user->id,
                'subscription_id' => $this->subscription->id,
                'plan_day_id' => $day->id,
                'day_number' => $dayNum,
                'completed_at' => Carbon::today(),
            ]);
        }
        $day29 = BiblePlanDay::where('plan_id', $this->plan->id)->where('day_number', 29)->first();
        $this->service->evaluateAfterCompletion($this->subscription, $day29);

        $this->assertDatabaseMissing('bible_user_badges', [
            'subscription_id' => $this->subscription->id,
            'badge_key' => BibleUserBadge::BADGE_FIEL_AO_PACTO,
        ]);
    }
}
