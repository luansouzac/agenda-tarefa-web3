import { defineStore } from "pinia";
import { ref } from "vue";
import api from "@/plugin/axios";

export const useCategoriaStore = defineStore("categoria", ()=>{
    const categorias = ref([]);
    const carregando = ref(false);

    //Exibir todos
    const listaCategoria = async () => {
        carregando.value = true;
        try{
            const response = await api.get('/categorias');
            categorias.value = response.data;
        }finally{
            carregando.value = false;
        }
    }

    //Inserir
    const insertCategoria = async (categoria) => {
        const response = await api.post('/categorias', categoria);
        categorias.value.push(response.data);
    }

    //Alterar
    const updateCategoria = async (categoria) => {
        const response = await api.put(
            `/categorias/${categoria.idcategoria}`, categoria);

        const index = categorias.value.findIndex( 
            c => c.idcategoria == response.data.idcategoria);
        if (index != -1){
             categorias.value[index] = response.data
        }
    }

    //Deletar
    const deleteCategoria = async (idcategoria) => {
        await api.delete(`/categorias/${idcategoria}`);
        categorias.value = categorias.value.filter(
            c => c.idcategoria !== idcategoria);
    }

    return {categorias, carregando, listaCategoria, insertCategoria, updateCategoria, 
        deleteCategoria }
});
