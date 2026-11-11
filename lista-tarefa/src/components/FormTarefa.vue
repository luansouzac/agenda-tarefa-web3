<script setup>
    import { computed, onMounted, ref, watch } from 'vue';
    import { useCategoriaStore } from '@/stores/categoria';
    import { useTarefaStore } from '@/stores/tarefa';
    import { useNotificacaoStore } from '@/stores/notificacao';
    import { storeToRefs } from 'pinia';
    import { diaDe, hoje } from '@/utils/datas';
    import { mensagemErro } from '@/utils/erros';

    const aberto = defineModel({ type: Boolean });

    //Propriedade: tarefa a editar (vazia = nova)
    const props = defineProps(['tarefa']);

    const vazio = () => ({
        idtarefa : '',
        idcategoria : null,
        descricao : '',
        data : hoje(),
        cumprida : false
    });

    const item = ref(vazio());
    const salvando = ref(false);
    const editando = computed(() => !!item.value.idtarefa);

    //Ao abrir, carrega a tarefa recebida (ou limpa para uma nova)
    watch(aberto, (valor) => {
        if(!valor) return;
        item.value = props.tarefa?.idtarefa
            ? { ...props.tarefa, data: diaDe(props.tarefa.data), cumprida: !!props.tarefa.cumprida }
            : vazio();
    }, {immediate: true});

    const storeCategoria = useCategoriaStore();
    const storeTarefa = useTarefaStore();
    const notificacao = useNotificacaoStore();
    const { categorias } = storeToRefs(storeCategoria);

    onMounted(() => {
        storeCategoria.listaCategoria();
    });

    const obrigatorio = v => !!v || 'Campo obrigatório';

    const salvar = async (event) => {
        const { valid } = await event;
        if(!valid) return;

        salvando.value = true;
        try{
            if(editando.value){
                await storeTarefa.updateTarefa(item.value);
                notificacao.sucesso('Tarefa alterada');
            }else{
                await storeTarefa.insertTarefa(item.value);
                notificacao.sucesso('Tarefa adicionada');
            }
            aberto.value = false;
        }catch(error){
            notificacao.erro(mensagemErro(error, 'Erro ao salvar a tarefa'));
        }finally{
            salvando.value = false;
        }
    }
</script>

<template>
    <v-dialog v-model="aberto" max-width="480">
        <v-card rounded="xl" class="pa-4">
            <v-card-title class="text-h6 font-weight-bold">
                {{ editando ? 'Editar tarefa' : 'Nova tarefa' }}
            </v-card-title>
            <v-card-text>
                <v-form @submit.prevent="salvar" id="form-tarefa">
                    <v-text-field v-model="item.descricao" label="O que precisa ser feito?"
                        :rules="[obrigatorio]" autofocus />
                    <v-text-field v-model="item.data" type="date" label="Data" :rules="[obrigatorio]" />
                    <v-select v-model="item.idcategoria" :items="categorias" item-title="descricao"
                        item-value="idcategoria" label="Categoria" :rules="[obrigatorio]" />
                    <v-switch v-if="editando" v-model="item.cumprida" color="success"
                        label="Concluída" hide-details inset />
                </v-form>
            </v-card-text>
            <v-card-actions>
                <v-spacer />
                <v-btn variant="text" @click="aberto = false">Cancelar</v-btn>
                <v-btn type="submit" form="form-tarefa" color="primary" variant="flat" rounded="lg"
                    :loading="salvando">Salvar</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
