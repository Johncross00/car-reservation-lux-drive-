<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';

import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as vehiclesIndex, show as vehicleShow } from '@/routes/vehicles';
import { create as reservationCreate } from '@/routes/reservations';
import { type BreadcrumbItem } from '@/types';

interface Vehicle {
    id: number;
    brand: string;
    model: string;
    plate_number: string;
    color: string;
    year: number;
    description?: string;
    is_available: boolean;
}

interface Props {
    vehicles: {
        data: Vehicle[];
        links: any[];
        current_page: number;
        last_page: number;
    };
    filters: {
        search?: string;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Véhicules',
        href: vehiclesIndex().url,
    },
];

const search = (value: string) => {
    router.get(vehiclesIndex().url, { search: value }, {
        preserveState: true,
        replace: true,
    });
};
</script>

<template>
    <Head title="Véhicules disponibles" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Véhicules disponibles</h1>
                    <p class="text-muted-foreground">Consultez et réservez un véhicule pour vos déplacements professionnels</p>
                </div>
            </div>

            <div class="flex gap-4">
                <div class="flex-1">
                    <Label for="search">Rechercher</Label>
                    <Input
                        id="search"
                        :model-value="filters.search"
                        type="text"
                        placeholder="Marque, modèle ou plaque..."
                        class="mt-1"
                        @input="(e) => search((e.target as HTMLInputElement).value)"
                    />
                </div>
            </div>

            <div v-if="vehicles.data.length === 0" class="flex flex-col items-center justify-center py-12">
                <p class="text-muted-foreground">Aucun véhicule disponible</p>
            </div>

            <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Card v-for="vehicle in vehicles.data" :key="vehicle.id">
                    <CardHeader>
                        <CardTitle>{{ vehicle.brand }} {{ vehicle.model }}</CardTitle>
                        <CardDescription>
                            {{ vehicle.year }} • {{ vehicle.color }} • {{ vehicle.plate_number }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <p v-if="vehicle.description" class="text-sm text-muted-foreground">
                            {{ vehicle.description }}
                        </p>
                        <div class="mt-2">
                            <span
                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                :class="vehicle.is_available ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'"
                            >
                                {{ vehicle.is_available ? 'Disponible' : 'Indisponible' }}
                            </span>
                        </div>
                    </CardContent>
                    <CardFooter class="flex gap-2">
                        <Button
                            variant="outline"
                            class="flex-1"
                            :href="vehicleShow(vehicle.id).url"
                            as="a"
                        >
                            Voir détails
                        </Button>
                        <Button
                            v-if="vehicle.is_available"
                            class="flex-1"
                            :href="reservationCreate().url + '?vehicle_id=' + vehicle.id"
                            as="a"
                        >
                            Réserver
                        </Button>
                    </CardFooter>
                </Card>
            </div>

            <div v-if="vehicles.last_page > 1" class="flex justify-center gap-2">
                <Button
                    v-for="link in vehicles.links"
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
