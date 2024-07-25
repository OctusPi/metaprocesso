import http from './http';
import forms from './forms'
import notifys from '@/utils/notifys';
class Data {

    constructor(page, emit, ui) {
        this.page = page
        this.emit = emit
        this.ui = ui
    }

    save = (over = null) => {
        const validation = forms.checkform(this.page.value.data, this.page.value.rules);
        if (!validation.isvalid) {
            this.emit('callAlert', notifys.warning(validation.message))
            return
        }

        let data = { ...this.page.value.data }

        //overwriting data values
        if (over) {
            data = Object.assign(data, over)
        }

        http.post(`${this.page.value.baseURL}/save`, data, this.emit, () => {
            this.list();
        })
    }

    update = (id) => {

        http.get(`${this.page.value.baseURL}/details/${id}`, this.emit, (response) => {
            this.page.value.data = response.data
            this.ui.toggle('update')
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
        http.post(`${this.page.value.baseURL}/list`, this.page.value.search, this.emit, (response) => {
            this.page.value.datalist = response.data ?? []
            console.log(response.data)
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