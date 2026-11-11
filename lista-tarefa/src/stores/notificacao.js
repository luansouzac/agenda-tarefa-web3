import { defineStore } from "pinia";
import { ref } from "vue";

export const useNotificacaoStore = defineStore("notificacao", ()=>{
    const visivel = ref(false);
    const texto = ref('');
    const cor = ref('success');

    const mostrar = (mensagem, tipo = 'success') => {
        texto.value = mensagem;
        cor.value = tipo;
        visivel.value = true;
    }

    const sucesso = (mensagem) => mostrar(mensagem, 'success');
    const erro = (mensagem) => mostrar(mensagem, 'error');

    return { visivel, texto, cor, sucesso, erro }
});
