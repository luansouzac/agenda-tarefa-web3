<script setup>
    import { computed, ref, watch } from 'vue';
    import { useCategoriaStore } from '@/stores/categoria';
    import { useNotificacaoStore } from '@/stores/notificacao';
    import { mensagemErro } from '@/utils/erros';

    const aberto = defineModel({ type: Boolean });

    //Propriedade: categoria a editar (vazia = nova)
    const props = defineProps(['categoria']);

    const item = ref({
        idcategoria: '',
        descricao: ''
    });
    const salvando = ref(false);
    const editando = computed(() => !!item.value.idcategoria);

    const storeCategoria = useCategoriaStore();
    const notificacao = useNotificacaoStore();

    //Ao abrir, carrega a categoria recebida (ou limpa para uma nova)
    watch(aberto, (valor) => {
        if(!valor) return;
        item.value = props.categoria?.idcategoria
            ? { ...props.categoria }
            : { idcategoria: '', descricao: '' };
    }, {immediate: true});

    const obrigatorio = v => !!v?.trim() || 'Campo obrigatório';

    const salvar = async (event) => {
        const { valid } = await event;
        if(!valid) return;

        salvando.value = true;
        try{
            if(editando.value){
                await storeCategoria.updateCategoria(item.value);
                notificacao.sucesso('Categoria alterada');
            }else{
                await storeCategoria.insertCategoria(item.value);
                notificacao.sucesso('Categoria adicionada');
            }
            aberto.value = false;
        }catch(error) {
            notificacao.erro(mensagemErro(error, 'Erro ao salvar a categoria'));
        }finally{
            salvando.value = false;
        }
    }
</script>

<template>
    <v-dialog v-model="aberto" max-width="420">
        <v-card rounded="xl" class="pa-4">
            <v-card-title class="text-h6 font-weight-bold">
                {{ editando ? 'Editar categoria' : 'Nova categoria' }}
            </v-card-title>
            <v-card-text>
                <v-form @submit.prevent="salvar" id="form-categoria">
                    <v-text-field v-model="item.descricao" label="Nome da categoria"
                        :rules="[obrigatorio]" autofocus />
                </v-form>
            </v-card-text>
            <v-card-actions>
                <v-spacer />
                <v-btn variant="text" @click="aberto = false">Cancelar</v-btn>
                <v-btn type="submit" form="form-categoria" color="primary" variant="flat" rounded="lg"
                    :loading="salvando">Salvar</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
