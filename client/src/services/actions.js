export default class Actions {
    static Create(icon, title, action, modal = null) {
        return { icon, title, action, modal }
    }
    static Edit(action) {
        return Actions.Create('create-outline', 'Editar', action)
    }
    static Delete(action) {
        return Actions.Create('trash-outline', 'Remover', action, '#modalDelete')
    }
    static FastDelete(action) {
        return Actions.Create('trash-outline', 'Remover', action)
    }
    static Dowload(action) {
        return Actions.Create('download-outline', 'Download', action)
    }
    static Export(icon, action) {
        return Actions.Create(icon, 'Exportar', action)
    }
    static ModalDetails(action) {
        return Actions.Create('book-outline', 'Detalhar', action, '#modalDetails')
    }
}