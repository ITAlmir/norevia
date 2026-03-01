<template>
  <footer class="mt-16 border-t border-slate-300/70 dark:border-slate-700">
    <div class="max-w-7xl mx-auto px-4 py-10">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 text-sm">

        <!-- Left links -->
        <nav class="flex flex-wrap gap-x-4 gap-y-2">
          <Link :href="route('pages.about')" class="text-slate-700 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">About</Link>

          <span class="mx-1 text-slate-300 dark:text-slate-700">•</span>

          <Link :href="route('pages.contact')" class="text-slate-700 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">Contact</Link>
          <Link :href="route('pages.support')" class="text-slate-700 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">Support</Link>


          <span class="mx-1 text-slate-300 dark:text-slate-700">•</span>

          <Link href="/pages/privacy" class="text-slate-700 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">Privacy</Link>
<Link href="/pages/terms" class="text-slate-700 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">Terms</Link>
        </nav>

        <!-- Right meta -->
        <div class="text-xs text-slate-500 dark:text-slate-400 flex flex-wrap items-center gap-2">
          <span>© {{ year }} Norevia. All rights reserved.</span>
          <span class="text-slate-300 dark:text-slate-700">-</span>

          <a
            class="underline hover:text-slate-700 dark:hover:text-slate-200"
            href="mailto:norevia.team@outlook.com"
          >
            norevia.team@outlook.com
          </a>

          <span class="text-slate-300 dark:text-slate-700">-</span>

          <button
            type="button"
            @click="openPrivacy"
            class="underline hover:text-slate-700 dark:hover:text-slate-200"
          >
            Privacy settings
          </button>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { useConsentUI } from '@/Utils/consent'

const { open: openPrivacy } = useConsentUI()
const year = new Date().getFullYear()
</script>