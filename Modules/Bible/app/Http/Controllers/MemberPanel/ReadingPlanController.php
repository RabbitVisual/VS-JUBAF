<?php

namespace Modules\Bible\App\Http\Controllers\MemberPanel;

use App\Http\Controllers\Controller;
use App\Services\PdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Bible\App\Models\BiblePlan;
use Modules\Bible\App\Models\BiblePlanSubscription;
use Modules\Bible\App\Services\ReadingCatchUpService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReadingPlanController extends Controller
{
    public function __construct(
        protected PdfService $pdfService,
        protected ReadingCatchUpService $catchUpService
    ) {}

    // The "Dashboard" - Shows my active plans
    public function index()
    {
        $user = Auth::user();

        $subscriptions = BiblePlanSubscription::with(['plan', 'progress'])
            ->where('user_id', $user->id)
            ->latest()
            ->get()
            ->filter(fn ($sub) => $sub->plan !== null);

        foreach ($subscriptions as $sub) {
            $total = $sub->plan->days()->count();
            $sub->total_days = $total ?: $sub->plan->duration_days;
            $completed = $sub->progress()->count();
            $sub->percent = $total > 0 ? round(($completed / $total) * 100) : 0;
            $sub->offer_recalculate = $this->catchUpService->shouldOfferRecalculate($sub);
            $this->catchUpService->ensurePrayerRequestForDelayWhenBehind($sub);
        }

        return view('bible::memberpanel.plans.dashboard', compact('subscriptions'));
    }

    // The "Catalog" - Browse new plans
    public function catalog(Request $request)
    {
        // Get plans that user is NOT subscribed to
        $subscribedPlanIds = BiblePlanSubscription::where('user_id', Auth::id())->pluck('plan_id');

        $featuredPlans = BiblePlan::where('is_active', true)
            ->where('is_featured', true)
            ->whereNotIn('id', $subscribedPlanIds)
            ->limit(3)
            ->get();

        $query = BiblePlan::where('is_active', true)
            ->whereNotIn('id', $subscribedPlanIds);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $allPlans = $query->latest()
            ->paginate(12);

        return view('bible::memberpanel.plans.catalog', compact('featuredPlans', 'allPlans'));
    }

    // Preview Plan before subscribing
    public function preview($id)
    {
        // Check if already subscribed to redirect? No, let them see details anyway, or redirect to dashboard.
        if (BiblePlanSubscription::where('user_id', Auth::id())->where('plan_id', $id)->exists()) {
            return redirect()->route('member.bible.plans.index');
        }

        $plan = BiblePlan::withCount('days')->findOrFail($id);

        // Get a sample of days (first 5)
        $sampleDays = $plan->days()->with('contents.book')->orderBy('day_number')->take(5)->get();

        return view('bible::memberpanel.plans.preview', compact('plan', 'sampleDays'));
    }

    // Subscribe to a plan
    public function subscribe(Request $request, $id)
    {
        $plan = BiblePlan::findOrFail($id);

        // Prevent duplicate sub
        if (BiblePlanSubscription::where('user_id', Auth::id())->where('plan_id', $id)->exists()) {
            return redirect()->route('member.bible.plans.index')->with('info', 'Você já está inscrito neste plano.');
        }

        BiblePlanSubscription::create([
            'user_id' => Auth::id(),
            'plan_id' => $plan->id,
            'start_date' => now(),
            'current_day_number' => 1,
        ]);

        return redirect()->route('member.bible.plans.index')->with('success', 'Inscrição realizada! Boa leitura.');
    }

    // Show Plan Details / Resume
    public function show($id)
    {
        $subscription = BiblePlanSubscription::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        return redirect()->route('member.bible.reader', ['subscriptionId' => $subscription->id, 'day' => $subscription->current_day_number]);
    }

    /**
     * Recalculate remaining route: redistribute readings to projected end date (anti-frustration).
     */
    public function recalculate(Request $request, $subscriptionId)
    {
        $subscription = BiblePlanSubscription::with('plan')
            ->where('user_id', Auth::id())
            ->where('id', $subscriptionId)
            ->firstOrFail();

        if (! $this->catchUpService->shouldOfferRecalculate($subscription)) {
            return redirect()->route('member.bible.plans.index')->with('info', 'Recálculo não disponível para este plano.');
        }

        $this->catchUpService->recalculateRemainingRoute($subscription);

        return redirect()->route('member.bible.plans.index')->with('success', 'Rotas recalculadas. A leitura restante foi redistribuída até a data final.');
    }

    public function downloadPdf($id): StreamedResponse
    {
        $plan = null;
        $subscription = BiblePlanSubscription::where('user_id', Auth::id())->where('id', $id)->first();

        if ($subscription) {
            $plan = $subscription->plan;
        } else {
            $plan = BiblePlan::findOrFail($id);
        }

        $days = $plan->days()->with(['contents' => function($q) {
            $q->with('book');
        }])->orderBy('day_number')->get();

        return $this->pdfService->downloadView(
            'bible::memberpanel.plans.pdf',
            compact('plan', 'days'),
            'plano-' . \Illuminate\Support\Str::slug($plan->title) . '.pdf',
            'A4',
            'Portrait',
            [15, 15, 15, 15]
        );
    }
}
