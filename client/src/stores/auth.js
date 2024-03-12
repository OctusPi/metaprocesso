import { ref } from 'vue'
import { defineStore } from 'pinia'

export const useJwt = defineStore('auth', () => {
  const token = ref(localStorage.getItem('token'))
  const user = ref(localStorage.getItem('user'))

  function setToken(tokenValue){
    localStorage.setItem('token', tokenValue)
    token.value = tokenValue
  }

  function setUser(userValue){
    localStorage.setItem('user', JSON.stringify(userValue))
    user.value = userValue
  }

  function getUser(){
    try {
      return JSON.parse(user);
  } catch (e) {
      console.log('Fail parse string to JSON')
      return {}
  }
  }

  return {
    token,
    user,
    setToken,
    setUser,
    getUser
  }
})
