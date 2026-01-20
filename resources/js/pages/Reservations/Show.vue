<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';

import ReservationController from '@/actions/App/Http/Controllers/ReservationController';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as reservationsIndex } from '@/routes/reservations';
import { type BreadcrumbItem } from '@/types';

interface Vehicle {
    id: number;
    brand: string;
    model: string;
    plate_number: string;
    color: string;
    year: number;
}

interface Reservation {
    id: number;
    start_date: string;
    end_date: string;
    purpose: string;
    status: string;
    vehicle: Vehicle;
}

interface Props {
    reservation: Reservation;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Réservations',
        href: reservationsIndex().url,
    },
    {
        title: `Réservation #${props.reservation.id}`,
        href: '#',
    },
];

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getStatusLabel = (status: string) => {
    const labels: Record<string, string> = {
        pending: 'En attente',
        confirmed: 'Confirmée',
        finished: 'Terminée',
        refused: 'Refusée',
        cancelled: 'Annulée',
    };
    return labels[status] || status;
};

const getStatusClass = (status: string) => {
    const classes: Record<string, string> = {
        pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
        confirmed: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
        finished: 'bg-slate-200 text-slate-900 dark:bg-slate-800 dark:text-slate-100',
        refused: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
        cancelled: 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
    };
    return classes[status] || '';
};

const deleteReservation = () => {
    if (confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')) {
        router.delete(ReservationController.destroy.url(props.reservation.id));
    }
};
</script>

<template>
    <Head :title="`Réservation #${reservation.id}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Réservation #{{ reservation.id }}</h1>
                    <p class="text-muted-foreground">Détails de la réservation</p>
                </div>
                <Button variant="outline" :href="reservationsIndex().url" as="a">
                    Retour
                </Button>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Informations du véhicule</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-2">
                        <div>
                            <span class="text-sm font-medium">Véhicule:</span>
                            <span class="ml-2">{{ reservation.vehicle.brand }} {{ reservation.vehicle.model }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium">Plaque:</span>
                            <span class="ml-2">{{ reservation.vehicle.plate_number }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium">Couleur:</span>
                            <span class="ml-2">{{ reservation.vehicle.color }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium">Année:</span>
                            <span class="ml-2">{{ reservation.vehicle.year }}</span>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Détails de la réservation</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-2">
                        <div>
                            <span class="text-sm font-medium">Raison:</span>
                            <span class="ml-2">{{ reservation.purpose }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium">Date de début:</span>
                            <span class="ml-2">{{ formatDate(reservation.start_date) }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium">Date de fin:</span>
                            <span class="ml-2">{{ formatDate(reservation.end_date) }}</span>
                        </div>
                        <div class="mt-4">
                            <span
                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                :class="getStatusClass(reservation.status)"
                            >
                                {{ getStatusLabel(reservation.status) }}
                            </span>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div v-if="reservation.status !== 'cancelled'" class="flex justify-end gap-2">
                <Button
                    variant="destructive"
                    @click="deleteReservation"
                >
                    Annuler la réservation
                </Button>
            </div>
        </div>
    </AppLayout>
</template>
