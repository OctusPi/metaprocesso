class Ui {
    constructor(page, target, article = 'o') {
        this.page = page
        this.target = target
        this.article = article
    }

    toggle(mode = null) {
        switch (mode) {
            case 'register':
                this.page.value.uiview.search = false
                this.page.value.uiview.register = !this.page.value.uiview.register
                this.page.value.data = {}
                break;
            case 'update':
                this.page.value.uiview.search = false
                this.page.value.uiview.register = !this.page.value.uiview.register
                break;
            case 'search':
                this.page.value.uiview.search = !this.page.value.uiview.search
                this.page.value.uiview.register = false
                break;
            default:
                this.page.value.uiview.register = false
                break;
        }

        if (this.page.value.uiview.register) {
            this.page.value.title.primary = `Registro de ${this.target}`
            this.page.value.title.secondary = `Insira os dados para registro de ${this.target} vinculad${this.article}s ao(s) Orgão(s)`
        } else {
            this.page.value.title.primary = `Lista de ${this.target}`
            this.page.value.title.secondary = `Dados d${this.article}s ${this.target} inserid${this.article}s no sistema`
        }
    }
}

export default Ui