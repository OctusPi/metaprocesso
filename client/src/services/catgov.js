
import axios from "axios"
import utils from '@/utils/utils'
import notifys from '@/utils/notifys'

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
        this.sev_url_data = 'https://cnbs.estaleiro.serpro.gov.br/cnbs-api/servico/v1/dadosServicoPorCodigo?codigo_servico=$'
        this.sev_url_und  = 'https://cnbs.estaleiro.serpro.gov.br/cnbs-api/servico/v1/unidadeMedidaPorCodigo?codigo_servico=$'
        this.sev_url_clb  = 'https://cnbs.estaleiro.serpro.gov.br/cnbs-api/servico/v1/classificacaoContabilPorCodigo?codigo_servico=$'

    }

    getEndpoint(url, search){
        return url.repleace(search)
    }

    async searchItem(search, emit){

        const datafind = {}
        const endpoint = this.getEndpoint(this.url_search, search)
        utils.load()
        
        await axios.get(endpoint).then(resp => {
            datafind.data = resp.data
        }).catch((error) => {
            emit('callAlert', notifys.warning('Falha ao tentar localizar item!'))
            console.log(error.message)
        }).finally(() => {
            utils.load(false)
        })

        return datafind
    }
}

export default CatGov