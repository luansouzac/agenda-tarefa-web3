//Cada categoria recebe uma cor fixa do tema, pela posição na lista
const paleta = ['info', 'secondary', 'roxo', 'rosa', 'primary'];

export const corCategoria = (categorias, idcategoria) => {
    const i = categorias.findIndex(c => c.idcategoria == idcategoria);
    return paleta[(i < 0 ? 0 : i) % paleta.length];
}
