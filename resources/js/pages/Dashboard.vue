<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Calendar, Car, CheckCircle, Clock, XCircle, Ban, Hourglass } from 'lucide-vue-next';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { create as reservationCreate, index as reservationsIndex, show as reservationShow } from '@/routes/reservations';
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
    stats: {
        total_reservations: number;
        pending_reservations: number;
        confirmed_reservations: number;
        upcoming_reservations: number;
        current_reservations: number;
        total_vehicles: number;
        available_vehicles: number;
        finished_reservations: number;
        refused_reservations: number;
        cancelled_reservations: number;
    };
    upcoming_reservations: Reservation[];
    current_reservations: Reservation[];
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
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
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div>
                <h1 class="text-2xl font-bold">Tableau de bord</h1>
                <p class="text-muted-foreground">Vue d'ensemble de vos réservations et des véhicules disponibles</p>
            </div>

            <!-- Statistiques -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <Card class="border border-gray-300 hover:shadow-lg hover:shadow-purple-200/50 transition-all duration-300 hover:scale-105 group">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Total réservations</CardTitle>
                        <Calendar class="h-4 w-4 text-purple-500 group-hover:text-purple-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.total_reservations }}</div>
                        <p class="text-xs text-muted-foreground">Toutes vos réservations</p>
                    </CardContent>
                </Card>

                <Card class="border border-gray-300 hover:shadow-lg hover:shadow-yellow-200/50 transition-all duration-300 hover:scale-105 group">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">En attente</CardTitle>
                        <Clock class="h-4 w-4 text-yellow-500 group-hover:text-yellow-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.pending_reservations }}</div>
                        <p class="text-xs text-muted-foreground">Réservations en attente</p>
                    </CardContent>
                </Card>

                <Card class="border border-gray-300 hover:shadow-lg hover:shadow-green-200/50 transition-all duration-300 hover:scale-105 group">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Confirmées</CardTitle>
                        <CheckCircle class="h-4 w-4 text-green-500 group-hover:text-green-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.confirmed_reservations }}</div>
                        <p class="text-xs text-muted-foreground">Réservations confirmées</p>
                    </CardContent>
                </Card>

                <Card class="border border-gray-300 hover:shadow-lg hover:shadow-gray-200/50 transition-all duration-300 hover:scale-105 group">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Terminées</CardTitle>
                        <Hourglass class="h-4 w-4 text-gray-500 group-hover:text-gray-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.finished_reservations }}</div>
                        <p class="text-xs text-muted-foreground">Réservations terminées</p>
                    </CardContent>
                </Card>

                <Card class="border border-gray-300 hover:shadow-lg hover:shadow-red-200/50 transition-all duration-300 hover:scale-105 group">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Refusées</CardTitle>
                        <Ban class="h-4 w-4 text-red-500 group-hover:text-red-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.refused_reservations }}</div>
                        <p class="text-xs text-muted-foreground">Réservations refusées</p>
                    </CardContent>
                </Card>

                <Card class="border border-gray-300 hover:shadow-lg hover:shadow-orange-200/50 transition-all duration-300 hover:scale-105 group">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">\
                        <CardTitle class="text-sm font-medium">Annulées</CardTitle>
                        <XCircle class="h-4 w-4 text-orange-500 group-hover:text-orange-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.cancelled_reservations }}</div>
                        <p class="text-xs text-muted-foreground">Réservations annulées</p>
                    </CardContent>
                </Card>

                <Card class="border border-gray-300 hover:shadow-lg hover:shadow-blue-200/50 transition-all duration-300 hover:scale-105 group">
                    <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                        <CardTitle class="text-sm font-medium">Véhicules disponibles</CardTitle>
                        <Car class="h-4 w-4 text-blue-500 group-hover:text-blue-600" />
                    </CardHeader>
                    <CardContent>
                        <div class="text-2xl font-bold">{{ stats.available_vehicles }}</div>
                        <p class="text-xs text-muted-foreground">sur {{ stats.total_vehicles }} véhicules</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Réservations en cours et à venir -->
            <div class="grid gap-4 md:grid-cols-2">
                <!-- Réservations en cours -->
                <Card class="border border-gray-300 hover:shadow-lg hover:shadow-green-200/50 transition-all duration-300 hover:scale-105 group">
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <div>
                                <CardTitle>Réservations en cours</CardTitle>
                                <CardDescription>Vos réservations actives</CardDescription>
                            </div>
                            <span class="text-2xl font-bold text-green-600">{{ stats.current_reservations }}</span>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div v-if="current_reservations.length === 0" class="text-center py-8 text-muted-foreground">
                            <p>Aucune réservation en cours</p>
                        </div>
                        <div v-else class="space-y-3">
                            <div
                                v-for="reservation in current_reservations"
                                :key="reservation.id"
                                class="flex items-center justify-between rounded-lg border p-3 hover:bg-accent/50 transition-colors"
                            >
                                <div class="flex-1">
                                    <p class="font-medium">{{ reservation.vehicle.brand }} {{ reservation.vehicle.model }}</p>
                                    <p class="text-sm text-muted-foreground">{{ reservation.purpose }}</p>
                                    <p class="text-xs text-muted-foreground mt-1">
                                        Jusqu'au {{ formatDate(reservation.end_date) }}
                                    </p>
                                </div>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    :href="reservationShow(reservation.id).url"
                                    as="a"
                                >
                                    Voir
                                </Button>
                            </div>
                        </div>
                        <div v-if="current_reservations.length > 0" class="mt-4">
                            <Button variant="outline" class="w-full" :href="reservationsIndex().url" as="a">
                                Voir toutes les réservations
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <!-- Réservations à venir -->
                <Card class="border border-gray-300 hover:shadow-lg hover:shadow-blue-200/50 transition-all duration-300 hover:scale-105 group">
                    <CardHeader>
                        <div class="flex items-center justify-between">
                            <div>
                                <CardTitle>Réservations à venir</CardTitle>
                                <CardDescription>Vos prochaines réservations</CardDescription>
                            </div>
                            <span class="text-2xl font-bold text-blue-600">{{ stats.upcoming_reservations }}</span>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div v-if="upcoming_reservations.length === 0" class="text-center py-8 text-muted-foreground">
                            <p>Aucune réservation à venir</p>
                            <Button variant="outline" class="mt-4" :href="reservationCreate().url" as="a">
                                Créer une réservation
                            </Button>
                        </div>
                        <div v-else class="space-y-3">
                            <div
                                v-for="reservation in upcoming_reservations"
                                :key="reservation.id"
                                class="flex items-center justify-between rounded-lg border p-3 hover:bg-accent/50 transition-colors"
                            >
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <p class="font-medium">{{ reservation.vehicle.brand }} {{ reservation.vehicle.model }}</p>
                                        <span
                                            class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium"
                                            :class="getStatusClass(reservation.status)"
                                        >
                                            {{ getStatusLabel(reservation.status) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-muted-foreground">{{ reservation.purpose }}</p>
                                    <p class="text-xs text-muted-foreground mt-1">
                                        Du {{ formatDate(reservation.start_date) }} au {{ formatDate(reservation.end_date) }}
                                    </p>
                                </div>
                                <Button
                                    variant="outline"
                                    size="sm"
                                    :href="reservationShow(reservation.id).url"
                                    as="a"
                                >
                                    Voir
                                </Button>
                            </div>
                        </div>
                        <div v-if="upcoming_reservations.length > 0" class="mt-4">
                            <Button variant="outline" class="w-full" :href="reservationsIndex().url" as="a">
                                Voir toutes les réservations
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Actions rapides -->
            <Card class="border border-gray-300 hover:shadow-lg hover:shadow-gray-200/50 transition-all duration-300 group">
                <CardHeader>
                    <CardTitle>Actions rapides</CardTitle>
                    <CardDescription>Accès rapide aux fonctionnalités principales</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="flex flex-wrap gap-2">
                        <Button :href="reservationCreate().url" as="a">
                            Nouvelle réservation
                        </Button>
                        <Button variant="outline" :href="reservationsIndex().url" as="a">
                            Mes réservations
                        </Button>
                        <Button variant="outline" :href="vehiclesIndex().url" as="a">
                            Voir les véhicules
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
