<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as vehiclesIndex } from '@/routes/vehicles';
import { create as reservationCreate } from '@/routes/reservations';
import { type BreadcrumbItem } from '@/types';

interface Reservation {
    id: number;
    start_date: string;
    end_date: string;
    purpose: string;
    status: string;
}

interface Vehicle {
    id: number;
    brand: string;
    model: string;
    plate_number: string;
    color: string;
    year: number;
    description?: string;
    is_available: boolean;
    reservations: Reservation[];
}

interface Props {
    vehicle: Vehicle;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Véhicules',
        href: vehiclesIndex().url,
    },
    {
        title: `${props.vehicle.brand} ${props.vehicle.model}`,
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
</script>

<template>
    <Head :title="`${vehicle.brand} ${vehicle.model}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">{{ vehicle.brand }} {{ vehicle.model }}</h1>
                    <p class="text-muted-foreground">Détails du véhicule</p>
                </div>
                <Button variant="outline" :href="vehiclesIndex().url" as="a">
                    Retour à la liste
                </Button>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Informations du véhicule</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-2">
                        <div>
                            <span class="text-sm font-medium">Marque:</span>
                            <span class="ml-2">{{ vehicle.brand }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium">Modèle:</span>
                            <span class="ml-2">{{ vehicle.model }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium">Plaque d'immatriculation:</span>
                            <span class="ml-2">{{ vehicle.plate_number }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium">Couleur:</span>
                            <span class="ml-2">{{ vehicle.color }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium">Année:</span>
                            <span class="ml-2">{{ vehicle.year }}</span>
                        </div>
                        <div v-if="vehicle.description">
                            <span class="text-sm font-medium">Description:</span>
                            <p class="mt-1 text-sm text-muted-foreground">{{ vehicle.description }}</p>
                        </div>
                        <div class="mt-4">
                            <span
                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                :class="vehicle.is_available ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'"
                            >
                                {{ vehicle.is_available ? 'Disponible' : 'Indisponible' }}
                            </span>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Réservations à venir</CardTitle>
                        <CardDescription>Réservations confirmées et en attente</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="vehicle.reservations.length === 0" class="text-sm text-muted-foreground">
                            Aucune réservation à venir
                        </div>
                        <div v-else class="space-y-3">
                            <div
                                v-for="reservation in vehicle.reservations"
                                :key="reservation.id"
                                class="rounded-lg border p-3"
                            >
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="text-sm font-medium">{{ reservation.purpose }}</p>
                                        <p class="text-xs text-muted-foreground">
                                            Du {{ formatDate(reservation.start_date) }} au {{ formatDate(reservation.end_date) }}
                                        </p>
                                    </div>
                                    <span
                                        class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                        :class="{
                                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': reservation.status === 'pending',
                                            'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': reservation.status === 'confirmed',
                                        }"
                                    >
                                        {{ reservation.status === 'pending' ? 'En attente' : 'Confirmée' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div v-if="vehicle.is_available" class="flex justify-end">
                <Button :href="reservationCreate().url + '?vehicle_id=' + vehicle.id" as="a" size="lg">
                    Réserver ce véhicule
                </Button>
            </div>
        </div>
    </AppLayout>
</template>
