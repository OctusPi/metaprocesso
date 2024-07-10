function load(status = true){
    const element = document.getElementById('load-wall')
    if (element) {
        status ? element.classList.remove('d-none') : element.classList.add('d-none')
    }
}

function dateNow() {
    return (new Date()).toLocaleDateString()
}

function randCode(len = 12) {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
    let final = ""

    for (let i = 0; i < len; i++) {
        final += chars[Math.floor(Math.random() * chars.length)]
    }

    return final
}

function getTxt(objs, key_subject, key_search = 'id', key_txt = 'title'){
    if(objs){
        const  f = objs.find(o => o[key_search] == key_subject)
        return f ? f[key_txt] : '*****'
    }

    return '*****'
    
}

function truncate(str, len = 100) {
    if (str == null) {
        return null
    }
    
    if (str.length <= len) {
        return str
    }

    return str.slice(0, len - 3) + "..."
}

function stripHTML(str) {
    const parser = new DOMParser().parseFromString(str, 'text/html')
    return parser.body.textContent
}

function dateProtocol(pivot, separator = '') {
        if (!pivot) {
            return null
        }
        
        const d = new Date();

        const date = (
            d.getDay().toString().padStart(2, '0')
            + d.getMonth().toString().padStart(2, '0')
            + d.getFullYear().toString().padStart(4, '0'))

        const mili = d.getMilliseconds().toString().padStart(4, '0')
    
        return String(pivot).padStart(3, '0') + separator + date + separator + mili
}

export default {
    load,
    dateNow,
    randCode,
    getTxt,
    truncate,
    stripHTML,
    dateProtocol
}