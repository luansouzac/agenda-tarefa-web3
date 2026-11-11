<script setup>
import FormCategoria from './FormCategoria.vue';
import { onMounted, ref } from 'vue';
import { useCategoriaStore } from '@/stores/categoria';
import { storeToRefs } from 'pinia';

const storeCategoria = useCategoriaStore();
const {categorias} = storeToRefs(storeCategoria);

const editar = ref({
    idcategoria: '',
    descricao: ''
});

onMounted(() => {
    //Exibir todos os registros da tabela
    storeCategoria.listaCategoria();
});

const apagarCategoria = (categoria) => {
    if(confirm(`Deseja apagar a categoria ${categoria.descricao}?`)){
        storeCategoria.deleteCategoria(categoria.idcategoria);
    }
}

const alterarCategoria = (categoria) => {
    editar.value = categoria;
}
</script>

<template>
    <FormCategoria :categoria="editar"></FormCategoria>
    <table>
        <!-- Cabeçalho da tabela -->
        <tr>
            <th>Descrição</th>
            <th>Ações</th>
        </tr>
        <!-- Corpo da tabela -->
        <tr v-for="categoria in categorias" :key="categoria.idcategoria">
            <td>{{ categoria.descricao }}</td>
            <td>
                <button @click="alterarCategoria(categoria)">Alterar</button>
                <button @click="apagarCategoria(categoria)">Excluir</button>
            </td>
        </tr>
    </table>
</template>