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
  localStorage.setItem('user', JSON.stringify(userValue))
  user.value = userValue
}

function setNavigation(navValue){
  localStorage.setItem('navigation', JSON.stringify(navValue))
  navigation.value = navValue
}

function getUser(){
  try {
    return JSON.parse(user.value);
  } catch (e) {
    console.log('Fail parse string to JSON')
    return {}
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
      Authorization:token.value
    }
  })

  return data;
  
}

export default {
  token:token.value,
  user:user.value,
  navigation:navigation.value,
  setToken,
  setUser,
  setNavigation,
  getUser,
  getNavigation,
  clear,
  isLoggedIn
}