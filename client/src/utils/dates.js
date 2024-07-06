const months = [
    { id: '01', title: 'Janeiro' },
    { id: '02', title: 'Fevereiro' },
    { id: '03', title: 'Março' },
    { id: '04', title: 'Abril' },
    { id: '05', title: 'Maio' },
    { id: '06', title: 'Junho' },
    { id: '07', title: 'Julho' },
    { id: '08', title: 'Agosto' },
    { id: '09', title: 'Setembro' },
    { id: '10', title: 'Outubro' },
    { id: '11', title: 'Novembro' },
    { id: '12', title: 'Dezembro' }
]

function pad(numero, tamanho) {
    return String(numero).padStart(tamanho, '0');
}

function listYears() {
    const dateNow = new Date()
    const year = dateNow.getFullYear();
    const years = [];

    for (let i = year; i > (year - 3); i--) {
        years.push(i)
    }

    return years
}

function dateTxtNow() {
    const diasDaSemana = ['Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado'];
    const mesesDoAno = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];

    const dataAtual = new Date();
    const diaSemana = diasDaSemana[dataAtual.getDay()];
    const dia = pad(dataAtual.getDate(), 2);
    const mes = mesesDoAno[dataAtual.getMonth()];
    const ano = dataAtual.getFullYear();

    return `${diaSemana}, ${dia} de ${mes} de ${ano}`;
}

function getMonthNow() {
    const dateNow = new Date()
    return pad(dateNow.getMonth() + 1, 2)
}

function getYearNow() {
    const dateNow = new Date()
    return dateNow.getFullYear();
}

function createBetween(month, year) {
    const firstDay = new Date(Date.UTC(year, parseInt(month) - 1, 1))
    const nextMonth = new Date(Date.UTC(year, parseInt(month), 1))
    const lastDay = new Date(nextMonth - 1)

    return [firstDay.toISOString().split('T')[0], lastDay.toISOString().split('T')[0]]
}

function getMonthYear(date_ptbr) {

    if (date_ptbr) {
        const d = date_ptbr.split('/')
        return d ? `${months.find(m => m.id == d[1])?.title} de ${d[2]}` : null
    }

    return '*****'

}

function getYear(datetime) {
    const data = new Date(datetime);
    const options = { year: 'numeric' };
    return data.toLocaleDateString('pt-BR', options);
}

function toPtBr(datetime) {
    if (datetime != null) {
        const data = new Date(datetime);
        const options = { year: 'numeric', month: '2-digit', day: '2-digit' };
        return data.toLocaleDateString('pt-BR', options);
    }

    return null
}

function nowPtbr() {
    const date = (new Date())
        .toLocaleString('pt-BR', { timeZone: 'America/Sao_Paulo' })
        .split(', ')

    return {
        date: date[0],
        time: date[1].slice(0, 5)
    }
}

export default {
    months,
    dateTxtNow,
    listYears,
    getMonthNow,
    getYearNow,
    createBetween,
    toPtBr,
    getMonthYear,
    getYear,
    nowPtbr
}