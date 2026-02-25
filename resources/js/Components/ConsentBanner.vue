<script setup>
import { computed, ref, watchEffect } from 'vue'
import { useConsent, useConsentUI } from '@/Utils/consent'

const { consent, decided, setAll, setCustom } = useConsent()
const { settingsOpen, close } = useConsentUI()

const showBanner = computed(() => !decided.value)
const showModal = computed(() => settingsOpen.value)

const analytics = ref(false)
const marketing = ref(false)

watchEffect(() => {
  // Ako korisnik još nije donio odluku
  if (!consent.value.decided) {
    analytics.value = true
    marketing.value = true
  } else {
    analytics.value = !!consent.value.analytics
    marketing.value = !!consent.value.marketing
  }
})


const acceptAll = () => { setAll('accept'); close() }
const rejectAll = () => { setAll('reject'); close() }

const saveSettings = () => {
  setCustom({ analytics: analytics.value, marketing: marketing.value })
  close()
}
</script>



<template>
  <!-- Banner (prvi put) -->
  <div
    v-if="showBanner"
    class="fixed bottom-0 left-0 right-0 z-[60]"
  >
    <div class="mx-auto max-w-7xl px-4 pb-4">
      <div
        class="rounded-2xl border shadow-xl backdrop-blur
               bg-white/90 border-slate-200 text-slate-900
               dark:bg-slate-900/90 dark:border-slate-700 dark:text-slate-100"
      >
        <div class="p-4 md:p-5 flex flex-col md:flex-row md:items-center gap-4 md:gap-6">
          <div class="flex-1 text-sm">
            <div class="font-semibold">Privacy preferences</div>
            <div class="mt-1 text-slate-600 dark:text-slate-300">
              We use cookies to improve the experience. You can accept, reject, or customize analytics and marketing.
            </div>
          </div>

          <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
            <button
              type="button"
              @click="rejectAll"
              class="px-4 py-2 rounded-xl border text-sm font-semibold transition
                     bg-white border-slate-300 text-slate-800 hover:bg-slate-100
                     dark:bg-slate-800 dark:border-slate-600 dark:text-slate-100 dark:hover:bg-slate-700"
            >
              Reject
            </button>

            <button
  type="button"
  @click="settingsOpen = true"
  class="px-4 py-2 rounded-xl border text-sm font-semibold transition
         bg-slate-200 border-slate-300 text-slate-900 hover:bg-slate-300
         dark:bg-slate-800 dark:border-slate-600 dark:text-slate-100 dark:hover:bg-slate-700"
>
  Settings
</button>


            <button
              type="button"
              @click="acceptAll"
              class="px-4 py-2 rounded-xl text-sm font-semibold transition
                     bg-emerald-600 text-white hover:bg-emerald-700"
            >
              Accept all
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal (settings) -->
  <div v-if="showModal" class="fixed inset-0 z-[70]">
    <div class="absolute inset-0 bg-black/60" @click="closeModal"></div>

    <div class="relative min-h-full flex items-center justify-center p-4">
      <div
        class="w-full max-w-lg rounded-2xl border shadow-2xl
               bg-white border-slate-200 text-slate-900
               dark:bg-slate-900 dark:border-slate-700 dark:text-slate-100"
      >
        <div class="p-5">
          <div class="text-lg font-bold">Cookie settings</div>
          <div class="mt-2 text-sm text-slate-600 dark:text-slate-300">
            Choose which categories you allow. Necessary cookies are always enabled.
          </div>

          <div class="mt-5 space-y-3">
            <div class="flex items-center justify-between rounded-xl border p-4 border-slate-200 dark:border-slate-700">
              <div>
                <div class="font-semibold">Necessary</div>
                <div class="text-xs text-slate-500 dark:text-slate-400">Required for basic site functionality.</div>
              </div>
              <div class="text-xs font-semibold px-2 py-1 rounded-lg bg-slate-200 dark:bg-slate-800">
                ON
              </div>
            </div>

            <label class="flex items-center justify-between rounded-xl border p-4 cursor-pointer border-slate-200 dark:border-slate-700">
              <div>
                <div class="font-semibold">Analytics</div>
                <div class="text-xs text-slate-500 dark:text-slate-400">Helps us understand usage (e.g., Google Analytics).</div>
              </div>
              <input v-model="analytics" type="checkbox" class="h-5 w-5" />
            </label>

            <label class="flex items-center justify-between rounded-xl border p-4 cursor-pointer border-slate-200 dark:border-slate-700">
              <div>
                <div class="font-semibold">Marketing</div>
                <div class="text-xs text-slate-500 dark:text-slate-400">Personalized ads/affiliate tracking.</div>
              </div>
              <input v-model="marketing" type="checkbox" class="h-5 w-5" />
            </label>
          </div>

          <div class="mt-6 flex flex-col sm:flex-row justify-end gap-2">
            <button
              type="button"
              @click="rejectAll"
              class="px-4 py-2 rounded-xl border text-sm font-semibold transition
                     bg-white border-slate-300 text-slate-800 hover:bg-slate-100
                     dark:bg-slate-800 dark:border-slate-600 dark:text-slate-100 dark:hover:bg-slate-700"
            >
              Reject all
            </button>

            <button
              type="button"
              @click="saveSettings"
              class="px-4 py-2 rounded-xl text-sm font-semibold transition
                     bg-emerald-600 text-white hover:bg-emerald-700"
            >
              Save settings
            </button>
          </div>

          <button
  type="button"
  @click="close"
  class="mt-4 text-xs text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200"
>
  Close
</button>

        </div>
      </div>
    </div>
  </div>
</template>
