import { reactive } from "vue"
import Data from "./data"

export default {
    new: (emit, preloaded = {}) => {
        const page = reactive({
            url: "",
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