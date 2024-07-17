import forms from './forms'
import notifys from '@/utils/notifys';

class PseudoData {
    constructor(page, emit, ui, mountCb) {
        this.mountCb = mountCb
        this.page = page
        this.emit = emit
        this.ui = ui

        this.list()
    }

    save = (over = null) => {
        const data = Object.assign(this.page.value.data, this.mountCb(this.page.value.data))
        const validation = forms.checkform(data, this.page.value.rules);

        if (!validation.isvalid) {
            this.emit('callAlert', notifys.warning(validation.message))
            return
        }

        if (over) {
            for (let k in over) {
                data[k] = over[k]
            }
        }

        if (data.id) {
            const index = this.page.value.datalist.findIndex(
                ({ id }) => id == data.id)
                
            if (index != -1) {
                this.page.value.datalist[index] = Object.assign(this.page.value.datalist[index], data)
            }

            this.list()
            return
        }

        const lastEl = this.page.value.datalist[this.page.value.datalist.length - 1]
        this.page.value.datalist.push({ id: (lastEl?.id ?? 0) + 1, ...this.page.value.data })

        this.list()
    }

    update = (id) => {
        const instance = this.page.value.datalist.find(
            (item) => item.id == id
        )
        this.page.value.data = instance
        this.ui.toggle('update')
    }

    remove = (id) => {
        this.page.value.datalist = this.page.value.datalist.filter((item) => item.id != id);
        this.list()
    }

    list = () => {
        this.ui.toggle('list')
    }
}

export default PseudoData