import { defineStore } from "pinia";
import { ref, computed } from "vue";
import api from "@/plugin/axios";

export const useTarefaStore = defineStore("tarefa", ()=>{
    const tarefas = ref([]);

    //Exibir todos
    const listaTarefa = async () => {
        const response = await api.get('/tarefas');
        tarefas.value = response.data;
    }

    //Inserir
    const insertTarefa = async (tarefa) => {
        try{
            const response = await api.post('/tarefas', tarefa);
            tarefas.value.push(response.data);
        }
        catch(error){
            console.log(error);
            alert('Erro ao realizar o cadastro');
        }
    }

    //Alterar
    const updateTarefa = async (tarefa) => {
        try{
            const response = await api.put(
                `/tarefas/${tarefa.idtarefa}`, tarefa);

            const index = tarefas.value.findIndex( 
                c => c.idtarefa == response.data.idtarefa);
            if (index != -1){
                tarefas.value[index] = response.data
            }
        }catch(error){
            console.log(error);
            throw(error);
        }
    }

    //Deletar
    const deleteTarefa = async (idtarefa) => {
        try{
            await api.delete(`/tarefas/${idtarefa}`);
            tarefas.value = tarefas.value.filter(
                c => c.idtarefa !== idtarefa);
        }catch(error){
            console.log(error);
            throw(error);
        }
    }

    return {tarefas, listaTarefa, insertTarefa, updateTarefa, 
        deleteTarefa }
});