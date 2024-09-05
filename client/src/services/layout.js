import { reactive } from "vue"
import Data from "./data"
import storeOrgan from '@/stores/organ';
import PseudoData from "./pseudodata";

function buildPage(preloaded = {}) {
    const organ = storeOrgan.get_organ()
    return reactive({
        url: "",
        organ: organ,
        organ_name: organ?.name,
        ui: { list: true },
        datalist: [],
        header: [],
        selects: {},
        data: {},
        search: {},
        rules: {},
        valids: {},
        ...preloaded
    })
}

export default {
    new: (emit, preloaded = {}) => {
        const page = buildPage(preloaded)
        const data = new Data(page, emit)

        return [page, data]
    },
    pseudo: (emit, preloaded = {}) => {
        const page = buildPage(preloaded)
        const data = new PseudoData(page, emit)

        return [page, data]
    }
}