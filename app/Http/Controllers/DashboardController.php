<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $now = now();

        // Statistiques des réservations de l'utilisateur
        $totalReservations = $user->reservations()->count();
        $pendingReservations = $user->reservations()->where('status', 'pending')->count();
        $confirmedReservations = $user->reservations()->where('status', 'confirmed')->count();
        $finishedReservations = $user->reservations()->where('status', 'finished')->count();
        $refusedReservations = $user->reservations()->where('status', 'refused')->count();
        $cancelledReservations = $user->reservations()->where('status', 'cancelled')->count();
        $upcomingReservations = $user->reservations()
            ->where('start_date', '>', $now)
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();
        $currentReservations = $user->reservations()
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where('status', 'confirmed')
            ->count();

        // Réservations à venir (prochaines 5)
        $upcomingReservationsList = $user->reservations()
            ->with('vehicle')
            ->where('start_date', '>', $now)
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('start_date')
            ->limit(5)
            ->get()
            ->map(function ($reservation) {
                $reservation->checkAndUpdateStatus();
                return $reservation;
            });

        // Réservations en cours
        $currentReservationsList = $user->reservations()
            ->with('vehicle')
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where('status', 'confirmed')
            ->orderBy('end_date')
            ->limit(5)
            ->get();

        // Statistiques des véhicules
        $totalVehicles = Vehicle::count();
        $availableVehicles = Vehicle::where('is_available', true)->count();

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_reservations' => $totalReservations,
                'pending_reservations' => $pendingReservations,
                'confirmed_reservations' => $confirmedReservations,
                'upcoming_reservations' => $upcomingReservations,
                'current_reservations' => $currentReservations,
                'finished_reservations' => $finishedReservations,
                'refused_reservations' => $refusedReservations,
                'cancelled_reservations' => $cancelledReservations,
                'total_vehicles' => $totalVehicles,
                'available_vehicles' => $availableVehicles,
            ],
            'upcoming_reservations' => $upcomingReservationsList,
            'current_reservations' => $currentReservationsList,
        ]);
    }
}
