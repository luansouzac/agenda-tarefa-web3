<script setup>
    defineProps({
        titulo: String,
        subtitulo: String
    });
</script>

<template>
    <div class="auth-wrap">
        <v-card class="auth-card pa-8" rounded="xl" elevation="0" width="100%" max-width="420">
            <div class="d-flex align-center mb-6">
                <v-avatar color="primary" size="44" rounded="lg">
                    <v-icon icon="mdi-calendar-check" />
                </v-avatar>
                <span class="ml-3 text-h6 font-weight-bold">Agenda</span>
            </div>
            <h1 class="text-h5 font-weight-bold mb-1">{{ titulo }}</h1>
            <p class="text-medium-emphasis mb-6">{{ subtitulo }}</p>
            <slot />
            <div class="text-center mt-6 text-body-2">
                <slot name="rodape" />
            </div>
        </v-card>
    </div>
</template>

<style scoped>
    .auth-wrap {
        min-height: calc(100vh - 32px);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .auth-card {
        border: 1px solid rgba(var(--v-theme-primary), 0.12);
    }
</style>
