import { ref } from 'vue'
import axios from 'axios'

const token = ref(localStorage.getItem('token'))
const user = ref(localStorage.getItem('user'))
const navigation = ref(localStorage.getItem('navigation'))

function setToken(tokenValue){
    localStorage.setItem('token', tokenValue)
    token.value = tokenValue
}


function setUser(userValue){
  const str_value = JSON.stringify(userValue)
  localStorage.setItem('user', str_value)
  user.value = str_value
}

function setNavigation(navValue){
  const str_value = JSON.stringify(navValue)
  localStorage.setItem('navigation', str_value)
  navigation.value = str_value
}

function getUser(){
  try {
    return JSON.parse(user.value);
  } catch (e) {
    console.log('Fail parse string to JSON')
    return null
  }
}

function getNavigation(){
  try {
    return JSON.parse(navigation.value);
  } catch (e) {
    console.log('Fail parse string to JSON')
    return {}
  }
}

function clear(){
  localStorage.removeItem('token')
  localStorage.removeItem('user')
  localStorage.removeItem('navigation')
}

async function isLoggedIn(path){

  const {data} = await axios.get(import.meta.env.VITE_URL_API+path, {
    headers:{
      'Accept': 'application/json',
      'Authorization':'Bearer '+token.value
    }
  })

  return data;
  
}

export default {
  token: token.value,
  setToken,
  setUser,
  setNavigation,
  getUser,
  getNavigation,
  clear,
  isLoggedIn
}