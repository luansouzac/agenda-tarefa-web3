<script setup>
    import { computed, onMounted, ref } from 'vue';
    import { storeToRefs } from 'pinia';
    import { useTarefaStore } from '@/stores/tarefa';
    import { useCategoriaStore } from '@/stores/categoria';
    import { useAuthStore } from '@/stores/auth';
    import { useNotificacaoStore } from '@/stores/notificacao';
    import { diaDe, hoje, amanha, formatarDia } from '@/utils/datas';
    import { mensagemErro } from '@/utils/erros';
    import { corCategoria as cor } from '@/utils/cores';
    import FormTarefa from './FormTarefa.vue';
    import TarefaItem from './TarefaItem.vue';
    import ConfirmDialog from './ConfirmDialog.vue';

    const storeTarefa = useTarefaStore();
    const storeCategoria = useCategoriaStore();
    const authStore = useAuthStore();
    const notificacao = useNotificacaoStore();
    const { tarefas, carregando, concluidas } = storeToRefs(storeTarefa);
    const { categorias } = storeToRefs(storeCategoria);

    onMounted(() => {
        storeTarefa.listaTarefa();
        storeCategoria.listaCategoria();
    });

    //Filtros
    const busca = ref('');
    const filtroCategoria = ref(null);
    const mostrarConcluidas = ref(false);

    const corCategoria = (idcategoria) => cor(categorias.value, idcategoria);

    const filtradas = computed(() => tarefas.value.filter(t =>
        (!filtroCategoria.value || t.idcategoria == filtroCategoria.value) &&
        t.descricao.toLowerCase().includes(busca.value.toLowerCase())
    ));

    //Pendentes agrupadas por dia: atrasadas, hoje, amanhã e próximas datas
    const grupos = computed(() => {
        const pendentes = filtradas.value
            .filter(t => !t.cumprida)
            .sort((a, b) => diaDe(a.data).localeCompare(diaDe(b.data)));

        const mapa = new Map();
        for(const t of pendentes){
            const dia = diaDe(t.data);
            let titulo;
            if(dia < hoje()) titulo = 'Atrasadas';
            else if(dia === hoje()) titulo = 'Hoje';
            else if(dia === amanha()) titulo = 'Amanhã';
            else titulo = formatarDia(dia);

            if(!mapa.has(titulo)) mapa.set(titulo, []);
            mapa.get(titulo).push(t);
        }
        return [...mapa.entries()].map(([titulo, itens]) => ({ titulo, itens }));
    });

    const listaConcluidas = computed(() => filtradas.value.filter(t => t.cumprida));

    const progresso = computed(() => tarefas.value.length
        ? Math.round(concluidas.value / tarefas.value.length * 100) : 0);

    const pendentesHoje = computed(() => tarefas.value
        .filter(t => !t.cumprida && diaDe(t.data) <= hoje()).length);

    const primeiroNome = computed(() => authStore.usuario?.name?.split(' ')[0] ?? '');

    //Dialog de cadastro/edição
    const formAberto = ref(false);
    const editar = ref(null);

    const nova = () => {
        editar.value = null;
        formAberto.value = true;
    }

    const alterar = (item) => {
        editar.value = item;
        formAberto.value = true;
    }

    const alternar = async (item) => {
        try{
            await storeTarefa.alternarCumprida(item);
        }catch(error){
            notificacao.erro(mensagemErro(error, 'Erro ao atualizar a tarefa'));
        }
    }

    //Exclusão com confirmação
    const confirmarAberto = ref(false);
    const paraExcluir = ref(null);

    const excluir = (item) => {
        paraExcluir.value = item;
        confirmarAberto.value = true;
    }

    const confirmarExclusao = async () => {
        try{
            await storeTarefa.deleteTarefa(paraExcluir.value.idtarefa);
            notificacao.sucesso('Tarefa excluída');
        }catch(error){
            notificacao.erro(mensagemErro(error, 'Erro ao excluir a tarefa'));
        }
    }
</script>

<template>
    <div class="pagina mx-auto">
        <!-- Resumo -->
        <v-card class="resumo pa-6 mb-6" rounded="xl" color="primary" elevation="0">
            <div class="d-flex align-center flex-wrap ga-4">
                <div class="flex-grow-1">
                    <div class="text-overline">{{ formatarDia(hoje()) }}</div>
                    <h1 class="text-h5 font-weight-bold">Olá, {{ primeiroNome }}!</h1>
                    <p class="mt-1 opacity-80">
                        <template v-if="pendentesHoje">
                            Você tem <strong>{{ pendentesHoje }}</strong>
                            {{ pendentesHoje === 1 ? 'tarefa' : 'tarefas' }} para hoje ou atrasada{{ pendentesHoje === 1 ? '' : 's' }}.
                        </template>
                        <template v-else>Nada pendente para hoje. 🎉</template>
                    </p>
                </div>
                <v-progress-circular :model-value="progresso" size="84" width="8" color="secondary"
                    bg-color="rgba(255,255,255,.15)">
                    <div class="text-center">
                        <div class="text-h6 font-weight-bold">{{ progresso }}%</div>
                    </div>
                </v-progress-circular>
            </div>
            <div class="text-caption mt-3 opacity-80">
                {{ concluidas }} de {{ tarefas.length }} tarefas concluídas
            </div>
        </v-card>

        <!-- Filtros -->
        <div class="d-flex align-center flex-wrap ga-3 mb-2">
            <v-text-field v-model="busca" placeholder="Buscar tarefa" prepend-inner-icon="mdi-magnify"
                density="compact" hide-details clearable class="busca" bg-color="surface" />
            <v-spacer />
            <v-btn color="secondary" variant="flat" rounded="lg" prepend-icon="mdi-plus" @click="nova">
                Nova tarefa
            </v-btn>
        </div>
        <v-chip-group v-model="filtroCategoria" selected-class="text-white bg-primary" class="mb-4">
            <v-chip v-for="categoria in categorias" :key="categoria.idcategoria"
                :value="categoria.idcategoria" variant="outlined" filter>
                <v-icon icon="mdi-circle" :color="corCategoria(categoria.idcategoria)" size="10" start />
                {{ categoria.descricao }}
            </v-chip>
        </v-chip-group>

        <v-progress-linear v-if="carregando" indeterminate color="secondary" class="mb-4" rounded />

        <!-- Pendentes por dia -->
        <section v-for="grupo in grupos" :key="grupo.titulo" class="mb-6">
            <h2 class="grupo text-subtitle-2 mb-2" :class="{ 'text-error': grupo.titulo === 'Atrasadas' }">
                {{ grupo.titulo }}
                <span class="contador">{{ grupo.itens.length }}</span>
            </h2>
            <v-card rounded="lg" elevation="0" class="lista">
                <v-list class="py-0">
                    <template v-for="(tarefa, i) in grupo.itens" :key="tarefa.idtarefa">
                        <v-divider v-if="i" />
                        <TarefaItem :tarefa="tarefa" :cor="corCategoria(tarefa.idcategoria)"
                            @alternar="alternar" @editar="alterar" @excluir="excluir" />
                    </template>
                </v-list>
            </v-card>
        </section>

        <!-- Estado vazio -->
        <div v-if="!carregando && !grupos.length" class="vazio text-center py-12">
            <v-icon icon="mdi-calendar-blank-outline" size="56" color="primary" class="mb-3" />
            <div class="text-h6 font-weight-bold">Nenhuma tarefa pendente</div>
            <p class="text-medium-emphasis mb-4">Que tal planejar o próximo passo?</p>
            <v-btn color="primary" variant="tonal" rounded="lg" prepend-icon="mdi-plus" @click="nova">
                Adicionar tarefa
            </v-btn>
        </div>

        <!-- Concluídas -->
        <section v-if="listaConcluidas.length" class="mt-4">
            <v-btn variant="text" class="px-0 mb-3" :append-icon="mostrarConcluidas ? 'mdi-chevron-up' : 'mdi-chevron-down'"
                @click="mostrarConcluidas = !mostrarConcluidas">
                Concluídas ({{ listaConcluidas.length }})
            </v-btn>
            <v-expand-transition>
                <v-card v-show="mostrarConcluidas" rounded="lg" elevation="0" class="lista">
                    <v-list class="py-0">
                        <template v-for="(tarefa, i) in listaConcluidas" :key="tarefa.idtarefa">
                            <v-divider v-if="i" />
                            <TarefaItem :tarefa="tarefa" :cor="corCategoria(tarefa.idcategoria)"
                                @alternar="alternar" @editar="alterar" @excluir="excluir" />
                        </template>
                    </v-list>
                </v-card>
            </v-expand-transition>
        </section>
    </div>

    <FormTarefa v-model="formAberto" :tarefa="editar" />
    <ConfirmDialog v-model="confirmarAberto" titulo="Excluir tarefa" textoConfirmar="Excluir"
        :mensagem="`Deseja apagar a tarefa &quot;${paraExcluir?.descricao}&quot;?`"
        @confirmar="confirmarExclusao" />
</template>

<style scoped>
    .pagina { max-width: 760px; }
    .resumo {
        background: linear-gradient(135deg, rgb(var(--v-theme-primary)) 0%, rgb(var(--v-theme-primary-claro)) 100%) !important;
    }
    .busca { max-width: 320px; }
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
    .vazio {
        border: 2px dashed rgba(var(--v-theme-primary), .15);
        border-radius: 16px;
    }
</style>
