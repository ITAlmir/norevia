<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import SeoHead from '@/Components/SeoHead.vue'

const updatedAt = 'February 28, 2026'
</script>

<template>
  <PublicLayout>
    <SeoHead
      title="Terms of Service | Norevia"
      description="Terms of Service for using Norevia website and software."
    />

    <div class="max-w-5xl mx-auto px-4 py-10">
      <div class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white/70 dark:bg-slate-900/40 backdrop-blur p-8">
        <p class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Legal</p>
        <h1 class="mt-2 text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-slate-100">
          Terms of Service
        </h1>
        <p class="mt-3 text-sm text-slate-600 dark:text-slate-300">
          Last updated: <span class="font-semibold">{{ updatedAt }}</span>
        </p>

        <p class="mt-5 text-slate-600 dark:text-slate-300 leading-relaxed max-w-3xl">
          By accessing the Norevia website or using Norevia software, you agree to these terms.
          If you do not agree, please do not use the website or software.
        </p>
      </div>

      <div class="mt-8 grid gap-4 md:grid-cols-2">
        <section class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/30 p-6">
          <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Use of the service</h2>
          <p class="mt-2 text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
            You may use Norevia tools for personal or business purposes, unless a specific tool states otherwise.
            You agree not to misuse the service, attempt unauthorized access, or interfere with normal operation.
          </p>
        </section>

        <section class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/30 p-6">
          <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Software is provided “as is”</h2>
          <p class="mt-2 text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
            Norevia software is provided without warranties of any kind. You are responsible for how you use the tools,
            including any system changes you choose to apply.
          </p>
        </section>
      </div>

      <section class="mt-4 rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/30 p-6">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Limitation of liability</h2>
        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300 leading-relaxed max-w-3xl">
          To the maximum extent permitted by law, Norevia is not liable for any indirect, incidental, special,
          or consequential damages arising from the use (or inability to use) the website or software.
        </p>
      </section>

      <section class="mt-4 rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/30 p-6">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Intellectual property</h2>
        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300 leading-relaxed max-w-3xl">
          The Norevia name, branding, website content, and software are protected by applicable laws.
          You may not copy, modify, redistribute, or resell them unless explicitly permitted.
        </p>
      </section>

      <section class="mt-4 rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/30 p-6">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Changes</h2>
        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300 leading-relaxed max-w-3xl">
          We may update these terms from time to time. Continued use of the website or software after updates
          means you accept the revised terms.
        </p>
      </section>

      <section class="mt-4 rounded-3xl border border-slate-200 dark:border-slate-700 bg-white/70 dark:bg-slate-900/40 backdrop-blur p-6">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Contact</h2>
        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
          Legal inquiries: <a class="font-semibold hover:underline text-slate-900 dark:text-slate-100" href="mailto:norevia.team@outlook.com">norevia.team@outlook.com</a>
        </p>
      </section>
    </div>
  </PublicLayout>
</template>