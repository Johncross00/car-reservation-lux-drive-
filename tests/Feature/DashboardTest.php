<?php

use App\Models\Reservation;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

it('displays correct reservation and vehicle statistics', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    // Create some vehicles
    Vehicle::factory(5)->create();
    Vehicle::factory()->create(['is_available' => false]);

    // Create some reservations for the user
    Reservation::factory()->for($user)->create(['status' => 'pending', 'start_date' => now()->addDay()]);
    Reservation::factory()->for($user)->create(['status' => 'confirmed', 'start_date' => now()->subDay(), 'end_date' => now()->addDay()]);
    Reservation::factory()->for($user)->create(['status' => 'finished', 'start_date' => now()->subDays(2), 'end_date' => now()->subDay()]);
    Reservation::factory()->for($user)->create(['status' => 'refused']);
    Reservation::factory()->for($user)->create(['status' => 'cancelled']);

    // Create some reservations for another user
    Reservation::factory(2)->create();

    $response = $this->get(route('dashboard'));

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->where('stats.total_reservations', 5)
        ->where('stats.pending_reservations', 1)
        ->where('stats.confirmed_reservations', 1)
        ->where('stats.finished_reservations', 1)
        ->where('stats.refused_reservations', 1)
        ->where('stats.cancelled_reservations', 1)
        ->where('stats.upcoming_reservations', 1)
        ->where('stats.current_reservations', 1)
        ->where('stats.total_vehicles', 6)
        ->where('stats.available_vehicles', 5)
    );
});