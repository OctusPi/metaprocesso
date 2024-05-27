
import axios from "axios"
import utils from '@/utils/utils'

class CatGov
{
    constructor(){


        this.url_search   = 'https://cnbs.estaleiro.serpro.gov.br/cnbs-api/item/v1/hint?palavra=$'

        // material
        this.mat_url_cpmd = 'https://cnbs.estaleiro.serpro.gov.br/cnbs-api/material/v1/caracteristicaPorCodigoPdm?codigo_pdm=$&todos=true&naoRetornaSiglaUnidadeMedidaCaracteristica=false'
        this.mat_url_cmat = 'https://cnbs.estaleiro.serpro.gov.br/cnbs-api/material/v1/materialCaracteristcaValorporPDM?codigo_pdm=$'
        this.mat_url_und  = 'https://cnbs.estaleiro.serpro.gov.br/cnbs-api/material/v1/unidadeFornecimentoPorCodigoPdm?codigo_pdm=$'
        this.mat_url_clb  = 'https://cnbs.estaleiro.serpro.gov.br/cnbs-api/material/v1/classificacaoContabilPorCodigoPdm?codigo_pdm=$'

        // service
        this.ser_url_data = 'https://cnbs.estaleiro.serpro.gov.br/cnbs-api/servico/v1/dadosServicoPorCodigo?codigo_servico=$'
        this.ser_url_und  = 'https://cnbs.estaleiro.serpro.gov.br/cnbs-api/servico/v1/unidadeMedidaPorCodigo?codigo_servico=$'
        this.ser_url_clb  = 'https://cnbs.estaleiro.serpro.gov.br/cnbs-api/servico/v1/classificacaoContabilPorCodigo?codigo_servico=$'

    }

    getEndpoint(url, search){
        return url.replace('$', search)
    }

    async searchItem(search){

        const datafind = {}
        const endpoint = this.getEndpoint(this.url_search, search)
        utils.load()
        
        await axios.get(endpoint).then(resp => {
            datafind.data = resp.data
        }).catch((error) => {
            datafind.error = 'Falha ao tentar localizar itens...'
            console.log(error.message)
        }).finally(() => {
            utils.load(false)
        })

        return datafind
    }

    async getMaterials(obj){
        const datafind = {}
        const endpoints = {
            units: this.getEndpoint(this.mat_url_und, obj.codigo),
            accounting: this.getEndpoint(this.mat_url_clb, obj.codigo),
            characteristics: this.getEndpoint(this.mat_url_cpmd, obj.codigo),
            materials: this.getEndpoint(this.mat_url_cmat, obj.codigo)
        }

        datafind.obj = obj
        
        for(let i in endpoints){
            utils.load()
            await axios.get(endpoints[i]).then(resp => {
                datafind[endpoints[i]] = resp.data
            }).catch((error) => {
                datafind.error = 'Falha ao tentar localizar Materiais no CATMAT...'
                console.log(error.message)
            }).finally(() => {
                utils.load(false)
            })
        }

        return datafind
    }

    async getServices(obj){
        const datafind = {}
        const endpoints = {
            units: this.getEndpoint(this.ser_url_und, obj.codigo),
            accounting: this.getEndpoint(this.ser_url_clb, obj.codigo),
            services: this.getEndpoint(this.ser_url_data, obj.codigo)
        }

        datafind.obj = obj
        
        for(let i in endpoints){
            utils.load()
            await axios.get(endpoints[i]).then(resp => {
                datafind[endpoints[i]] = resp.data
            }).catch((error) => {
                datafind.error = 'Falha ao tentar localizar Materiais no CATSER...'
                console.log(error.message)
            }).finally(() => {
                utils.load(false)
            })
        }

        return datafind
    }

    async getItems(obj){
        return await obj.tipo === 'M' ? this.getMaterials(obj) : this.getServices(obj)
    }
}

export default CatGov