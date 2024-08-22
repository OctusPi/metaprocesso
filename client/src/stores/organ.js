import { ref } from 'vue'

const key_store = 'organ'+import.meta.env.VITE_APP_KEY
const organ = ref(localStorage.getItem(key_store))

function set_organ(organValue){
  const str_value = JSON.stringify(organValue)
  localStorage.setItem(key_store, str_value)
}

function get_organ(){
  try {
    return JSON.parse(organ.value);
  } catch (e) {
    console.log('Fail parse string to JSON')
    return null
  }
}

function clear(){
  localStorage.removeItem(key_store)
}

export default {
  organ:organ.value,
  set_organ,
  get_organ,
  clear
}