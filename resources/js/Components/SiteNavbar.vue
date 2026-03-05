<template>
  <nav
    class="bg-slate-100/80 backdrop-blur border-b border-slate-300
           dark:bg-slate-900/80 dark:border-slate-700"
  >
    <div class="max-w-7xl mx-auto px-6 py-4">
  <!-- TOP ROW (mobile): logo left, actions right -->
  <div class="flex items-center gap-3">
    <!-- LOGO -->
    <Link href="/" class="text-xl font-bold tracking-widest text-blue-800 dark:text-blue-400 shrink-0">
      Nor<span class="text-red-700 dark:text-red-400">EVIA</span>
    </Link>

    <!-- desktop NAV center -->
    <div class="hidden md:flex flex-1 justify-center gap-10 text-sm font-semibold tracking-wide">
      <Link href="/" :class="navClass('/')">HOME</Link>
      <Link href="/downloads" :class="navClass('/downloads')">DOWNLOADS</Link>
      <Link href="/blog" :class="navClass('/blog')">BLOG</Link>
    </div>

    <!-- RIGHT side (mobile + desktop) -->
    <div class="ml-auto flex items-center gap-2">
      <!-- AUTH (desktop only) -->
      <div class="hidden md:flex items-center gap-3">
        <Link
          v-if="canLogin"
          href="/login"
          class="text-sm font-bold text-cyan-700 hover:text-blue-600"
        >
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
      </div>

      <!-- THEME -->
      <button
        type="button"
        @click="toggleTheme"
        class="px-3 py-2 rounded-xl border text-sm font-medium transition
               bg-slate-200 text-slate-800 border-slate-300 hover:bg-slate-300
               dark:bg-slate-800 dark:text-slate-100 dark:border-slate-700 dark:hover:bg-slate-700"
      >
        <span v-if="isDark" class="flex items-center gap-2">☀️</span>
        <span v-else class="flex items-center gap-2">🌙</span>
      </button>

      <!-- MOBILE MENU BUTTON (hamburger) -->
      <button
        @click="mobileOpen = !mobileOpen"
        class="md:hidden p-2 rounded-lg border
               bg-slate-200 border-slate-300 text-slate-700
               dark:bg-slate-800 dark:border-slate-700 dark:text-slate-200"
        aria-label="Open menu"
      >
        <span v-if="!mobileOpen">☰</span>
        <span v-else>✕</span>
      </button>
    </div>
  </div>

  <!-- SECOND ROW (mobile): SEARCH full width -->
  <div class="mt-3 md:mt-0 md:hidden">
    <div class="w-full relative">
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
        aria-label="Search"
      >
        🔍
      </button>
    </div>
  </div>

  <!-- SEARCH (desktop only) -->
  <div class="hidden md:block w-56 relative shrink-0 mt-0">
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
      aria-label="Search"
    >
      🔍
    </button>
  </div>
</div>
  </nav>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { useTheme } from '@/Utils/theme'

const page = usePage()
const { isDark, toggleTheme } = useTheme()

const mobileOpen = ref(false)
const searchQuery = ref('')

// Ziggy canLogin/canRegister comes via page props in your app
const canLogin = computed(() => page.props?.canLogin ?? true)
const canRegister = computed(() => page.props?.canRegister ?? true)

function performSearch() {
  if (!searchQuery.value.trim()) return
  router.get('/search', { q: searchQuery.value }, { preserveState: true, replace: true })
}

function performSearchMobile() {
  performSearch()
  mobileOpen.value = false
}

function navClass(path) {
  const url = page.url || ''
  const active = path === '/' ? url === '/' : url.startsWith(path)
  return active
    ? 'text-slate-900 dark:text-white underline underline-offset-4'
    : 'text-slate-700 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white'
}
</script>
