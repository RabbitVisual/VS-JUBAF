<?php

namespace Modules\Intercessor\App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Modules\Intercessor\App\Models\PrayerCategory;
use Modules\Intercessor\App\Models\PrayerRequest;
use Modules\Intercessor\App\Notifications\NewPrayerRequestNotification;

class PublicPrayerRequestController extends Controller
{
    public function create()
    {
        if (! \Modules\Intercessor\App\Services\IntercessorSettings::get('allow_requests')) {
            abort(404);
        }

        $categories = PrayerCategory::where('is_active', true)->get();

        return view('intercessor::public.request-form', compact('categories'));
    }

    public function store(Request $request)
    {
        if (! \Modules\Intercessor\App\Services\IntercessorSettings::get('allow_requests')) {
            return redirect()->route('public.intercessor.requests.create')
                ->with('error', 'No momento não estamos recebendo novos pedidos de oração.');
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'category_id' => 'required|exists:prayer_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'privacy_level' => 'required|in:public,members_only,intercessors_only,pastoral_only',
            'urgency_level' => 'required|in:normal,high,critical',
        ]);

        $data = [
            'user_id' => null,
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'privacy_level' => $validated['privacy_level'],
            'urgency_level' => $validated['urgency_level'],
            'is_anonymous' => true,
            'show_identity' => false,
            'status' => \Modules\Intercessor\App\Services\IntercessorSettings::get('require_approval') ? 'pending' : 'active',
        ];

        $prayerRequest = PrayerRequest::create($data);

        // Notificar intercessores/pastores sobre novo pedido externo (sempre restrito)
        $recipients = \App\Models\User::whereHas('role', function ($q) {
            $q->whereIn('slug', ['pastor', 'intercessor', 'prayer_team', 'admin']);
        })->get();

        if ($recipients->isNotEmpty() && class_exists(NewPrayerRequestNotification::class)) {
            Notification::send($recipients, new NewPrayerRequestNotification($prayerRequest));
        }

        return redirect()
            ->route('public.intercessor.requests.create')
            ->with('success', 'Seu pedido foi recebido com carinho. Nossa equipe de intercessão irá orar por você.');
    }
}

