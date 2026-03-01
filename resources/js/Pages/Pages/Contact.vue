<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import SeoHead from '@/Components/SeoHead.vue'
</script>

<template>
  <PublicLayout>
    <SeoHead
      title="Contact | Norevia"
      description="Contact the Norevia team for general inquiries, partnerships, or product questions."
    />

    <div class="max-w-5xl mx-auto px-4 py-10">
      <div class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white/70 dark:bg-slate-900/40 backdrop-blur p-8">
        <p class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Contact</p>
        <h1 class="mt-2 text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-slate-100">
          Reach the Norevia team
        </h1>
        <p class="mt-4 text-slate-600 dark:text-slate-300 leading-relaxed max-w-3xl">
          For general inquiries, partnerships, collaborations, or website issues, you can contact us anytime.
        </p>

        <div class="mt-6 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/20 p-5">
          <div class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">Email</div>
          <a
            class="mt-1 inline-block text-xl font-extrabold text-slate-900 dark:text-slate-100 hover:underline"
            href="mailto:norevia.team@outlook.com"
          >
            norevia.team@outlook.com
          </a>
          <div class="mt-2 text-sm text-slate-600 dark:text-slate-300">
            Typical response time: <span class="font-semibold">24–72 hours</span> (business days).
          </div>
        </div>
      </div>

      <div class="mt-8 grid gap-4 md:grid-cols-2">
        <section class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/30 p-6">
          <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Partnerships</h2>
          <p class="mt-2 text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
            If you have a collaboration idea, include a short summary, links, and what success looks like.
          </p>
          <ul class="mt-4 space-y-2 text-sm text-slate-600 dark:text-slate-300">
            <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>Your organization</li>
            <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>Proposal summary</li>
            <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>Relevant links</li>
          </ul>
        </section>

        <section class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/30 p-6">
          <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Need app help?</h2>
          <p class="mt-2 text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
            For bugs and troubleshooting, use Support so we can respond faster with the right context.
          </p>
          <a
            href="/pages/support"
            class="mt-4 inline-flex items-center justify-center rounded-2xl px-4 py-2 text-sm font-semibold
                   bg-slate-900 text-white hover:bg-slate-800
                   dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100"
          >
            Go to Support
          </a>
        </section>
      </div>
    </div>
  </PublicLayout>
</template>