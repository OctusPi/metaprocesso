import { ref } from 'vue'

const key_store = 'organ'+import.meta.env.VITE_APP_KEY
const organ = ref(localStorage.getItem(key_store))

function set_organ(organValue){
  localStorage.setItem(key_store, organValue)
  organ.value = organValue
}

function clear(){
  localStorage.removeItem(key_store)
}

export default {
  organ:organ.value,
  set_organ,
  clear
}