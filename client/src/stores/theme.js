import { ref } from 'vue'

const theme = ref(localStorage.getItem('theme'))

function setTheme(themeValue){
  localStorage.setItem('theme', themeValue)
  theme.value = themeValue
}

function clear(){
  localStorage.removeItem('theme')
}

export default {
  theme:theme.value,
  setTheme,
  clear
}