<script setup>
    const aberto = defineModel({ type: Boolean });

    defineProps({
        titulo: { type: String, default: 'Confirmar' },
        mensagem: String,
        textoConfirmar: { type: String, default: 'Confirmar' }
    });

    const emit = defineEmits(['confirmar']);

    const confirmar = () => {
        emit('confirmar');
        aberto.value = false;
    }
</script>

<template>
    <v-dialog v-model="aberto" max-width="400">
        <v-card rounded="xl" class="pa-2">
            <v-card-title class="font-weight-bold">{{ titulo }}</v-card-title>
            <v-card-text>{{ mensagem }}</v-card-text>
            <v-card-actions>
                <v-spacer />
                <v-btn variant="text" @click="aberto = false">Cancelar</v-btn>
                <v-btn color="error" variant="flat" rounded="lg" @click="confirmar">{{ textoConfirmar }}</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
