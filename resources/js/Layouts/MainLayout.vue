<!-- resources/js/Layouts/MainLayout.vue -->
<template>
  <div class="min-h-screen
            bg-gradient-to-b from-slate-200 via-slate-100 to-slate-200
            text-slate-900
            dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 dark:text-gray-100">

    <SeoHead />
    <!-- NAVBAR -->
    <nav class="bg-slate-100/80 backdrop-blur border-b border-slate-300
            dark:bg-slate-900/80 dark:border-slate-700">


      <div class="max-w-7xl mx-auto px-6 py-4 flex items-center gap-6">
        <!-- LOGO -->
        <Link href="/" class="shrink-0 flex items-center">
  <img
    src="/images/logo-norevia.png"
    alt="Norevia"
    class="h-10 w-auto object-contain"
  />
</Link>


        <!-- PUBLIC NAV + SEARCH (samo gost) -->
        <template v-if="!auth.user">
          <!-- NAV CENTER -->
          <div class="hidden md:flex flex-1 justify-center gap-10 text-sm font-semibold tracking-wide">
  <Link href="/" :class="navClass('/')">HOME</Link>
  <Link href="/downloads" :class="navClass('/downloads')">DOWNLOADS</Link>
  <Link href="/blog" :class="navClass('/blog')">BLOG</Link>

  <Link
    v-if="auth.user"
    href="/dashboard"
    :class="navClass('/dashboard')"
  >
    DASHBOARD
  </Link>
</div>


          <!-- SEARCH BAR (site) -->
          <div class="w-56 relative shrink-0">
            <input
  v-model="searchQuery"
  type="text"
  placeholder="Search site..."
  class="w-full rounded-md px-3 py-2 text-sm
         bg-white text-slate-800 border border-slate-300
         placeholder:text-slate-400
         focus:outline-none focus:ring-2 focus:ring-blue-500
         dark:bg-slate-800 dark:text-gray-200 dark:border-slate-600 dark:placeholder:text-gray-400"
  @keyup.enter="performSearch"
/>

            <button
  @click="performSearch"
  type="button"
  class="absolute right-0 top-1/2 -translate-y-1/2 p-2 rounded-md
         bg-slate-200 hover:bg-slate-300 text-slate-700 hover:text-slate-900
         dark:bg-slate-700 dark:hover:bg-slate-600 dark:text-gray-300 dark:hover:text-white transition"
>
  🔍
</button>

          </div>
        </template>

        <!-- APP SEARCH (samo ulogovan) -->
        <template v-else>
          <div class="flex-1 flex items-center justify-center gap-3">
            <select
  v-model="appSearchMode"
  class="bg-white text-slate-800 border border-slate-300 rounded-md px-3 py-2 text-sm
         focus:outline-none focus:ring-2 focus:ring-blue-500
         dark:bg-slate-800 dark:text-gray-200 dark:border-slate-600"
>
              <option value="all">All</option>
              <option value="collaborations">Collaborations</option>
              <option value="sponsors">Sponsors</option>
            </select>

            <div class="w-full max-w-md relative">
              <input
  v-model="appSearchQuery"
  type="text"
  placeholder="Search sponsors or collaborations..."
  class="w-full rounded-md px-3 py-2 text-sm
         bg-white text-slate-800 border border-slate-300
         placeholder:text-slate-400
         focus:outline-none focus:ring-2 focus:ring-blue-500
         dark:bg-slate-800 dark:text-gray-200 dark:border-slate-600 dark:placeholder:text-gray-400"
  @keyup.enter="performAppSearch"
/>

              <button
                @click="performAppSearch"
                type="button"
                class="absolute right-0 top-1/2 -translate-y-1/2 p-2 rounded-md bg-slate-700 hover:bg-slate-600 text-gray-300 hover:text-white transition"
              >
                🔍
              </button>
            </div>
          </div>
        </template>

        <!-- AUTH LINKS -->
        <div class="hidden md:flex items-center gap-4 shrink-0">
          <template v-if="auth.user?.role === 'super_admin'">
            <Link href="/super-admin" class="text-sm text-red-800 hover:text-red-700">
              Super Admin
            </Link>
          </template>

          <template v-if="auth.user">
            <Link href="/dashboard" class="text-sm text-blue-800 hover:text-blue-700">
              Dashboard
            </Link>

            <button
              type="button"
              @click="confirmLogout"
              class="text-sm text-red-700 hover:text-red-600"
            >
              Log Out
            </button>
          </template>

          <template v-else>
            <Link v-if="canLogin" href="/login" class="text-sm font-bold text-cyan-700 hover:text-blue-600">
              Log in
            </Link>
            <Link
  v-if="canRegister"
  href="/register"
  class="text-sm font-semibold px-3 py-1.5 rounded-lg border transition
         bg-emerald-600 text-white border-emerald-600 hover:bg-emerald-700
         dark:bg-emerald-500 dark:border-emerald-500 dark:hover:bg-emerald-600"
>
  Register
</Link>

          </template>
        </div>
        <button
  type="button"
  @click="toggleTheme"
  class="px-3 py-2 rounded-xl border text-sm font-medium transition
         bg-slate-200 text-slate-800 border-slate-300 hover:bg-slate-300
         dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700 dark:hover:bg-slate-700"
>
  <span v-if="isDark" class="flex items-center gap-2">
    ☀️ Light
  </span>
  <span v-else class="flex items-center gap-2">
    🌙 Dark
  </span>
</button>


      </div>
    </nav>
            <!-- MOBILE MENU BUTTON -->
<button
  @click="mobileOpen = !mobileOpen"
  class="md:hidden ml-[80%] p-3 rounded-xl border
         transition-all duration-200 active:scale-95
         backdrop-blur-sm"
  :class="mobileOpen
    ? 'bg-slate-600/90 border-red-700 text-white hover:bg-red-700 hover:shadow-[0_15px_40px_-20px_rgba(220,38,38,0.8)]'
    : 'bg-slate-600/90 border-emerald-800 text-white hover:bg-emerald-800 hover:shadow-[0_15px_40px_-20px_rgba(16,185,129,0.8)]'"
>
  <span
    class="text-sm font-extrabold tracking-wide flex items-center gap-2 transition-all duration-200"
    :class="mobileOpen ? 'scale-105' : ''"
  >
    <span v-if="!mobileOpen">☰</span>
    <span v-else>✕</span>

    {{ mobileOpen ? 'Close' : 'Menu' }}
  </span>
</button>
<!-- MOBILE NAV -->
<div
  v-if="mobileOpen"
  class="md:hidden border-b border-slate-300 dark:border-slate-700
         bg-slate-100 dark:bg-slate-900"
>
  <div class="px-6 py-5 space-y-2">
    <!-- MAIN LINKS -->
    <Link
      href="/"
      @click="mobileOpen=false"
      class="block px-4 py-3 rounded-xl text-base font-extrabold tracking-wide transition text-center
             text-blue-800 hover:text-blue-900 hover:bg-white/70
             dark:text-blue-300 dark:hover:text-blue-200 dark:hover:bg-white/5"
    >
      HOME
    </Link>

    <Link
      href="/downloads"
      @click="mobileOpen=false"
      class="block px-4 py-3 rounded-xl text-base font-extrabold tracking-wide transition text-center
             text-emerald-700 hover:text-emerald-800 hover:bg-emerald-50
             dark:text-emerald-300 dark:hover:text-emerald-200 dark:hover:bg-emerald-500/10"
    >
      DOWNLOADS
    </Link>

    <Link
      href="/blog"
      @click="mobileOpen=false"
      class="block px-4 py-3 rounded-xl text-base font-extrabold tracking-wide transition text-center
             text-fuchsia-700 hover:text-fuchsia-800 hover:bg-fuchsia-50
             dark:text-fuchsia-300 dark:hover:text-fuchsia-200 dark:hover:bg-fuchsia-500/10"
    >
      BLOG
    </Link>

    <!-- AUTH AREA -->
    <div class="pt-4 mt-4 border-t border-slate-300/70 dark:border-slate-700/70 space-y-2">
      <Link
        v-if="canLogin"
        href="/login"
        @click="mobileOpen=false"
        class="block px-4 py-3 rounded-xl text-base font-extrabold transition text-center
               bg-cyan-600 text-white
               hover:bg-cyan-700
               shadow-[0_18px_45px_-30px_rgba(16,185,129,0.85)]
               hover:-translate-y-[1px] hover:shadow-[0_26px_70px_-40px_rgba(16,185,129,0.95)]
               active:translate-y-0"
      >
        Log in
      </Link>

      <Link
        v-if="canRegister"
        href="/register"
        @click="mobileOpen=false"
        class="block px-4 py-3 rounded-xl text-base font-extrabold transition text-center
               bg-emerald-600 text-white
               hover:bg-emerald-700
               shadow-[0_18px_45px_-30px_rgba(16,185,129,0.85)]
               hover:-translate-y-[1px] hover:shadow-[0_26px_70px_-40px_rgba(16,185,129,0.95)]
               active:translate-y-0"
      >
        Register
      </Link>
    </div>
  </div>
</div>

    <!-- MAIN CONTENT -->
    <main class="max-w-7xl mx-auto px-4 py-8">
      <slot />
    </main>
      <SiteFooter />

      <!-- CONFIRM MODAL -->
  <div v-if="confirmModal.open" class="fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/70" @click="closeConfirm"></div>

    <div class="relative min-h-full flex items-center justify-center p-4">
      <div class="w-full max-w-md rounded-2xl border border-slate-700 bg-slate-900 shadow-xl">
        <div class="p-6">
          <div class="text-lg font-semibold text-white">{{ confirmModal.title }}</div>
          <div class="text-sm text-gray-400 mt-2">{{ confirmModal.message }}</div>
        </div>

        <div class="px-6 pb-6 flex justify-end gap-3">
          <button
            type="button"
            @click="closeConfirm"
            class="px-4 py-2 rounded-lg border border-slate-700 text-gray-200 hover:bg-slate-800"
          >
            Cancel
          </button>

          <button
            type="button"
            @click="confirmYes"
            class="px-4 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white"
          >
            Yes
          </button>
        </div>
      </div>
    </div>
  </div>
<ConsentBanner />


  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { usePage, router, Link } from '@inertiajs/vue3'
import SeoHead from '@/Components/SeoHead.vue'
import { useTheme } from '@/Utils/theme'
import SiteFooter from '@/Components/SiteFooter.vue'
import ConsentBanner from '@/Components/ConsentBanner.vue'


const mobileOpen = ref(false)

const page = usePage()

const consentSettingsOpen = ref(false)

// Props
const auth = computed(() => page.props.auth || {})
const canLogin = computed(() => page.props.canLogin || false)
const canRegister = computed(() => page.props.canRegister || false)

// Public site search
const searchQuery = ref('')

// App search (only for logged-in)
const appSearchMode = ref('all')
const appSearchQuery = ref('')

const { isDark, toggleTheme } = useTheme()


// Optional: if dashboard already has query params, keep input synced
onMounted(() => {
  const url = new URL(window.location.href)
  const q = url.searchParams.get('q')
  const mode = url.searchParams.get('mode')
  if (q) appSearchQuery.value = q
  if (mode) appSearchMode.value = mode
})

const navClass = (path) => {
  const url = page.url || ''
  const active = url === path || url.startsWith(path + '/')

  return active
    ? 'text-red-700 dark:text-red-400'
    : 'text-slate-700 hover:text-slate-900 dark:text-blue-300 dark:hover:text-blue-200'
}



function performSearch() {
  if (!searchQuery.value.trim()) return
  router.get('/search', { q: searchQuery.value }, { preserveState: true, replace: true })
}

// ✅ App search: go to dashboard and pass query in URL
function performAppSearch() {
  if (!appSearchQuery.value.trim()) return

  router.get('/dashboard', {
    q: appSearchQuery.value,
    mode: appSearchMode.value
  }, {
    preserveState: true,
    replace: true
  })
}

// Confirm modal (for logout)
const confirmModal = ref({
  open: false,
  title: '',
  message: '',
  action: null,
})

const openConfirm = ({ title, message, action }) => {
  confirmModal.value = { open: true, title, message, action }
}

const closeConfirm = () => {
  confirmModal.value.open = false
  confirmModal.value.action = null
}

const confirmYes = async () => {
  if (typeof confirmModal.value.action === 'function') {
    await confirmModal.value.action()
  }
  closeConfirm()
}

const doLogout = async () => {
  router.post('/logout')
}

const confirmLogout = () => {
  openConfirm({
    title: 'Log out',
    message: 'Are you sure you want to log out?',
    action: doLogout
  })
}
</script>
