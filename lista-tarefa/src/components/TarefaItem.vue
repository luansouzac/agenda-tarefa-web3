<script setup>
    import { computed } from 'vue';
    import { diaDe, hoje } from '@/utils/datas';

    const props = defineProps({
        tarefa: { type: Object, required: true },
        cor: { type: String, default: 'primary' }
    });

    const emit = defineEmits(['alternar', 'editar', 'excluir']);

    const atrasada = computed(() =>
        !props.tarefa.cumprida && diaDe(props.tarefa.data) < hoje());
</script>

<template>
    <v-list-item class="tarefa py-3" :class="{ feita: tarefa.cumprida }">
        <template #prepend>
            <v-btn :icon="tarefa.cumprida ? 'mdi-check-circle' : 'mdi-circle-outline'"
                :color="tarefa.cumprida ? 'success' : 'medium-emphasis'" variant="text" size="small"
                class="mr-2" @click="emit('alternar', tarefa)" />
        </template>

        <v-list-item-title class="descricao font-weight-medium">{{ tarefa.descricao }}</v-list-item-title>
        <div class="d-flex align-center ga-2 mt-1">
            <span class="categoria text-caption">
                <v-icon icon="mdi-circle" :color="cor" size="8" class="mr-1" />
                {{ tarefa.categoria?.descricao }}
            </span>
            <span v-if="atrasada" class="text-caption text-error font-weight-medium">
                · Atrasada
            </span>
        </div>

        <template #append>
            <v-btn icon="mdi-pencil-outline" variant="text" size="small" @click="emit('editar', tarefa)" />
            <v-btn icon="mdi-trash-can-outline" variant="text" size="small" color="error"
                @click="emit('excluir', tarefa)" />
        </template>
    </v-list-item>
</template>

<style scoped>
    .tarefa { text-align: left; }
    .categoria { color: rgba(0, 0, 0, .6); }
    .feita { opacity: .55; }
    .feita .descricao { text-decoration: line-through; }
</style>
