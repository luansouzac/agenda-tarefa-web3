import { defineStore } from "pinia";
import { ref, computed } from "vue";
import api from "@/plugin/axios";

export const useTarefaStore = defineStore("tarefa", ()=>{
    const tarefas = ref([]);
    const carregando = ref(false);

    const concluidas = computed(() => tarefas.value.filter(t => t.cumprida).length);

    //Exibir todos
    const listaTarefa = async () => {
        carregando.value = true;
        try{
            const response = await api.get('/tarefas');
            tarefas.value = response.data;
        }finally{
            carregando.value = false;
        }
    }

    //Inserir
    const insertTarefa = async (tarefa) => {
        const response = await api.post('/tarefas', tarefa);
        tarefas.value.push(response.data);
    }

    //Alterar
    const updateTarefa = async (tarefa) => {
        const response = await api.put(
            `/tarefas/${tarefa.idtarefa}`, tarefa);

        const index = tarefas.value.findIndex( 
            c => c.idtarefa == response.data.idtarefa);
        if (index != -1){
            tarefas.value[index] = response.data
        }
    }

    //Marcar / desmarcar como concluída
    const alternarCumprida = async (tarefa) => {
        await updateTarefa({ ...tarefa, cumprida: !tarefa.cumprida });
    }

    //Deletar
    const deleteTarefa = async (idtarefa) => {
        await api.delete(`/tarefas/${idtarefa}`);
        tarefas.value = tarefas.value.filter(
            c => c.idtarefa !== idtarefa);
    }

    return {tarefas, carregando, concluidas, listaTarefa, insertTarefa,
        updateTarefa, alternarCumprida, deleteTarefa }
});
