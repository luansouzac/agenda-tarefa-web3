import { defineStore } from "pinia"
import { ref, computed } from "vue"
import api from "@/plugin/axios"

const usuarioSalvo = () => {
    try{
        return JSON.parse(localStorage.getItem("usuario"));
    }catch{
        return null;
    }
}

export const useAuthStore = defineStore("auth", ()=>{
    const usuario = ref(usuarioSalvo());
    const token = ref(localStorage.getItem("token"));

    const isAuthenticated = computed(() => !!token.value);

    const salvarSessao = (dados) => {
        usuario.value = dados.user;
        token.value = dados.token;
        localStorage.setItem("token", dados.token);
        localStorage.setItem("usuario", JSON.stringify(dados.user));
    }

    const limparSessao = () => {
        usuario.value = null;
        token.value = null;
        localStorage.removeItem("token");
        localStorage.removeItem("usuario");
    }

    const login = async (email, password) => {
        const response = await api.post("/login", {email, password});
        salvarSessao(response.data);
    }

    //Cadastro: a API já devolve o token, então o usuário entra direto
    const register = async (dados) => {
        const response = await api.post("/register", dados);
        salvarSessao(response.data);
    }

    const logout = async () => {
        try{
            await api.post("/logout");
        }finally{
            limparSessao();
        }
    }

    return { usuario, token, isAuthenticated, login, register, logout }
});
