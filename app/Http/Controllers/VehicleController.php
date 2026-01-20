<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VehicleController extends Controller
{
    /**
     * Display a listing of available vehicles.
     */
    public function index(Request $request): Response
    {
        $vehicles = Vehicle::query()
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('brand', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%")
                        ->orWhere('plate_number', 'like', "%{$search}%");
                });
            })
            ->where('is_available', true)
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Vehicles/Index', [
            'vehicles' => $vehicles,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Display the specified vehicle.
     */
    public function show(Vehicle $vehicle): Response
    {
        $vehicle->load(['reservations' => function ($query) {
            $query->whereIn('status', ['pending', 'confirmed'])
                ->orderBy('start_date');
        }]);

        return Inertia::render('Vehicles/Show', [
            'vehicle' => $vehicle,
        ]);
    }
}
