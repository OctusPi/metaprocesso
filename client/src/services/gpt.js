import http from "./http"

async function generate(url, payload, emit, call){
    if(payload && call){
        http.post(url, {payload}, emit, call)
    }
}


export default {
    generate
}