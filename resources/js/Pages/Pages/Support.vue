<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import SeoHead from '@/Components/SeoHead.vue'
</script>

<template>
  <PublicLayout>
    <SeoHead
      title="Support | Norevia"
      description="Get support for Norevia apps: troubleshooting, bug reports, and feature requests."
    />

    <div class="max-w-5xl mx-auto px-4 py-10">
      <div class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white/70 dark:bg-slate-900/40 backdrop-blur p-8">
        <p class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Support</p>
        <h1 class="mt-2 text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-slate-100">
          Help for Norevia apps
        </h1>
        <p class="mt-4 text-slate-600 dark:text-slate-300 leading-relaxed max-w-3xl">
          If you’re experiencing issues with a Norevia tool or app, email us and include the details below.
          The more context you provide, the faster we can help.
        </p>

        <div class="mt-6 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/20 p-5">
          <div class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Support email</div>
          <a
            class="mt-1 inline-block text-xl font-extrabold text-slate-900 dark:text-slate-100 hover:underline"
            href="mailto:norevia.team@outlook.com?subject=Norevia%20Support"
          >
            norevia.team@outlook.com
          </a>
          <div class="mt-2 text-sm text-slate-600 dark:text-slate-300">
<div class="mt-2 text-sm text-slate-600 dark:text-slate-300">
  Tip: Use a clear subject like <span class="font-semibold">“Startup Manager Lite – Bug”</span> or
  <span class="font-semibold">“Ping Tool – Question”</span>.
</div>          </div>
        </div>
      </div>

      <div class="mt-8 grid gap-4 md:grid-cols-2">
        <section class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/30 p-6">
          <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Bug report checklist</h2>
          <ul class="mt-4 space-y-2 text-sm text-slate-600 dark:text-slate-300">
            <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>App name + version</li>
            <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>Windows version (10/11) + 64-bit</li>
            <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>Steps to reproduce</li>
            <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>Screenshots or logs (if available)</li>
          </ul>
        </section>

        <section class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/30 p-6">
          <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Performance issues</h2>
          <p class="mt-2 text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
            If your issue is related to stability or performance, include basic hardware details:
          </p>
          <ul class="mt-4 space-y-2 text-sm text-slate-600 dark:text-slate-300">
            <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>CPU / GPU / RAM</li>
            <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>Antivirus / background tools</li>
            <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>When it happens (startup, idle, gaming)</li>
          </ul>
        </section>
      </div>

      <section class="mt-8 rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/30 p-6">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Feature requests</h2>
        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300 leading-relaxed max-w-3xl">
          We love practical suggestions. Describe the problem you want to solve, what you tried already,
          and what a “perfect solution” would look like.
        </p>
      </section>
    </div>
  </PublicLayout>
</template>