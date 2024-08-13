import { ref } from 'vue'

const organ = ref(localStorage.getItem('organ'))

function set_organ(organValue){
  localStorage.setItem('organ', organValue)
  organ.value = organValue
}

function clear(){
  localStorage.removeItem('organ')
}

export default {
  organ:organ.value,
  set_organ,
  clear
}