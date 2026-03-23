<?php

namespace Modules\Assets\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Assets\App\Models\AssetReservation;

class AssetReservationController extends Controller
{
    public function index(Request $request): View
    {
        $query = AssetReservation::with(['asset', 'ministry', 'requester', 'event'])
            ->latest();

        if ($request->filled('ministry_id')) {
            $query->where('ministry_id', $request->ministry_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reservations = $query->paginate(15);
        $ministries = \Modules\Ministries\App\Models\Ministry::orderBy('name')->get(['id', 'name']);

        return view('assets::admin.reservations.index', compact('reservations', 'ministries'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'ministry_id' => 'required|exists:ministries,id',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after_or_equal:start_at',
            'event_id' => 'nullable|exists:events,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        if (AssetReservation::hasCollision(
            (int) $validated['asset_id'],
            $validated['start_at'],
            $validated['end_at']
        )) {
            return back()
                ->withInput()
                ->with('error', 'Este equipamento já está reservado neste horário. Escolha outro horário ou recurso.');
        }

        AssetReservation::create([
            'asset_id' => $validated['asset_id'],
            'ministry_id' => $validated['ministry_id'],
            'event_id' => $validated['event_id'] ?? null,
            'requested_by' => $request->user()->id,
            'start_at' => $validated['start_at'],
            'end_at' => $validated['end_at'],
            'status' => AssetReservation::STATUS_REQUESTED,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('assets.admin.reservations.index')->with('success', 'Reserva criada com sucesso.');
    }

    public function approve(AssetReservation $reservation): RedirectResponse
    {
        $reservation->update(['status' => AssetReservation::STATUS_APPROVED]);
        return back()->with('success', 'Reserva aprovada.');
    }

    public function deny(Request $request, AssetReservation $reservation): RedirectResponse
    {
        $reservation->update([
            'status' => AssetReservation::STATUS_DENIED,
            'notes' => $reservation->notes . "\n[Admin] " . ($request->input('reason', 'Rejeitado.')),
        ]);
        return back()->with('success', 'Reserva rejeitada.');
    }
}
