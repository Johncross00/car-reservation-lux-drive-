<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Http\Requests\UpdateReservationRequest;
use App\Models\Reservation;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ReservationController extends Controller
{
    /**
     * Display a listing of the user's reservations.
     */
    public function index(Request $request): Response
    {
        $reservations = $request->user()
            ->reservations()
            ->with('vehicle')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Vérifier et mettre à jour les statuts des réservations affichées
        foreach ($reservations->items() as $reservation) {
            $reservation->checkAndUpdateStatus();
        }

        return Inertia::render('Reservations/Index', [
            'reservations' => $reservations,
        ]);
    }

    /**
     * Show the form for creating a new reservation.
     */
    public function create(Request $request): Response
    {
        $vehicle = null;
        if ($request->has('vehicle_id')) {
            $vehicle = Vehicle::findOrFail($request->vehicle_id);
        }

        $vehicles = Vehicle::where('is_available', true)
            ->orderBy('brand')
            ->orderBy('model')
            ->get();

        return Inertia::render('Reservations/Create', [
            'vehicle' => $vehicle,
            'vehicles' => $vehicles,
        ]);
    }

    /**
     * Store a newly created reservation in storage.
     */
    public function store(StoreReservationRequest $request): RedirectResponse
    {
        $reservation = $request->user()->reservations()->create($request->validated());

        return to_route('reservations.show', $reservation)
            ->with('success', 'Réservation créée avec succès.');
    }

    /**
     * Display the specified reservation.
     */
    public function show(Reservation $reservation): Response
    {
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        // Vérifier et mettre à jour le statut si nécessaire
        $reservation->checkAndUpdateStatus();
        $reservation->load('vehicle');

        return Inertia::render('Reservations/Show', [
            'reservation' => $reservation,
        ]);
    }

    /**
     * Show the form for editing the specified reservation.
     */
    public function edit(Reservation $reservation): Response
    {
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        $reservation->load('vehicle');

        return Inertia::render('Reservations/Edit', [
            'reservation' => $reservation,
        ]);
    }

    /**
     * Update the specified reservation in storage.
     */
    public function update(UpdateReservationRequest $request, Reservation $reservation): RedirectResponse
    {
        $reservation->update($request->validated());

        return to_route('reservations.show', $reservation)
            ->with('success', 'Réservation mise à jour avec succès.');
    }

    /**
     * Remove the specified reservation from storage.
     */
    public function destroy(Reservation $reservation): RedirectResponse
    {
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        $reservation->delete();

        return to_route('reservations.index')
            ->with('success', 'Réservation annulée avec succès.');
    }
}
