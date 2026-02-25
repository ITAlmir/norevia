<!-- resources/js/Layouts/GuestLayout.vue -->
<template>
  <div class="min-h-screen bg-gradient-to-b from-slate-800 to-gray-900 text-gray-100 flex flex-col items-center pt-16">
    <!-- LOGO -->
    <div class="mb-8">
      <a href="/" class="text-3xl font-bold tracking-widest text-blue-400">
        Nor<span class="text-red-400">EVIA</span>
      </a>
    </div>

    <!-- AUTH CARD -->
    <div class="w-full max-w-md px-6 py-8 bg-slate-800/50 shadow-lg rounded-xl border border-slate-700">
      <slot />
    </div>

    <!-- BACK TO HOME -->
    <div class="mt-8">
      <a href="/" class="text-sm text-gray-400 hover:text-gray-300">
        ← Back to home
      </a>
    </div>
  </div>
</template>

<script setup>
// Prazno layout - bez route() koristenja
</script>