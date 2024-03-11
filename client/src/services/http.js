import axsi from '@/services/axsi'
import utils from '@/utils/utils'

function request(opt, resp, emit){
    utils.load()

    axsi(opt).then(res => {
        if(res.data){
            resp(res.data, emit)
            return
        }

        emit('callAlert', {show: true, data:{type:'darger', msg: 'Falha ao receber dados...'}})

    }).catch((error) => {
        console.log(error.message)
        emit('callAlert', {show: true, data:{type:'darger', msg: 'Falha ao receber dados, verifique sua conexão...'}})
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

export default {
    request,
    response
}