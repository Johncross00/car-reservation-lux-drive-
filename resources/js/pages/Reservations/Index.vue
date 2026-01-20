<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { create as reservationCreate } from '@/routes/reservations';
import { show as reservationShow } from '@/routes/reservations';
import { index as vehiclesIndex } from '@/routes/vehicles';
import { type BreadcrumbItem } from '@/types';

interface Vehicle {
    id: number;
    brand: string;
    model: string;
    plate_number: string;
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
    reservations: {
        data: Reservation[];
        links: any[];
        current_page: number;
        last_page: number;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Mes réservations',
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
</script>

<template>
    <Head title="Mes réservations" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Mes réservations</h1>
                    <p class="text-muted-foreground">Gérez vos réservations de véhicules</p>
                </div>
                <div class="flex gap-2">
                    <Button variant="outline" :href="vehiclesIndex().url" as="a">
                        Voir les véhicules
                    </Button>
                    <Button :href="reservationCreate().url" as="a">
                        Nouvelle réservation
                    </Button>
                </div>
            </div>

            <div v-if="reservations.data.length === 0" class="flex flex-col items-center justify-center py-12">
                <p class="text-muted-foreground mb-4">Aucune réservation</p>
                <Button :href="reservationCreate().url" as="a">
                    Créer une réservation
                </Button>
            </div>

            <div v-else class="grid gap-4">
                <Card v-for="reservation in reservations.data" :key="reservation.id">
                    <CardHeader>
                        <div class="flex items-start justify-between">
                            <div>
                                <CardTitle>{{ reservation.vehicle.brand }} {{ reservation.vehicle.model }}</CardTitle>
                                <CardDescription>{{ reservation.vehicle.plate_number }}</CardDescription>
                            </div>
                            <span
                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                :class="getStatusClass(reservation.status)"
                            >
                                {{ getStatusLabel(reservation.status) }}
                            </span>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-2">
                            <div>
                                <span class="text-sm font-medium">Raison:</span>
                                <span class="ml-2">{{ reservation.purpose }}</span>
                            </div>
                            <div>
                                <span class="text-sm font-medium">Du:</span>
                                <span class="ml-2">{{ formatDate(reservation.start_date) }}</span>
                            </div>
                            <div>
                                <span class="text-sm font-medium">Au:</span>
                                <span class="ml-2">{{ formatDate(reservation.end_date) }}</span>
                            </div>
                        </div>
                    </CardContent>
                    <CardFooter>
                        <Button
                            variant="outline"
                            :href="reservationShow(reservation.id).url"
                            as="a"
                            class="w-full"
                        >
                            Voir les détails
                        </Button>
                    </CardFooter>
                </Card>
            </div>

            <div v-if="reservations.last_page > 1" class="flex justify-center gap-2">
                <Button
                    v-for="link in reservations.links"
                    :key="link.label"
                    variant="outline"
                    :disabled="!link.url || link.active"
                    :href="link.url || '#'"
                    as="a"
                    v-html="link.label"
                />
            </div>
        </div>
    </AppLayout>
</template>
