import axsi from './axsi'
import forms from './forms'
import utils from '@/utils/utils'

async function request(opt, emit, resp = null){
    utils.load()
    const rsp = resp ?? response

    await axsi.request(opt).then(res => {
        if(res.data){
            rsp(res.data, emit)
            return
        }

        emit('callAlert', {show: true, data:{type:'danger', msg: 'Falha ao receber dados...'}})

    }).catch((error) => {
        console.log(error.message)
        emit('callAlert', {show: true, data:error?.response?.data?.notify})
    }).finally(() => {
        utils.load(false)
    })
}

function response(data, emit){
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
        data : forms.buildata(data)
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

export default {
    request,
    response,
    post,
    get
}