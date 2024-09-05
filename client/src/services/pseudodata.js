import forms from './forms'
import notifys from '@/utils/notifys';

class PseudoData {
    constructor(page, emit) {
        this.beforeSave = function () { }
        this.afterSave = function () { }
        this.page = page
        this.emit = emit
        this.ptrs = {};
        this.list()
    }

    ui = (mode = null) => {
        switch (mode) {
            case 'register':
                this.page.ui.search = false
                this.page.ui.prepare = false
                this.page.ui.register = !this.page.ui.register
                this.page.valids = {}
                this.page.data = {}
                break;
            case 'update':
                this.page.ui.search = false
                this.page.ui.register = !this.page.ui.register
                this.page.ui.prepare = false
                this.page.valids = {}
                break;
            case 'search':
                this.page.ui.search = !this.page.ui.search
                this.page.ui.register = false
                this.page.ui.prepare = false
                this.page.search = {}
                break;
            case 'prepare':
                this.page.ui.prepare = !this.page.ui.prepare
                this.page.ui.register = false
                this.page.ui.search = false
                break;
            default:
                this.page.ui.register = false
                this.page.ui.prepare = false
                break;
        }
    }

    static findInRef(dataset, key, search_key, search_val) {
        const item = dataset.find((item) =>
            item[search_key] ? item[search_key] == search_val : null)
        return item ? item[key] : []
    }

    setBeforeSave(callback) {
        this.beforeSave = callback
    }

    setAfterSave(callback) {
        this.afterSave = callback
    }

    #remapAfterSave() {
        this.page.datalist.forEach((item, i) => {
            item.verb_id = i + 1
            item = Object.assign(item, this.afterSave(item, i))
        })
    }

    select(dataset, key, search_key, search_val) {
        this.page.selects[key] = PseudoData.findInRef(dataset, key, search_key, search_val)
    }

    separate(key, till) {
        return this.page.datalist.reduce((prev, curr, i) => {
            if (i <= till) prev[curr[key]] ? prev[curr[key]]++ : prev[curr[key]] = 1
            return prev
        }, {})
    }

    save = (over = null) => {
        const validation = forms.checkform(this.page.data, this.page.rules);

        if (!validation.isvalid) {
            this.emit('callAlert', notifys.warning(validation.message))
            return
        }

        let data = Object.assign(this.page.data, this.beforeSave(this.page.data))

        if (over) data = Object.assign(data, over)

        if (data.id) {
            const index = this.page.datalist.findIndex(
                ({ id }) => id == data.id)
            if (index != -1) {
                this.page.datalist[index] = Object.assign(this.page.datalist[index], data)
            }
        } else {
            data = { ...data, id: this.page.datalist.length + 1 }
            this.page.datalist.push(data)
        }

        this.#remapAfterSave()
        this.list()
    }

    update = (id, instanceCallback = function () { }) => {
        const instance = this.page.datalist.find(
            (item) => item.id == id)

        instanceCallback(instance)

        this.page.data = instance
        this.ui('update')
    }

    remove = (id) => {
        this.page.datalist = this.page.datalist.filter((item) => item.id != id);
        this.#remapAfterSave()
        this.list()
    }

    removeCascade(id, dataset, origin_key, dataset_key) {
        const item = this.page.datalist.find((x) => x.id == id)

        this.page.datalist = this.page.datalist.filter((x) => x != item);

        dataset.datalist = dataset.datalist.filter((x) => {
            if (x[dataset_key] && item[origin_key]) {
                return x[dataset_key] != item[origin_key]
            }
            return true
        })

        this.#remapAfterSave()
        this.list()
    }

    list = () => {
        this.ui('list')
    }
}

export default PseudoData