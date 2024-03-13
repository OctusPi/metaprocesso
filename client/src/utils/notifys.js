
function notify(type, msg){
    return { show:true, data:{type: type, msg:msg} }
}

function success(msg){
    return notify('success', msg)
}

function warning(msg){
    return notify('warning', msg)
}

function danger(msg){
    return notify('danger', msg)
}

export default {
    success,
    warning,
    danger
}