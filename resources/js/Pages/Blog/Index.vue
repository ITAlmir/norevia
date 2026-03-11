<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import AdSense from '@/Components/AdSense.vue'

const props = defineProps({
  topics: Array,
  latest: Array,
})

const hubs = [
  {
    key: 'cs2',
    title: 'CS2 Performance',
    desc: 'Launch options, FPS, input lag, network tweaks, and competitive settings.',
    href: '/blog/cs2',
    icon: '🎯',
    accent: 'from-cyan-500/20 to-blue-500/20',
  },
  {
    key: 'pc-optimization',
    title: 'PC Optimization',
    desc: 'Windows 11 tuning, latency, drivers, power plans, and performance guides.',
    href: '/blog/pc-optimization',
    icon: '⚡',
    accent: 'from-emerald-500/20 to-lime-500/20',
  },
  {
    key: 'creator-tools',
    title: 'Creator Tools',
    desc: 'Recording, editing, audio, upload workflow, and productivity for creators.',
    href: '/blog/creator-tools',
    icon: '🎬',
    accent: 'from-fuchsia-500/20 to-pink-500/20',
  },
]

const allowedTopics = new Set(['gaming', 'cs2', 'pc-optimization', 'creator-tools'])

const visibleTopics = computed(() =>
  (props.topics || []).filter(t => allowedTopics.has(String(t)))
)

const latestGaming = computed(() =>
  (props.latest || []).filter(p => p?.page_type === 'gaming' && p?.topic)
)

const excerpt = (p) => (p?.meta_description || p?.excerpt || '').toString()

const rowClasses = (index) => {
  const variants = [
    'bg-slate-50/90 dark:bg-slate-900/30',
    'bg-blue-50/70 dark:bg-slate-900/50',
    'bg-emerald-50/70 dark:bg-slate-900/30',
    'bg-violet-50/70 dark:bg-slate-900/50',
  ]
  return variants[index % variants.length]
}
</script>

<template>
  <Head>
    <title>Blog</title>
    <link rel="canonical" href="/blog" />
    <meta
      name="description"
      content="Norevia Blog: CS2 performance, PC optimization, and creator tools. Practical guides that lead to real utility downloads."
    />
  </Head>

  <PublicLayout>
    <div class="max-w-6xl mx-auto px-4 py-10">
      <!-- Hero -->
      <div class="rounded-3xl border overflow-hidden
                  border-slate-200 bg-white
                  dark:border-slate-800 dark:bg-slate-900/30">
        <div class="px-6 py-8 md:px-8 md:py-10 bg-gradient-to-r from-slate-100 via-white to-slate-100 dark:from-slate-900/70 dark:via-slate-900/30 dark:to-slate-900/70">
          <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-5">
            <div>
              <div class="text-sm font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">
                Norevia
              </div>
              <h1 class="mt-2 text-3xl md:text-5xl font-bold tracking-tight text-slate-900 dark:text-white">
                Blog
              </h1>
              <p class="mt-3 max-w-2xl text-slate-600 dark:text-slate-400">
                Practical guides, clearer structure, and direct paths to useful tools.
              </p>
            </div>

            <div class="flex gap-2">
              <a
                href="/downloads"
                class="px-4 py-2.5 rounded-xl border text-sm font-semibold transition
                       bg-slate-900 border-slate-900 text-white hover:bg-slate-800
                       dark:bg-emerald-600 dark:border-emerald-600 dark:hover:bg-emerald-700"
              >
                Downloads
              </a>
            </div>
          </div>
        </div>

        <div class="px-6 pb-6 md:px-8">
          <div class="mt-1">
            <AdSense ad-slot="6645163613" />
          </div>
        </div>
      </div>

      <!-- Intro / Editorial description -->
<div class="mt-8 rounded-2xl border p-6
            border-slate-200 bg-white
            dark:border-slate-800 dark:bg-slate-900/30">

  <h2 class="text-xl font-semibold text-slate-900 dark:text-white">
    About Norevia Guides
  </h2>

  <p class="mt-3 text-slate-600 dark:text-slate-400 leading-relaxed">
    Norevia provides practical guides focused on gaming performance, PC optimization, 
    and productivity tools for content creators. Our articles are designed to help 
    players improve FPS, reduce system latency, and optimize their hardware for 
    competitive games such as Counter-Strike 2.
  </p>

  <p class="mt-3 text-slate-600 dark:text-slate-400 leading-relaxed">
    In addition to gaming optimization, Norevia also covers creator workflows 
    including gameplay recording, video editing, AI voice generation, and publishing 
    strategies. Each guide focuses on practical solutions that can help gamers and 
    creators build faster, more efficient setups.
  </p>

</div>

      <!-- Hubs -->
      <div class="mt-8">
        <div class="flex items-center justify-between gap-4">
          <h2 class="text-xl md:text-2xl font-semibold text-slate-900 dark:text-white">
            Start with a hub
          </h2>
          <span class="text-sm text-slate-500 dark:text-slate-400">Main topics</span>
        </div>

        <div class="mt-4 grid md:grid-cols-3 gap-4">
          <a
            v-for="h in hubs"
            :key="h.key"
            :href="h.href"
            class="group rounded-2xl border overflow-hidden transition
                   border-slate-200 bg-white hover:shadow-lg hover:-translate-y-0.5
                   dark:border-slate-800 dark:bg-slate-900/30"
          >
            <div
              class="h-2 bg-gradient-to-r"
              :class="h.accent"
            />
            <div class="p-5">
              <div class="flex items-center justify-between gap-3">
                <div class="text-2xl">{{ h.icon }}</div>
                <span class="text-[11px] px-2 py-1 rounded-full border
                             border-slate-300 text-slate-700
                             dark:border-slate-700 dark:text-slate-300">
                  Hub
                </span>
              </div>

              <div class="mt-4 text-lg font-semibold text-slate-900 dark:text-white group-hover:underline">
                {{ h.title }}
              </div>

              <div class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                {{ h.desc }}
              </div>

              <div class="mt-4 text-sm font-semibold text-slate-900 dark:text-slate-100">
                Open hub →
              </div>
            </div>
          </a>
        </div>

        <div class="mt-5 flex flex-wrap gap-2">
          <Link
            v-for="t in visibleTopics"
            :key="'topic-' + t"
            class="text-sm px-3 py-1.5 rounded-full border transition
                   bg-white border-slate-300 text-slate-800 hover:bg-slate-100
                   dark:bg-slate-900/20 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-900/40"
            :href="`/blog/${t}`"
          >
            {{ t }}
          </Link>
        </div>
      </div>

      <!-- Latest -->
      <div class="mt-10">
        <div class="flex items-center justify-between">
          <h2 class="text-xl md:text-2xl font-semibold text-slate-900 dark:text-white">Latest posts</h2>
          <a href="/blog/cs2" class="text-sm text-slate-600 hover:underline dark:text-slate-400">
            Browse hubs →
          </a>
        </div>

        <div class="mt-4 rounded-2xl border overflow-hidden border-slate-200 dark:border-slate-800">
          <Link
            v-for="(p, index) in latestGaming"
            :key="p.id"
            :href="p.topic ? `/blog/${p.topic}/${p.slug}` : `/pages/${p.slug}`"
            class="group block border-b last:border-b-0 border-slate-200 dark:border-slate-800 transition hover:bg-white dark:hover:bg-slate-900/60"
            :class="rowClasses(index)"
          >
            <div class="p-5 md:p-6 flex flex-col md:flex-row md:items-center gap-4 md:gap-6">
              <div class="md:w-44 shrink-0">
                <div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">
                  {{ p.topic || 'blog' }}
                </div>
                <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                  Article
                </div>
              </div>

              <div class="min-w-0 flex-1">
                <div class="text-lg font-semibold text-slate-900 dark:text-white group-hover:underline">
                  {{ p.title }}
                </div>
                <div class="mt-2 text-sm text-slate-600 dark:text-slate-400 line-clamp-2">
                  {{ excerpt(p) || 'Read more…' }}
                </div>
              </div>

              <div class="shrink-0 text-sm font-semibold text-slate-900 dark:text-slate-100">
                Read →
              </div>
            </div>
          </Link>

          <div
            v-if="!latestGaming.length"
            class="p-6 text-slate-600 dark:text-slate-400 bg-white dark:bg-slate-900/20"
          >
            No gaming posts yet.
          </div>
        </div>
      </div>

      <!-- CTA -->
      <div class="mt-12 rounded-2xl border p-6
                  bg-gradient-to-r from-slate-100 via-white to-slate-100 border-slate-200
                  dark:from-slate-900/40 dark:via-slate-900/20 dark:to-slate-900/40 dark:border-slate-800">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div>
            <div class="text-lg font-semibold text-slate-900 dark:text-white">Need tools, not only guides?</div>
            <div class="mt-1 text-slate-600 dark:text-slate-400">
              Go to Downloads: CS2 tools, Windows utilities, optimization helpers.
            </div>
          </div>
          <a
            href="/downloads"
            class="px-5 py-3 rounded-xl font-semibold transition
                   bg-slate-900 text-white hover:bg-slate-800
                   dark:bg-emerald-600 dark:hover:bg-emerald-700"
          >
            Open Downloads →
          </a>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>