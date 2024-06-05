import utils from "@/utils/utils"
import axios from "axios"

export default class SuppliersGov {
    constructor() {
        this.endpoind = 'https://compras.dados.gov.br/fornecedores/v1/fornecedores.json?ativo=true&tipo_pessoa=PJ'
    }

    async list(nome = '') {
        utils.load()
        const obj = {}
        try {
            console.log(`${this.endpoind}&nome=${nome}`)
            const { data } = await axios.get(`${this.endpoind}&nome=${nome}`)
            obj.data = Object.values(data._embedded.fornecedores)
        } catch (err) {
            obj.error = 'Falha ao buscar fornecedores SICAF...'
        }
        utils.load(false)
        return obj
    }
}