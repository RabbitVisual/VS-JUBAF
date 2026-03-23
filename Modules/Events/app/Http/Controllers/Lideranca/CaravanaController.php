<?php

namespace Modules\Events\App\Http\Controllers\Lideranca;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Events\App\Models\Event;
use Modules\Events\App\Models\EventRegistration;

class CaravanaController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $events = Event::query()
            ->whereHas('registrations.user', function ($query) use ($user) {
                $query->where('igreja_id', $user->igreja_id);
            })
            ->withCount([
                'registrations as caravan_registrations_count' => function ($query) use ($user) {
                    $query->whereHas('user', function ($subQuery) use ($user) {
                        $subQuery->where('igreja_id', $user->igreja_id);
                    });
                },
            ])
            ->orderBy('start_date', 'desc')
            ->paginate(12);

        return view('events::liderancapanel.caravanas.index', compact('events'));
    }

    public function show(Request $request, Event $event): View
    {
        $user = auth()->user();
        $sort = $request->string('sort', 'recent')->toString();
        $status = $request->string('status', 'all')->toString();
        $search = $request->string('search', '')->toString();

        $registrationsQuery = EventRegistration::query()
            ->where('event_id', $event->id)
            ->whereHas('user', function ($query) use ($user) {
                $query->where('igreja_id', $user->igreja_id);
            })
            ->with([
                'user:id,name,cellphone,phone,igreja_id',
                'batch:id,name',
                'participants:id,registration_id,name',
                'latestPayment',
            ]);

        if ($search !== '') {
            $registrationsQuery->whereHas('user', function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('whatsapp', 'like', '%'.$search.'%');
            });
        }

        if ($status === 'paid') {
            $registrationsQuery->where(function ($query) {
                $query->where('status', EventRegistration::STATUS_CONFIRMED)
                    ->orWhereHas('latestPayment', function ($paymentQuery) {
                        $paymentQuery->whereIn('status', ['paid', 'approved', 'completed']);
                    });
            });
        } elseif ($status === 'pending') {
            $registrationsQuery->where(function ($query) {
                $query->where('status', EventRegistration::STATUS_PENDING)
                    ->whereDoesntHave('latestPayment', function ($paymentQuery) {
                        $paymentQuery->whereIn('status', ['paid', 'approved', 'completed']);
                    });
            });
        }

        if ($sort === 'name') {
            $registrationsQuery->join('users', 'users.id', '=', 'event_registrations.user_id')
                ->orderBy('users.name')
                ->select('event_registrations.*');
        } else {
            $registrationsQuery->orderByDesc('created_at');
        }

        $totalNaCaravana = (clone $registrationsQuery)->count();
        $totalPago = (clone $registrationsQuery)
            ->where(function ($query) {
                $query->where('status', EventRegistration::STATUS_CONFIRMED)
                    ->orWhereHas('latestPayment', function ($paymentQuery) {
                        $paymentQuery->whereIn('status', ['paid', 'approved', 'completed']);
                    });
            })
            ->count();

        $registrations = $registrationsQuery
            ->paginate(20);

        return view('events::liderancapanel.caravanas.show', compact(
            'event',
            'registrations',
            'totalNaCaravana',
            'totalPago',
            'sort',
            'status',
            'search'
        ));
    }
}
