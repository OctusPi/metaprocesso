import { ref } from 'vue'

const key_store = 'user'+import.meta.env.VITE_APP_KEY
const auth_user = ref(localStorage.getItem(key_store))

function set_user(userValue) {
  const str_value = JSON.stringify(userValue)
  localStorage.setItem(key_store, str_value)
  auth_user.value = str_value
}

function get_user() {
  try {
    return JSON.parse(auth_user.value);
  } catch (e) {
    console.log('Fail parse string to JSON')
    return null
  }
}

function clear(){
  localStorage.removeItem(key_store)
}

export default {
  set_user,
  get_user,
  clear
}