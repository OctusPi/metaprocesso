import axsi from './axsi'
import forms from './forms'
import utils from '@/utils/utils'

async function request(opt, emit, resp = null){
    utils.load()

    const callresp = resp ?? response

    await axsi.axiosInstanceAuth.request(opt).then(response => {
        if(response){
            callresp(response, emit)
            return
        }

        emit('callAlert', {show: true, data:{type:'danger', msg: 'Falha ao receber dados...'}})
        
    }).catch((error) => {
        console.log(error.message)
        emit('callAlert', {show: true, data:error?.response?.data?.notify ?? {type:'danger', msg:'Que feio servidor, não faz assim!'}})
        callresp(error?.response, emit)
        
    }).finally(() => {
        utils.load(false)
    })
}

function response(resp, emit){

    const data = resp.data

    //call redirect
    if(data.redirect){
        window.location = data.redirect
    }

     //call notifys
    if(data.notify){
        emit('callAlert', {show:true, data:data.notify})
    }
}

function post (url, data, emit, resp= null){
    const opt = {
        url: url,
        method: 'POST',
        data : forms.builddata(data)
    }
    
    request(opt, emit, resp)
}

function put (url, data, emit, resp= null){
    const opt = {
        url: url,
        method: 'PUT',
        data : forms.builddata(data)
    }
    
    request(opt, emit, resp)
}

function get (url, emit, resp = null){
    const opt = {
        method: 'GET',
        url: url
    }
    request(opt, emit, resp)
}

function patch (url, data, emit, resp= null){
    const opt = {
        url: url,
        method: 'PATCH',
        data : forms.builddata(data)
    }
    
    request(opt, emit, resp)
}

function remove (url, emit, resp = null){
    const opt = {
        method: 'DELETE',
        url: url
    }
    request(opt, emit, resp)
}

export default {
    request,
    response,
    post,
    put,
    patch,
    get,
    remove
}