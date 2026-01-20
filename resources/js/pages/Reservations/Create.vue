<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';

import ReservationController from '@/actions/App/Http/Controllers/ReservationController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as reservationsIndex } from '@/routes/reservations';
import { type BreadcrumbItem } from '@/types';

interface Vehicle {
    id: number;
    brand: string;
    model: string;
    plate_number: string;
}

interface Props {
    vehicle?: Vehicle;
    vehicles: Vehicle[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Réservations',
        href: reservationsIndex().url,
    },
    {
        title: 'Nouvelle réservation',
        href: '#',
    },
];
</script>

<template>
    <Head title="Nouvelle réservation" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Nouvelle réservation</h1>
                    <p class="text-muted-foreground">Réservez un véhicule pour vos déplacements professionnels</p>
                </div>
                <Button variant="outline" :href="reservationsIndex().url" as="a">
                    Retour
                </Button>
            </div>

            <div class="max-w-2xl">
                <Card v-if="props.vehicle">
                    <CardHeader>
                        <CardTitle>Véhicule sélectionné</CardTitle>
                        <CardDescription>{{ props.vehicle.brand }} {{ props.vehicle.model }} - {{ props.vehicle.plate_number }}</CardDescription>
                    </CardHeader>
                </Card>

                <Card class="mt-4">
                    <CardHeader>
                        <CardTitle>Détails de la réservation</CardTitle>
                        <CardDescription>Remplissez les informations pour créer votre réservation</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Form
                            v-bind="ReservationController.store.form()"
                            class="space-y-6"
                            v-slot="{ errors, processing }"
                        >
                            <input v-if="props.vehicle" type="hidden" name="vehicle_id" :value="props.vehicle.id" />

                            <div v-if="!props.vehicle" class="grid gap-2">
                                <Label for="vehicle_id">Véhicule</Label>
                                <select 
                                    id="vehicle_id"
                                    name="vehicle_id"
                                    class="mt-1 block w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] outline-none dark:bg-zinc-800 dark:text-zinc-50 dark:border-zinc-700/80"
                                    required
                                >
                                    <option value="">Sélectionnez un véhicule</option>
                                    <option
                                        v-for="v in props.vehicles"
                                        :key="v.id"
                                        :value="v.id"
                                    >
                                        {{ v.brand }} {{ v.model }} - {{ v.plate_number }}
                                    </option>
                                </select>
                                <InputError :message="errors.vehicle_id" />
                                <p v-if="props.vehicles.length === 0" class="text-sm text-muted-foreground">
                                    Aucun véhicule disponible pour le moment.
                                </p>
                            </div>

                            <div class="grid gap-2">
                                <Label for="start_date">Date et heure de début</Label>
                                <Input
                                    id="start_date"
                                    type="datetime-local"
                                    name="start_date"
                                    class="dark:bg-zinc-800 dark:text-zinc-50"
                                    required
                                />
                                <InputError :message="errors.start_date" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="end_date">Date et heure de fin</Label>
                                <Input
                                    id="end_date"
                                    type="datetime-local"
                                    name="end_date"
                                    class="dark:bg-zinc-800 dark:text-zinc-50"
                                    required
                                />
                                <InputError :message="errors.end_date" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="purpose">Raison de la réservation</Label>
                                <Input
                                    id="purpose"
                                    type="text"
                                    name="purpose"
                                    placeholder="Ex: Déplacement client Paris"
                                    required
                                />
                                <InputError :message="errors.purpose" />
                            </div>

                            <div class="flex items-center gap-4">
                                <Button
                                    type="submit"
                                    :disabled="processing"
                                >
                                    <Spinner v-if="processing" />
                                    Créer la réservation
                                </Button>
                                <Button
                                    type="button"
                                    variant="outline"
                                    :href="reservationsIndex().url"
                                    as="a"
                                >
                                    Annuler
                                </Button>
                            </div>
                        </Form>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
