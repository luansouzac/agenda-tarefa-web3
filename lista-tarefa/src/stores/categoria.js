import { defineStore } from "pinia";
import { ref, computed } from "vue";
import api from "@/plugin/axios";

export const useCategoriaStore = defineStore("categoria", ()=>{
    const categorias = ref([]);

    //Exibir todos
    const listaCategoria = async () => {
        const response = await api.get('/categorias');
        categorias.value = response.data;
    }

    //Inserir
    const insertCategoria = async (categoria) => {
        const response = await api.post('/categorias', categoria);
        categorias.value.push(response.data);
    }

    //Alterar
    const updateCategoria = async (categoria) => {
        try{
            const response = await api.put(
                `/categorias/${categoria.idcategoria}`, categoria);

            const index = categorias.value.findIndex( 
                c => c.idcategoria == response.data.idcategoria);
            if (index != -1){
                 categorias.value[index] = response.data
            }
            //listaCategoria();
        }catch(error){
            console.log(error);
            throw(error);
        }
    }

    //Deletar
    const deleteCategoria = async (idcategoria) => {
        try{
            await api.delete(`/categorias/${idcategoria}`);
            categorias.value = categorias.value.filter(
                c => c.idcategoria !== idcategoria);
        }catch(error){
            console.log(error);
            throw(error);
        }
    }

    return {categorias, listaCategoria, insertCategoria, updateCategoria, 
        deleteCategoria }
});