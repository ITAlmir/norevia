<template>
  <div
    class="min-h-screen
           bg-gradient-to-b from-slate-200 via-slate-100 to-slate-200 text-slate-900
           dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 dark:text-gray-100"
  >
    <SiteNavbar />

    <main class="max-w-7xl mx-auto px-4 py-8">
      <slot />
    </main>

    <SiteFooter />
    <ConsentBanner />
  </div>
</template>

<script setup>
import SiteNavbar from '@/Components/SiteNavbar.vue'
import SiteFooter from '@/Components/SiteFooter.vue'
import ConsentBanner from '@/Components/ConsentBanner.vue'

</script>
