<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';

import ReservationController from '@/actions/App/Http/Controllers/ReservationController';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AppLayout from '@/layouts/AppLayout.vue';
import { show as reservationShow } from '@/routes/reservations';
import { index as reservationsIndex } from '@/routes/reservations';
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
    reservation: Reservation;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Réservations',
        href: reservationsIndex().url,
    },
    {
        title: `Modifier réservation #${props.reservation.id}`,
        href: '#',
    },
];

const formatDateTimeLocal = (dateString: string) => {
    const date = new Date(dateString);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    return `${year}-${month}-${day}T${hours}:${minutes}`;
};
</script>

<template>
    <Head :title="`Modifier réservation #${reservation.id}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold">Modifier la réservation</h1>
                    <p class="text-muted-foreground">Modifiez les détails de votre réservation</p>
                </div>
                <Button variant="outline" :href="reservationShow(reservation.id).url" as="a">
                    Retour
                </Button>
            </div>

            <div class="max-w-2xl">
                <Card>
                    <CardHeader>
                        <CardTitle>Détails de la réservation</CardTitle>
                        <CardDescription>{{ reservation.vehicle.brand }} {{ reservation.vehicle.model }} - {{ reservation.vehicle.plate_number }}</CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Form
                            v-bind="ReservationController.update.form(reservation.id)"
                            class="space-y-6"
                            v-slot="{ errors, processing }"
                        >
                            <div class="grid gap-2">
                                <Label for="start_date">Date et heure de début</Label>
                                <Input
                                    id="start_date"
                                    type="datetime-local"
                                    name="start_date"
                                    :default-value="formatDateTimeLocal(reservation.start_date)"
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
                                    :default-value="formatDateTimeLocal(reservation.end_date)"
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
                                    :default-value="reservation.purpose"
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
                                    Enregistrer les modifications
                                </Button>
                                <Button
                                    type="button"
                                    variant="outline"
                                    :href="reservationShow(reservation.id).url"
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
