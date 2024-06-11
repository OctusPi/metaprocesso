import http from "./http"

function make_payload(type, base){
    console.log(base)
    switch(type){
        case 'dfd_description':
            return `Create: Descrição sucinta de objeto de contratação de empresa especializada para fornecimento de ${base.type?.title} para ${base?.description} para atender as necessidades da ${base.unit?.title} vinculado a ${base.organ?.title}. `
        
        case 'dfd_justification':
            return `Create: Justifique a necessidade de contratação para ${base.description}`
        
        case 'dfd_quantitys':
            return `Create: jsutifique a quantidade demandada para esses itens ${base.items} de acordo com esse objeto: ${base.description} dentro dessa justificativa ${base.justification}`
        
        default:
            return null
    }
}

async function generate(url, payload, emit){

    if(payload){
        http.post(url, {payload}, emit, async (resp) => {
            return await resp.data?.choices[0]
        })
    }

    return ''

}

export default {
    make_payload,
    generate
}