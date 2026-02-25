import { ref } from 'vue'

const THEME_KEY = 'norevia_theme'
const isDark = ref(false)

export function initTheme() {
  try {
    const saved = localStorage.getItem(THEME_KEY)
    if (saved === 'dark') isDark.value = true
    else if (saved === 'light') isDark.value = false
    else {
      // default: LIGHT (po tvojoj želji)
      isDark.value = false
    }
  } catch (e) {
    isDark.value = false
  }

  applyThemeClass()
}

export function useTheme() {
  const setTheme = (mode) => {
    isDark.value = (mode === 'dark')
    try { localStorage.setItem(THEME_KEY, isDark.value ? 'dark' : 'light') } catch (e) {}
    applyThemeClass()
  }

  const toggleTheme = () => setTheme(isDark.value ? 'light' : 'dark')

  return { isDark, setTheme, toggleTheme }
}

function applyThemeClass() {
  const html = document.documentElement
  if (isDark.value) html.classList.add('dark')
  else html.classList.remove('dark')
}
