import forms from './forms'
import notifys from '@/utils/notifys';

class PseudoData {
    constructor(page, emit, ui) {
        this.beforeSave = function () { }
        this.afterSave = function () { }
        this.page = page
        this.emit = emit
        this.ui = ui
        this.ptrs = {};

        this.list()
    }

    setBeforeSave(callback) {
        this.beforeSave = callback
    }

    setAfterSave(callback) {
        this.afterSave = callback
    }

    #remapAfterSave() {
        this.page.value.datalist.forEach((item, i) => {
            item.id = i + 1
            item = Object.assign(item, this.afterSave(item, i))
        })
    }

    select(dataset, key, search_key, search_val) {
        const item = dataset.find((item) =>
            item[search_key] ? item[search_key] == search_val : null)
        this.page.value.selects[key] = item ? item[key] : [] 
    }

    separate(key, till) {
        return this.page.value.datalist.reduce((prev, curr, i) => {
            if (i <= till) prev[curr[key]] ? prev[curr[key]]++ : prev[curr[key]] = 1
            return prev
        }, {})
    }

    save = (over = null) => {
        const validation = forms.checkform(this.page.value.data, this.page.value.rules);

        if (!validation.isvalid) {
            this.emit('callAlert', notifys.warning(validation.message))
            return
        }

        let data = Object.assign(this.page.value.data, this.beforeSave(this.page.value.data))

        if (over) data = Object.assign(data, over)

        if (data.id) {
            const index = this.page.value.datalist.findIndex(
                ({ id }) => id == data.id)
            if (index != -1) {
                this.page.value.datalist[index] = Object.assign(this.page.value.datalist[index], data)
            }
        } else {
            data = { ...data, id: this.page.value.datalist.length + 1 }
            this.page.value.datalist.push(data)
        }

        this.#remapAfterSave()
        this.list()
    }

    update = (id, instanceCallback = function () {}) => {
        const instance = this.page.value.datalist.find(
            (item) => item.id == id)

        instanceCallback(instance)

        this.page.value.data = instance
        this.ui.toggle('update')
    }

    remove = (id) => {
        this.page.value.datalist = this.page.value.datalist.filter((item) => item.id != id);
        this.#remapAfterSave()
        this.list()
    }

    list = () => {
        this.ui.toggle('list')
    }
}

export default PseudoData