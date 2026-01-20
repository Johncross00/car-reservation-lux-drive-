<?php

use App\Models\User;
use App\Models\Vehicle;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('unauthenticated users cannot view vehicles', function () {
    get(route('vehicles.index'))
        ->assertRedirect(route('login'));
});

test('authenticated users can view vehicles list', function () {
    $user = User::factory()->create();

    Vehicle::factory()->count(5)->create();

    actingAs($user)
        ->get(route('vehicles.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Vehicles/Index')
            ->has('vehicles.data', 5)
        );
});

test('authenticated users can search vehicles', function () {
    $user = User::factory()->create();

    Vehicle::factory()->create([
        'brand' => 'Toyota',
        'model' => 'Corolla',
        'plate_number' => 'AB-123-CD',
    ]);

    Vehicle::factory()->create([
        'brand' => 'Ford',
        'model' => 'Focus',
        'plate_number' => 'EF-456-GH',
    ]);

    actingAs($user)
        ->get(route('vehicles.index', ['search' => 'Toyota']))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Vehicles/Index')
            ->has('vehicles.data', 1)
            ->where('vehicles.data.0.brand', 'Toyota')
        );
});

test('only available vehicles are shown', function () {
    $user = User::factory()->create();

    Vehicle::factory()->create(['is_available' => true]);
    Vehicle::factory()->create(['is_available' => false]);

    actingAs($user)
        ->get(route('vehicles.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Vehicles/Index')
            ->has('vehicles.data', 1)
            ->where('vehicles.data.0.is_available', true)
        );
});

test('authenticated users can view vehicle details', function () {
    $user = User::factory()->create();
    $vehicle = Vehicle::factory()->create();

    actingAs($user)
        ->get(route('vehicles.show', $vehicle))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Vehicles/Show')
            ->where('vehicle.id', $vehicle->id)
        );
});
