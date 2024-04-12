import axsi from './axsi'
import forms from './forms'
import utils from '@/utils/utils'

async function request(opt, emit, customresp = null){
    utils.load()

    await axsi.axiosInstanceAuth.request(opt).then(response => {
        if(response){
            if(customresp && success(response)){ customresp(response, emit) }
            defresp(response, emit)
            return
        }

        emit('callAlert', {show: true, data:{type:'danger', msg: 'Falha ao receber dados...'}})
        
    }).catch((error) => {
        console.log(error.message)
        const resperror = customresp ?? defresp
        emit('callAlert', {show: true, data:error?.response?.data?.notify ?? {type:'danger', msg:'Que feio servidor, não faz assim!'}})
        resperror(error?.response, emit)
        
    }).finally(() => {
        utils.load(false)
    })
}

function defresp(resp, emit){

    const data = resp.data

    //call redirect
    if(data.redirect){
        window.location = data.redirect
    }

     //call notifys
    if(data?.notify){
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
        data : forms.builddata(data),
        headers:{
            'Content-Type':'application/json',
        }
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

function destroy (url, data, emit, resp= null){
    const opt = {
        url: url,
        method: 'POST',
        data : forms.builddata(data)
    }
    
    request(opt, emit, resp)
}

function success(response){
    return response?.status === 200 || response?.status === 201 || response?.status === 202
}

export default {
    request,
    defresp,
    post,
    put,
    patch,
    get,
    destroy,
    success
}