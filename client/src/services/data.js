import http from './http';
import forms from './forms'
import notifys from '@/utils/notifys';
class Data {

    constructor(page, emit) {
        this.page = page
        this.emit = emit
    }

    ui = (mode = null) => {
        switch (mode) {
            case 'register':
                this.page.value.ui.search = false
                this.page.value.ui.prepare = false
                this.page.value.ui.register = !this.page.value.ui.register
                this.page.value.data = {}
                break;
            case 'update':
                this.page.value.ui.search = false
                this.page.value.ui.register = !this.page.value.ui.register
                this.page.value.ui.prepare = false
                break;
            case 'search':
                this.page.value.ui.search = !this.page.value.ui.search
                this.page.value.ui.register = false
                this.page.value.ui.prepare = false
                break;
            case 'prepare':
                this.page.value.ui.prepare = !this.page.value.ui.prepare
                this.page.value.ui.register = false
                this.page.value.ui.search = false
                break;
            default:
                this.page.value.ui.register = false
                this.page.value.ui.prepare = false
                break;
        }
    }

    save = (over = null) => {
        const validation = forms.checkform(this.page.value.data, this.page.value.rules);
        if (!validation.isvalid) {
            this.emit('callAlert', notifys.warning(validation.message))
            return
        }

        let data = { ...this.page.value.data }

        if (over)  data = Object.assign(data, over)

        http.post(`${this.page.value.baseURL}/save`, data, this.emit, () => {
            this.list();
        })
    }

    update = (id) => {

        http.get(`${this.page.value.baseURL}/details/${id}`, this.emit, (response) => {
            this.page.value.data = response.data
            this.ui('update')
        })
    }

    remove = (id) => {
        this.emit('callRemove', {
            id: id,
            url: this.page.value.baseURL,
            search: this.page.value.search,
        })
    }

    fastremove = (id) => {
        http.destroy(`${this.page.value.baseURL}/fastdestroy`, { id }, this.emit, (resp) => {
            if (http.success(resp)) {
                this.list()
            }
        })
    }

    list = () => {
        this.page.value.search.sent = true
        http.post(`${this.page.value.baseURL}/list`, this.page.value.search, this.emit, (response) => {
            this.page.value.datalist = response.data ?? []
            this.ui('list')
        })
    }

    selects = (key = null, search = null) => {

        const urlselect = (key && search) ? `${this.page.value.baseURL}/selects/${key}/${search}` : `${this.page.value.baseURL}/selects`

        http.get(urlselect, this.emit, (response) => {
            this.page.value.selects = response.data
        })
    }

    download = (id, name = 'Documento') => {
        http.download(`${this.page.value.baseURL}/download/${id}`, this.emit, (response) => {
            if (response.headers['content-type'] !== 'application/pdf') {
                this.emit('callAlert', notifys.warning('Arquivo Indisponível'))
                return
            }
            const url = URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }))
            const link = document.createElement('a')
            link.href = url;
            link.download = `${name}-${id}.pdf`
            document.body.appendChild(link)
            link.click()
            window.URL.revokeObjectURL(url);
        })
    }

    listForSearch = (...preseted) => {
        let isFilled = false
        for (let [key, value] of Object.entries(this.page.value.search)) {
            if (value && !preseted.includes(key)) {
                this.list()
                isFilled = true
                break
            }
        }
        if (!isFilled) {
            this.emit('callAlert', notifys.warning('Preencha ao menos um campo para continuar'))
        }
    }
}

export default Data