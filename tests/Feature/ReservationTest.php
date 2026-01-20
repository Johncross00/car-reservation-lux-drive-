<?php

use App\Models\Reservation;
use App\Models\User;
use App\Models\Vehicle;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

test('unauthenticated users cannot view reservations', function () {
    get(route('reservations.index'))
        ->assertRedirect(route('login'));
});

test('authenticated users can view their reservations', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Reservation::factory()->count(3)->create(['user_id' => $user->id]);
    Reservation::factory()->count(2)->create(['user_id' => $otherUser->id]);

    actingAs($user)
        ->get(route('reservations.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Reservations/Index')
            ->has('reservations.data', 3)
        );
});

test('authenticated users can create a reservation', function () {
    $user = User::factory()->create();
    $vehicle = Vehicle::factory()->create(['is_available' => true]);

    $startDate = now()->addDay();
    $endDate = now()->addDays(2);

    actingAs($user)
        ->post(route('reservations.store'), [
            'vehicle_id' => $vehicle->id,
            'start_date' => $startDate->format('Y-m-d H:i:s'),
            'end_date' => $endDate->format('Y-m-d H:i:s'),
            'purpose' => 'Déplacement client',
        ])
        ->assertRedirect();

    expect(Reservation::where('user_id', $user->id)->count())->toBe(1);
    expect(Reservation::where('vehicle_id', $vehicle->id)->first())
        ->purpose->toBe('Déplacement client')
        ->status->toBe('pending');
});

test('cannot create reservation for unavailable vehicle', function () {
    $user = User::factory()->create();
    $vehicle = Vehicle::factory()->create(['is_available' => false]);

    actingAs($user)
        ->post(route('reservations.store'), [
            'vehicle_id' => $vehicle->id,
            'start_date' => now()->addDay()->format('Y-m-d H:i:s'),
            'end_date' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'purpose' => 'Déplacement client',
        ])
        ->assertSessionHasErrors('vehicle_id');
});

test('cannot create overlapping reservations', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $vehicle = Vehicle::factory()->create(['is_available' => true]);

    Reservation::factory()->create([
        'vehicle_id' => $vehicle->id,
        'user_id' => $otherUser->id,
        'start_date' => now()->addDay(),
        'end_date' => now()->addDays(3),
        'status' => 'confirmed',
    ]);

    actingAs($user)
        ->post(route('reservations.store'), [
            'vehicle_id' => $vehicle->id,
            'start_date' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'end_date' => now()->addDays(4)->format('Y-m-d H:i:s'),
            'purpose' => 'Déplacement client',
        ])
        ->assertSessionHasErrors('vehicle_id');
});

test('cannot create reservation with end date before start date', function () {
    $user = User::factory()->create();
    $vehicle = Vehicle::factory()->create(['is_available' => true]);

    actingAs($user)
        ->post(route('reservations.store'), [
            'vehicle_id' => $vehicle->id,
            'start_date' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'end_date' => now()->addDay()->format('Y-m-d H:i:s'),
            'purpose' => 'Déplacement client',
        ])
        ->assertSessionHasErrors('end_date');
});

test('cannot create reservation with start date in the past', function () {
    $user = User::factory()->create();
    $vehicle = Vehicle::factory()->create(['is_available' => true]);

    actingAs($user)
        ->post(route('reservations.store'), [
            'vehicle_id' => $vehicle->id,
            'start_date' => now()->subDay()->format('Y-m-d H:i:s'),
            'end_date' => now()->addDay()->format('Y-m-d H:i:s'),
            'purpose' => 'Déplacement client',
        ])
        ->assertSessionHasErrors('start_date');
});

test('authenticated users can view their reservation details', function () {
    $user = User::factory()->create();
    $reservation = Reservation::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->get(route('reservations.show', $reservation))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Reservations/Show')
            ->where('reservation.id', $reservation->id)
        );
});

test('users cannot view other users reservations', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $reservation = Reservation::factory()->create(['user_id' => $otherUser->id]);

    actingAs($user)
        ->get(route('reservations.show', $reservation))
        ->assertForbidden();
});

test('authenticated users can update their reservation', function () {
    $user = User::factory()->create();
    $vehicle = Vehicle::factory()->create(['is_available' => true]);
    $reservation = Reservation::factory()->create([
        'user_id' => $user->id,
        'vehicle_id' => $vehicle->id,
        'start_date' => now()->addDays(5),
        'end_date' => now()->addDays(7),
    ]);

    $newStartDate = now()->addDays(10);
    $newEndDate = now()->addDays(12);

    actingAs($user)
        ->put(route('reservations.update', $reservation), [
            'start_date' => $newStartDate->format('Y-m-d H:i:s'),
            'end_date' => $newEndDate->format('Y-m-d H:i:s'),
            'purpose' => 'Nouvelle raison',
        ])
        ->assertRedirect();

    $reservation->refresh();
    expect($reservation->purpose)->toBe('Nouvelle raison');
});

test('users cannot update other users reservations', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $reservation = Reservation::factory()->create(['user_id' => $otherUser->id]);

    actingAs($user)
        ->put(route('reservations.update', $reservation), [
            'purpose' => 'Hacked',
        ])
        ->assertForbidden();
});

test('authenticated users can delete their reservation', function () {
    $user = User::factory()->create();
    $reservation = Reservation::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->delete(route('reservations.destroy', $reservation))
        ->assertRedirect();

    expect(Reservation::find($reservation->id))->toBeNull();
});

test('users cannot delete other users reservations', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $reservation = Reservation::factory()->create(['user_id' => $otherUser->id]);

    actingAs($user)
        ->delete(route('reservations.destroy', $reservation))
        ->assertForbidden();
});
