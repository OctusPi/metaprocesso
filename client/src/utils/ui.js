class Ui{
    constructor(page, target){
        this.page = page
        this.target = target
    }

    toggle(mode = null){
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
            this.page.value.title.secondary = `Insira os dados para registro de ${this.target} vinculados ao Orgãos`
        } else {
            this.page.value.title.primary = `Lista de ${this.target}`
            this.page.value.title.secondary = `Dados dos ${this.target} inseridos no sistema`
        }
    }
}

export default Ui