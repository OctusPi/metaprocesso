import http from './http';
import forms from './forms'
import notifys from '@/utils/notifys';
class Data {

    constructor(page, emit, ui) {
        this.page = page
        this.emit = emit
        this.ui = ui
    }

    save = () => {
        const validation = forms.checkform(this.page.value.data, this.page.value.rules);
        if (!validation.isvalid) {
            this.emit('callAlert', notifys.warning(validation.message))
            return
        }

        const data = { ...this.page.value.data }
        const url = this.page.value.data?.id ? `${this.page.value.baseURL}/update` : `${this.page.value.baseURL}/save`
        const exec = this.page.value.data?.id ? http.put : http.post

        exec(url, data, this.emit, () => {
            this.list();
        })
    }

    update = (id) => {

        http.get(`${this.page.value.baseURL}/details/${id}`, this.emit, (response) => {
            this.page.value.data = response.data
            this.ui.toggle('update')
        })
    }

    remove = (id, onRemove = () => {}) => {
        this.emit('callRemove', {
            id: id,
            url: this.page.value.baseURL,
            search: this.page.value.search,
            cb: onRemove
        })
    }

    list = () => {
        http.post(`${this.page.value.baseURL}/list`, this.page.value.search, this.emit, (response) => {
            this.page.value.datalist = response.data ?? []
            this.ui.toggle('list')
        })
    }

    selects = (key = null, search = null) => {

        const urlselect = (key && search) ? `${this.page.value.baseURL}/selects/${key}/${search}` : `${this.page.value.baseURL}/selects`

        http.get(urlselect, this.emit, (response) => {
            this.page.value.selects = response.data
        })
    }

    download = (id, name = 'Documento') => {
        http.download(`/ordinators/download/${id}`, this.emit, (response) => {
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
}

export default Data