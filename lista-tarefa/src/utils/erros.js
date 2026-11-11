//Extrai uma mensagem legível da resposta de erro da API
export const mensagemErro = (error, padrao = 'Ocorreu um erro inesperado') => {
    const dados = error?.response?.data;
    if(dados?.errors){
        return Object.values(dados.errors).flat()[0];
    }
    return dados?.erro ?? dados?.message ?? padrao;
}
