// resources/js/app.js
import '../css/app.css';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import axios from 'axios';
import { ZiggyVue } from 'ziggy-js'
import { initTheme } from '@/Utils/theme'


// Učitaj sve stranice
const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });

axios.defaults.withCredentials = true
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
axios.defaults.baseURL = '/'
axios.defaults.withCredentials = true
axios.defaults.headers.common['Accept'] = 'application/json'


axios.defaults.withCredentials = true
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

// BITNO: eksplicitno (da nema dileme)
axios.defaults.xsrfCookieName = 'XSRF-TOKEN'
axios.defaults.xsrfHeaderName = 'X-XSRF-TOKEN'

initTheme()

createInertiaApp({
  title: (title) => (title ? `${title} | Norevia` : 'Norevia'),
  
  // resources/js/app.js - PROMIJENI resolve funkciju
resolve: (name) => {
  // normalizuj: ako dođe "Pages/Pages/Show" -> "Pages/Show"
  const cleaned = name.replace(/^Pages\//, '')

  const candidates = [
    `./Pages/${cleaned}.vue`,
    `./${name}.vue`,
    `./Pages/${name}.vue`,
  ]

  for (const key of candidates) {
    const page = pages[key]
    if (page) return page.default || page
  }

  throw new Error(`Page not found: ${name} (tried: ${candidates.join(', ')})`)
},

  setup({ el, App, props, plugin }) {
  const app = createApp({ render: () => h(App, props) })

  app.use(plugin)

  // ✅ use Ziggy routes delivered from backend (HandleInertiaRequests)
  if (props?.initialPage?.props?.ziggy) {
    app.use(ZiggyVue, props.initialPage.props.ziggy)
  }

  app.mount(el)
},


});
