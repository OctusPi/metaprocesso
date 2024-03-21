import { ref } from 'vue'
import { defineStore } from 'pinia'

export const useTheme = defineStore('theme', () => {
  const theme = ref(localStorage.getItem('theme'))

  function setTheme(themeValue){
    localStorage.setItem('theme', themeValue)
    theme.value = themeValue
  }


  function clear(){
    localStorage.removeItem('theme')
  }

  return {
    theme,
    setTheme,
    clear
  }
})
