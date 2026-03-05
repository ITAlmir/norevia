<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
</script>

<template>
<MainLayout>

<div class="max-w-3xl mx-auto px-4 py-16">

  <div class="rounded-3xl p-[1px]
              bg-gradient-to-r from-emerald-200 via-white to-sky-200
              dark:from-emerald-900/35 dark:via-slate-900/35 dark:to-sky-900/30">

    <div class="rounded-3xl p-8 bg-white/90 dark:bg-slate-950/50
                border border-slate-200 dark:border-slate-800 text-center">

      <div class="text-5xl mb-4">✅</div>

      <h1 class="text-3xl font-black text-slate-900 dark:text-white">
        Thank you for your purchase!
      </h1>

      <p class="mt-4 text-slate-600 dark:text-slate-300">
        Your payment was successful.
      </p>

      <p class="mt-3 text-slate-600 dark:text-slate-300">
        Your <b>PRIME license key</b> will be delivered manually
        within <b>24 hours</b> to your PayPal email address.
      </p>

      <div class="mt-6 text-sm text-slate-500 dark:text-slate-400">
        License policy: <b>1 key = 1 PC</b>
      </div>

      <!-- activation steps -->
      <div class="mt-10 text-left max-w-lg mx-auto">
        <h2 class="font-semibold text-lg text-slate-900 dark:text-white mb-3">
          How to activate your key
        </h2>

        <ol class="list-decimal pl-5 space-y-2 text-slate-600 dark:text-slate-300">
          <li>Open <b>CS2 Performance System</b></li>
          <li>Go to <b>Support</b> or <b>Optimizer</b> tab</li>
          <li>Paste your license key</li>
          <li>Click <b>Activate</b></li>
        </ol>
      </div>

<div class="mt-10 border-t border-slate-200 dark:border-slate-800 pt-6 text-center">

  <div class="text-sm text-slate-600 dark:text-slate-300">
    Want optimization tips and updates?
  </div>

  <a
    href="https://youtube.com/@NoreviaApp"
    target="_blank"
    class="inline-flex items-center gap-2 mt-3 px-4 py-2 rounded-lg
           bg-red-600 text-white text-sm font-semibold
           hover:bg-red-500 transition"
  >
    ▶ Subscribe on YouTube
  </a>

  <div class="text-xs text-slate-500 dark:text-slate-400 mt-2">
    @NoreviaApp — tutorials, updates and performance guides
  </div>

</div>

      <div class="mt-8 text-xs text-slate-400">
        If you don't receive your key within 24 hours,
        please contact support@norevia.app and include your PayPal transaction ID.
      </div>

      <!-- buttons -->
      <div class="mt-10 flex flex-wrap gap-3 justify-center">

  <a href="/downloads/cs2-performance-system"
     class="px-4 py-2 rounded-lg text-sm font-semibold text-white
            bg-gradient-to-r from-emerald-600 to-sky-600
            hover:from-emerald-500 hover:to-sky-500
            transition">
    Download App
  </a>

  <a href="/downloads"
     class="px-4 py-2 rounded-lg text-sm border
            border-slate-300 dark:border-slate-700
            text-slate-700 dark:text-slate-200
            hover:bg-slate-50 dark:hover:bg-slate-900 transition">
    Back to Downloads
  </a>

</div>

    </div>
  </div>

</div>

</MainLayout>
</template>