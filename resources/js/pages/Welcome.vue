<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

import { dashboard, login, register } from '@/routes';

defineProps<{
    canRegister: boolean;
}>();
</script>

<template>
        <div
            class="relative flex min-h-screen items-center justify-center bg-gradient-to-br from-background via-muted to-background px-6 text-foreground"
        >
            <!-- Carte centrale -->
            <div
                class="w-full max-w-xl rounded-2xl border border-border bg-background/70 p-10 shadow-xl backdrop-blur-md"
            >
                <!-- Logo & branding -->
                <div class="mb-8 flex flex-col items-center gap-4 text-center">
                    <img
                        src="/images/lux-drive-logo.png"
                        alt="Logo Lux Drive"
                        class="h-24 w-24 object-contain"
                    />
    
                    <h1 class="text-3xl font-semibold tracking-tight">
                        Lux Drive
                    </h1>
    
                    <p class="text-sm text-muted-foreground">
                        Plateforme intelligente de réservation de véhicules
                    </p>
                </div>
    
                <!-- Description -->
                <div class="mb-10 text-center text-sm leading-relaxed text-muted-foreground">
                    Lux Drive permet aux employés de réserver facilement les véhicules
                    de l’organisation, de gérer les disponibilités et d’éviter les
                    conflits de planning grâce à une application web sécurisée et intuitive.
                </div>
    
                <!-- Actions -->
                <div class="flex flex-col gap-3">
                    <template v-if="$page.props.auth.user">
                        <Link
                            :href="dashboard()"
                            class="inline-flex justify-center rounded-xl bg-primary px-4 py-2.5 text-sm font-medium text-primary-foreground transition hover:opacity-90"
                        >
                            Accéder au tableau de bord
                        </Link>
                    </template>
    
                    <template v-else>
                        <Link
                            :href="login()"
                            class="inline-flex justify-center rounded-xl bg-primary px-4 py-2.5 text-sm font-medium text-primary-foreground transition hover:opacity-90"
                        >
                            Se connecter
                        </Link>
    
                        <Link
                            v-if="canRegister"
                            :href="register()"
                            class="inline-flex justify-center rounded-xl border border-border px-4 py-2.5 text-sm font-medium transition hover:bg-muted"
                        >
                            Créer un compte
                        </Link>
                    </template>
                </div>
            </div>
        </div>
    </template>
    