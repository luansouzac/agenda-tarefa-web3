<script setup>
    import { ref, defineProps } from 'vue';
    import { useTarefaStore } from '@/stores/tarefa';
    import { storeToRefs } from 'pinia';
    import { onMounted } from 'vue';
    import FormTarefa from './FormTarefa.vue';

    const storeTarefa = useTarefaStore();
    const {tarefas} = storeToRefs(storeTarefa);

    onMounted(() => {
        storeTarefa.listaTarefa();
    });

    const excluir = (item) => {
        if(confirm(`Deseja apagar a tarefa ${item.descricao}?`)){
            storeTarefa.deleteTarefa(item.idtarefa);
        }
    }

    const editar = ref({
        idtarefa : '',
        idcategoria : '',
        idusuario : '',
        descricao : '',
        data : '',
        cumprida : false
    });

    const alterar = (item) => {
        editar.value = item;
    }
</script>

<template>
    <FormTarefa :tarefa="editar"></FormTarefa>
    <table>
        <thead>
            <tr>
                <th>Tarefa</th><th>Data</th>
                <th>Categoria</th><th>Finalizada</th><th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="tarefa in tarefas" :key="tarefa.idtarefa">
                <td>{{ tarefa.descricao }}</td>
                <td>{{ tarefa.data }}</td>
                <td>{{ tarefa.categoria.descricao }}</td>
                <td>{{ tarefa.cumprida }}</td>
                <td>
                    <button @click="alterar(tarefa)">Alterar</button>
                    <button @click="excluir(tarefa)">Excluir</button>
                </td>
            </tr>
        </tbody>
    </table>
</template>

<style scoped>
    table{
        background-color: #ccc;
    }

    td, th{
        border: #000 solid 1px;
    }
</style>