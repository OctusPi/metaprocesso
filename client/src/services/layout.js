import { reactive } from "vue"
import Data from "./data"
import storeOrgan from '@/stores/organ';

export default {
    new: (emit, preloaded = {}) => {
        
        const organ = storeOrgan.get_organ()

        const page = reactive({
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

        const data = new Data(page, emit)

        return [page, data]
    },
}