import http from "./http"

function make_payload(type, base){
    switch(type){
        case 'dfd_description':
            return `Create: Estou elaborando um DFD, por favor crie a descrição sucinta do 
            objeto de contratação de uma empresa especializada para fornecimento de 
            ${base.type?.title} para ${base?.description} para atender as necessidades da 
            ${base.unit?.title} vinculado a ${base.organ?.title}. 
            Por favor gere a resposta em um único parágarfo, sem quebras de linha.`
        
        case 'dfd_justification':
            return `Create: Justifique a necessidade de contratação para ${base.description}. 
            Por favor gere a resposta em um único parágarfo, sem quebras de linha.`
        
        case 'dfd_quantitys':
            return `Create: jsutifique a quantidade demandada para esses itens ${base.items} 
            de acordo com esse objeto: ${base.description} com base nessa justificativa ${base.justification}. 
            Por favor gere a resposta em um único parágarfo, sem quebras de linha.`
        
        default:
            return null
    }
}

async function generate(url, payload, emit, call){

    if(payload && call){
        http.post(url, {payload}, emit, call)
    }
}

export default {
    make_payload,
    generate
}