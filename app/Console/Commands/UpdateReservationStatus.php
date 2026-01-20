<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Illuminate\Console\Command;

class UpdateReservationStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Met à jour automatiquement les statuts des réservations (pending -> confirmed à la date de début, confirmed -> finished à la date de fin)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = now();

        $confirmedCount = Reservation::where('status', 'pending')
            ->where('start_date', '<=', $now)
            ->update(['status' => 'confirmed']);

        $finishedCount = Reservation::where('status', 'confirmed')
            ->where('end_date', '<', $now)
            ->update(['status' => 'finished']);

        $this->info("Confirmées automatiquement : {$confirmedCount}");
        $this->info("Terminées automatiquement : {$finishedCount}");

        return Command::SUCCESS;
    }
}
