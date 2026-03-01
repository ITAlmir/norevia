<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import SeoHead from '@/Components/SeoHead.vue'

const updatedAt = 'February 28, 2026'
</script>

<template>
  <PublicLayout>
    <SeoHead
      title="Privacy Policy | Norevia"
      description="Learn how Norevia handles privacy, data, and cookies."
    />

    <div class="max-w-5xl mx-auto px-4 py-10">
      <div class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white/70 dark:bg-slate-900/40 backdrop-blur p-8">
        <p class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Legal</p>
        <h1 class="mt-2 text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-slate-100">
          Privacy Policy
        </h1>
        <p class="mt-3 text-sm text-slate-600 dark:text-slate-300">
          Last updated: <span class="font-semibold">{{ updatedAt }}</span>
        </p>

        <p class="mt-5 text-slate-600 dark:text-slate-300 leading-relaxed max-w-3xl">
          Norevia respects your privacy. This policy explains what we collect, why we collect it,
          and the choices you have when using our website and services.
        </p>
      </div>

      <div class="mt-8 grid gap-4 md:grid-cols-2">
        <section class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/30 p-6">
          <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">What we collect</h2>
          <ul class="mt-4 space-y-2 text-sm text-slate-600 dark:text-slate-300">
            <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>Basic technical data (browser type, device type, pages visited)</li>
            <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>Cookies/consent preferences (if enabled)</li>
            <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>Emails you send us (content + your address)</li>
          </ul>
        </section>

        <section class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/30 p-6">
          <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">How we use it</h2>
          <ul class="mt-4 space-y-2 text-sm text-slate-600 dark:text-slate-300">
            <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>To operate and improve the website</li>
            <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>To respond to support requests and inquiries</li>
            <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>To protect the service (security, abuse prevention)</li>
          </ul>
        </section>
      </div>

      <section class="mt-4 rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/30 p-6">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Sharing</h2>
        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300 leading-relaxed max-w-3xl">
          We do not sell your personal data. We may share limited technical data with infrastructure providers
          (e.g., hosting, CDN, email) strictly to operate the service, or if required by law.
        </p>
      </section>

      <section class="mt-4 rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/30 p-6">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Your choices</h2>
        <ul class="mt-4 space-y-2 text-sm text-slate-600 dark:text-slate-300">
          <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>You can adjust cookie preferences using “Privacy settings” (if available)</li>
          <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>You can contact us to request deletion of email conversations where applicable</li>
        </ul>
      </section>

      <section class="mt-4 rounded-3xl border border-slate-200 dark:border-slate-700 bg-white/70 dark:bg-slate-900/40 backdrop-blur p-6">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Contact</h2>
        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
          Privacy questions: <a class="font-semibold hover:underline text-slate-900 dark:text-slate-100" href="mailto:norevia.team@outlook.com">norevia.team@outlook.com</a>
        </p>
      </section>
    </div>
  </PublicLayout>
</template>