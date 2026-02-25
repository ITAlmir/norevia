<template>
  <footer class="mt-16 border-t border-slate-300/70 dark:border-slate-700">
    <div class="max-w-7xl mx-auto px-4 py-10">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 text-sm">
        <!-- Left links -->
        <nav class="flex flex-wrap gap-x-4 gap-y-2">
          <a href="/gaming" class="text-slate-700 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">Gaming</a>
          <a href="/tools" class="text-slate-700 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">Tools</a>
          <a href="/apps" class="text-slate-700 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">Apps</a>
          <a href="/downloads" class="text-slate-700 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">Downloads</a>
          <a href="/blog" class="text-slate-700 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">Blog</a>

          <span class="mx-1 text-slate-300 dark:text-slate-700">•</span>

          <a href="/pages/privacy" class="text-slate-700 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">Privacy</a>
          <a href="/pages/terms" class="text-slate-700 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">Terms</a>
        </nav>

        <!-- Right meta -->
        <div class="text-xs text-slate-500 dark:text-slate-400 flex flex-wrap items-center gap-2">
          <span>© {{ year }} Norevia. All rights reserved.</span>
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
import { useConsentUI } from '@/Utils/consent'
const { open: openPrivacy } = useConsentUI()
const year = new Date().getFullYear()
</script>
