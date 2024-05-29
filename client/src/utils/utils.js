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

export default {
    load,
    dateNow,
    randCode
}