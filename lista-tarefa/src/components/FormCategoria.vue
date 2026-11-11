<script setup>
    import { ref, watch } from 'vue';
    import { useCategoriaStore } from '@/stores/categoria';

    //Propriedade
    const props = defineProps(['categoria']);

    const item = ref({
        idcategoria: '',
        descricao: ''
    });

    const storeCategoria = useCategoriaStore();

    const cancelar = () => {
        item.value.idcategoria = '';
        item.value.descricao = '';
    }

    const salvar = () => {
        try{
            if(item.value.idcategoria==''){
                storeCategoria.insertCategoria(item.value);
            }else{
                storeCategoria.updateCategoria(item.value);
            }
            cancelar();
            alert('Operação realizada com sucesso!');
        }catch(error) {
            console.log('Erro ao cadastrar a categoria', error);
        }
    }

    watch(() => props.categoria, (categoria)=>{
        item.value = { ...categoria };
    }, {immediate: true});
</script>

<template>
    <form @submit.prevent="salvar">
        <label for="categoria">Categoria</label>
        <input type="text" id="categoria" v-model="item.descricao"><br>
        <input type="submit" value="Salvar">
        <input type="button" value="Cancelar" @click="cancelar">
    </form>
</template>