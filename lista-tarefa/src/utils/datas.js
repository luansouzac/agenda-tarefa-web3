//A API devolve "AAAA-MM-DD" ou "AAAA-MM-DD HH:MM:SS"; trabalhamos só com o dia
export const diaDe = (data) => (data ?? '').slice(0, 10);

const isoLocal = (d) => {
    const mes = String(d.getMonth() + 1).padStart(2, '0');
    const dia = String(d.getDate()).padStart(2, '0');
    return `${d.getFullYear()}-${mes}-${dia}`;
}

export const hoje = () => isoLocal(new Date());

export const amanha = () => {
    const d = new Date();
    d.setDate(d.getDate() + 1);
    return isoLocal(d);
}

export const formatarDia = (dia) => {
    const [ano, mes, d] = dia.split('-').map(Number);
    return new Date(ano, mes - 1, d).toLocaleDateString('pt-BR', {
        weekday: 'long', day: '2-digit', month: 'long'
    });
}
