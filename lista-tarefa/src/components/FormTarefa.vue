<script setup>
    import { onMounted, ref, watch } from 'vue';
    import { useCategoriaStore } from '@/stores/categoria';
    import { useTarefaStore } from '@/stores/tarefa';
    import { storeToRefs } from 'pinia';

    //Propriedade
    const props = defineProps(['tarefa']);

    
    const item = ref({
        idtarefa : '',
        idusuario : '',
        idcategoria : '',
        descricao : '',
        data : '',
        cumprida : false
    });
    
    //Observador
    watch(() => props.tarefa, (tarefa)=>{
        item.value = { ...tarefa };
    }, {immediate: true});
    
    const storeCategoria = useCategoriaStore();
    const storeTarefa = useTarefaStore();
    const { categorias } = storeToRefs(storeCategoria);

    onMounted(() => {
        storeCategoria.listaCategoria();
    });

    //function adicionarTarefa(){
    const adicionarTarefa = () => {
        //Verificar se os dados foram preenchidos
        console.log(item);
        if(item.value.descricao && item.value.data && item.value.idcategoria){
            item.value.idusuario = localStorage.getItem("idusuario");
            try{
                if(item.value.idtarefa == ''){
                    storeTarefa.insertTarefa(item.value);
                    alert('Tarefa adicionada');
                }else{
                    storeTarefa.updateTarefa(item.value);
                    alert('Tarefa alterada');
                }
                limparCampos();
            }catch(error){
                alert('Erro ao adicionar a tarefa');
            }
            
            
        }else{
            alert('Por favor, preencha todos os campos da tarefa');
        }
        
    }

    const limparCampos = () => {
        item.value = {
            idtarefa : '',
            idusuario : '',
            idcategoria : '',
            descricao : '',
            data : '',
            cumprida : false
        }
    }

</script>

<template>
    <h1>Tarefas</h1>
    <form @submit.prevent="adicionarTarefa">
        <label for="tarefa">Tarefa: </label>
        <input type="text" id="tarefa" placeholder="Digite a Tarefa" 
            v-model="item.descricao"><br>
        <label for="data">Data: </label>
        <input type="date" id="data" placeholder="Selecione a data"
            v-model="item.data"><br>
        <label for="hora">Categoria: </label>
        <select v-model="item.idcategoria">
            <option v-for="categoria in categorias" 
                :value="categoria.idcategoria">
                    {{ categoria.descricao }}</option>
        </select><br>
        <input type="checkbox" id="cumprido" v-model="item.cumprida">
        <label for="cumprido">Concluída</label>
        <input type="submit" value="Salvar">
    </form>
</template>