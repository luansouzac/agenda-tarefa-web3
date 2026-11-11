<script setup>
import { computed, onMounted, ref } from 'vue';
import { storeToRefs } from 'pinia';
import { useCategoriaStore } from '@/stores/categoria';
import { useTarefaStore } from '@/stores/tarefa';
import { useNotificacaoStore } from '@/stores/notificacao';
import { corCategoria } from '@/utils/cores';
import { mensagemErro } from '@/utils/erros';
import FormCategoria from './FormCategoria.vue';
import ConfirmDialog from './ConfirmDialog.vue';

const storeCategoria = useCategoriaStore();
const storeTarefa = useTarefaStore();
const notificacao = useNotificacaoStore();
const { categorias, carregando } = storeToRefs(storeCategoria);
const { tarefas } = storeToRefs(storeTarefa);

onMounted(() => {
    //Exibir todos os registros da tabela
    storeCategoria.listaCategoria();
    storeTarefa.listaTarefa();
});

//Quantas tarefas (pendentes e no total) o usuário tem em cada categoria
const resumo = (idcategoria) => {
    const daCategoria = tarefas.value.filter(t => t.idcategoria == idcategoria);
    return {
        total: daCategoria.length,
        pendentes: daCategoria.filter(t => !t.cumprida).length
    };
}

const totalTarefas = computed(() => tarefas.value.length);

//Dialog de cadastro/edição
const formAberto = ref(false);
const editar = ref(null);

const nova = () => {
    editar.value = null;
    formAberto.value = true;
}

const alterarCategoria = (categoria) => {
    editar.value = categoria;
    formAberto.value = true;
}

//Exclusão com confirmação
const confirmarAberto = ref(false);
const paraExcluir = ref(null);

const apagarCategoria = (categoria) => {
    paraExcluir.value = categoria;
    confirmarAberto.value = true;
}

const confirmarExclusao = async () => {
    try{
        await storeCategoria.deleteCategoria(paraExcluir.value.idcategoria);
        notificacao.sucesso('Categoria excluída');
    }catch(error){
        notificacao.erro(mensagemErro(error, 'Erro ao excluir a categoria'));
    }
}
</script>

<template>
    <div class="pagina mx-auto">
        <!-- Resumo -->
        <v-card class="resumo pa-6 mb-6" rounded="xl" color="primary" elevation="0">
            <div class="d-flex align-center flex-wrap ga-4">
                <div class="flex-grow-1">
                    <div class="text-overline">Organização</div>
                    <h1 class="text-h5 font-weight-bold">Categorias</h1>
                    <p class="mt-1 opacity-80">
                        {{ categorias.length }} {{ categorias.length === 1 ? 'categoria' : 'categorias' }}
                        para separar suas {{ totalTarefas }} tarefas.
                    </p>
                </div>
                <v-avatar color="rgba(255,255,255,.15)" size="72" rounded="xl">
                    <v-icon icon="mdi-tag-multiple-outline" size="36" color="secondary" />
                </v-avatar>
            </div>
        </v-card>

        <div class="d-flex align-center mb-4">
            <h2 class="grupo text-subtitle-2">
                Todas
                <span class="contador">{{ categorias.length }}</span>
            </h2>
            <v-spacer />
            <v-btn color="secondary" variant="flat" rounded="lg" prepend-icon="mdi-plus" @click="nova">
                Nova categoria
            </v-btn>
        </div>

        <v-progress-linear v-if="carregando" indeterminate color="secondary" class="mb-4" rounded />

        <v-card v-if="categorias.length" rounded="lg" elevation="0" class="lista">
            <v-list class="py-0">
                <template v-for="(categoria, i) in categorias" :key="categoria.idcategoria">
                    <v-divider v-if="i" />
                    <v-list-item class="py-3">
                        <template #prepend>
                            <v-avatar :color="corCategoria(categorias, categoria.idcategoria)" variant="tonal"
                                rounded="lg" size="38" class="mr-3">
                                <v-icon icon="mdi-tag-outline" size="20" />
                            </v-avatar>
                        </template>

                        <v-list-item-title class="font-weight-medium">{{ categoria.descricao }}</v-list-item-title>
                        <v-list-item-subtitle>
                            <template v-if="resumo(categoria.idcategoria).total">
                                {{ resumo(categoria.idcategoria).pendentes }} pendentes ·
                                {{ resumo(categoria.idcategoria).total }} no total
                            </template>
                            <template v-else>Nenhuma tarefa</template>
                        </v-list-item-subtitle>

                        <template #append>
                            <v-btn icon="mdi-pencil-outline" variant="text" size="small"
                                @click="alterarCategoria(categoria)" />
                            <v-btn icon="mdi-trash-can-outline" variant="text" size="small" color="error"
                                @click="apagarCategoria(categoria)" />
                        </template>
                    </v-list-item>
                </template>
            </v-list>
        </v-card>

        <!-- Estado vazio -->
        <div v-if="!carregando && !categorias.length" class="vazio text-center py-12">
            <v-icon icon="mdi-tag-off-outline" size="56" color="primary" class="mb-3" />
            <div class="text-h6 font-weight-bold">Nenhuma categoria</div>
            <p class="text-medium-emphasis mb-4">Crie uma categoria para organizar suas tarefas.</p>
            <v-btn color="primary" variant="tonal" rounded="lg" prepend-icon="mdi-plus" @click="nova">
                Adicionar categoria
            </v-btn>
        </div>
    </div>

    <FormCategoria v-model="formAberto" :categoria="editar" />
    <ConfirmDialog v-model="confirmarAberto" titulo="Excluir categoria" textoConfirmar="Excluir"
        :mensagem="`Deseja apagar a categoria &quot;${paraExcluir?.descricao}&quot;?`"
        @confirmar="confirmarExclusao" />
</template>

<style scoped>
    .pagina { max-width: 760px; }
    .resumo {
        background: linear-gradient(135deg, rgb(var(--v-theme-primary)) 0%, rgb(var(--v-theme-primary-claro)) 100%) !important;
    }
    .grupo {
        text-transform: uppercase;
        letter-spacing: .08em;
        font-weight: 700;
        color: rgba(var(--v-theme-primary), .7);
    }
    .contador {
        display: inline-block;
        margin-left: 6px;
        padding: 0 8px;
        border-radius: 10px;
        background: rgba(var(--v-theme-primary), .08);
        font-size: 11px;
    }
    .lista { border: 1px solid rgba(var(--v-theme-primary), 0.1); }
    .lista :deep(.v-list-item) { text-align: left; }
    .vazio {
        border: 2px dashed rgba(var(--v-theme-primary), .15);
        border-radius: 16px;
    }
</style>
