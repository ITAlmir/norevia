<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import SeoHead from '@/Components/SeoHead.vue'
</script>

<template>
  <PublicLayout>
    <SeoHead
      title="About | Norevia"
      description="Norevia builds lightweight Windows tools focused on performance, stability, and a clean user experience."
    />

    <div class="max-w-5xl mx-auto px-4 py-10">
      <!-- Hero -->
      <div class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white/70 dark:bg-slate-900/40 backdrop-blur p-8">
        <p class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">
          About
        </p>
        <h1 class="mt-2 text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-slate-100">
          Built to keep Windows fast, clean, and predictable.
        </h1>
        <p class="mt-4 text-slate-600 dark:text-slate-300 leading-relaxed max-w-3xl">
          Norevia is an independent software studio focused on lightweight, performance-oriented tools for Windows users.
          Our mission is simple: solve real problems without bloat, intrusive background services, or confusing UI.
        </p>

        <div class="mt-6 flex flex-wrap gap-2">
          <span class="px-3 py-1 rounded-full text-xs border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300">
            Lightweight tools
          </span>
          <span class="px-3 py-1 rounded-full text-xs border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300">
            Transparency-first
          </span>
          <span class="px-3 py-1 rounded-full text-xs border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300">
            Gamer & creator friendly
          </span>
        </div>
      </div>

      <!-- Grid -->
      <div class="mt-8 grid gap-4 md:grid-cols-2">
        <section class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/30 p-6">
          <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">What we build</h2>
          <p class="mt-2 text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
            Tools that improve everyday performance and reduce friction — without “magic buttons” that do unknown things.
          </p>
          <ul class="mt-4 space-y-2 text-sm text-slate-600 dark:text-slate-300">
            <li class="flex gap-2">
              <span class="mt-1 h-1.5 w-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>
              Startup control and system hygiene
            </li>
            <li class="flex gap-2">
              <span class="mt-1 h-1.5 w-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>
              Diagnostics, monitoring, and lightweight utilities
            </li>
            <li class="flex gap-2">
              <span class="mt-1 h-1.5 w-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>
              Practical helpers for gamers and creators
            </li>
          </ul>
        </section>

        <section class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/30 p-6">
          <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Our principles</h2>
          <p class="mt-2 text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
            If a setting changes system behavior, you should know exactly what it does and how to revert it.
          </p>
          <div class="mt-4 grid gap-3">
            <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
              <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Minimal overhead</div>
              <div class="mt-1 text-sm text-slate-600 dark:text-slate-300">
                No unnecessary background tasks or hidden services.
              </div>
            </div>
            <div class="rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
              <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Transparent changes</div>
              <div class="mt-1 text-sm text-slate-600 dark:text-slate-300">
                Clear explanations, safe defaults, and reversible actions.
              </div>
            </div>
          </div>
        </section>
      </div>

      <!-- Callout -->
      <section class="mt-8 rounded-3xl border border-slate-200 dark:border-slate-700 bg-white/70 dark:bg-slate-900/40 backdrop-blur p-6">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Get in touch</h2>
        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
          Want to collaborate, suggest a feature, or report a bug? Visit the Support page or send us an email.
        </p>

        <div class="mt-4 flex flex-wrap gap-3">
          <a
            href="/pages/support"
            class="inline-flex items-center justify-center rounded-2xl px-4 py-2 text-sm font-semibold
                   bg-slate-900 text-white hover:bg-slate-800
                   dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100"
          >
            Support
          </a>

          <a
            href="/pages/contact"
            class="inline-flex items-center justify-center rounded-2xl px-4 py-2 text-sm font-semibold
                   border border-slate-200 hover:border-slate-300
                   dark:border-slate-700 dark:hover:border-slate-600
                   text-slate-900 dark:text-slate-100"
          >
            Contact
          </a>
        </div>
      </section>
    </div>
  </PublicLayout>
</template>