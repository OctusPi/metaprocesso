import { ref } from 'vue'

const theme = ref(localStorage.getItem('theme'))

function set_theme(themeValue){
  localStorage.setItem('theme', themeValue)
  theme.value = themeValue
}

function apply_theme() {
  const screen = document.getElementById('screen')
  if (screen) {
    screen.classList.remove('light')
    screen.classList.remove('dark')
    screen.classList.add(theme.value)
  }
}

function clear(){
  localStorage.removeItem('theme')
}

export default {
  theme:theme.value,
  set_theme,
  apply_theme,
  clear
}