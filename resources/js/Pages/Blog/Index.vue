<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { computed } from 'vue'


const props = defineProps({
  topics: Array,
  latest: Array,
})


// Canonical hubs (your strategic pillars)
const hubs = [
  {
    key: 'cs2',
    title: 'CS2 Performance',
    desc: 'Launch options, FPS, input lag, network tweaks, and competitive settings.',
    href: '/blog/cs2',
    icon: '🎯',
  },
  {
    key: 'pc-optimization',
    title: 'PC Optimization',
    desc: 'Windows 11 tuning, latency, drivers, power plans, and performance guides.',
    href: '/blog/pc-optimization',
    icon: '⚡',
  },
  {
    key: 'creator-tools',
    title: 'Creator Tools',
    desc: 'Recording, editing, audio, upload workflow, and productivity for creators.',
    href: '/blog/creator-tools',
    icon: '🎬',
  },
]

// Only show blog topics you actually want in the blog UI
const allowedTopics = new Set(['gaming', 'cs2', 'pc-optimization', 'creator-tools'])

const visibleTopics = computed(() =>
  (props.topics || []).filter(t => allowedTopics.has(String(t)))
)

const latestGaming = computed(() =>
  (props.latest || []).filter(p => p?.page_type === 'gaming' && p?.topic)
)


// fallback ako meta_description fali
const excerpt = (p) => (p?.meta_description || p?.excerpt || '').toString()
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
      <!-- Header -->
      <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
          <h1 class="text-3xl md:text-4xl font-semibold text-slate-900 dark:text-white">Norevia Blog</h1>
        </div>

        <!-- quick links -->
        <div class="flex gap-2">
          <a
            href="/downloads"
            class="px-4 py-2 rounded-xl border text-sm font-medium transition
                   bg-slate-200 border-slate-300 text-slate-900 hover:bg-slate-300
                   dark:bg-slate-800 dark:border-slate-700 dark:text-slate-100 dark:hover:bg-slate-700"
          >
            Downloads
          </a>
        </div>
      </div>

      <!-- HUB LIST -->
<div class="mt-8 rounded-2xl border p-5
            bg-white border-slate-200
            dark:bg-slate-900/20 dark:border-slate-800">
  <div class="flex items-center justify-between gap-4">
    <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Start with a hub</h2>
    <span class="text-sm text-slate-500 dark:text-slate-400">Canonical entry points</span>
  </div>

  <div class="mt-4 divide-y divide-slate-200 dark:divide-slate-800">
    <a
      v-for="h in hubs"
      :key="h.key"
      :href="h.href"
      class="group flex items-start gap-4 py-4"
    >
      <div class="text-2xl leading-none mt-0.5">{{ h.icon }}</div>

      <div class="flex-1">
        <div class="flex items-center justify-between gap-3">
          <div class="text-base font-semibold text-slate-900 dark:text-white group-hover:underline">
            {{ h.title }}
          </div>

          <span class="text-[11px] px-2 py-0.5 rounded-full border
                       border-slate-300 text-slate-700
                       dark:border-slate-700 dark:text-slate-300">
            Hub
          </span>
        </div>

        <div class="mt-1 text-sm text-slate-600 dark:text-slate-400">
          {{ h.desc }}
        </div>

        <div class="mt-2 text-sm font-medium text-slate-900 dark:text-slate-100 group-hover:underline">
          Open hub →
        </div>
      </div>
    </a>
  </div>

  <!-- Optional: show only selected topics as small pills -->
  <div class="mt-4 flex flex-wrap gap-2">
    <Link
      v-for="t in visibleTopics"
      :key="'topic-' + t"
      class="text-sm px-3 py-1 rounded-full border transition
             bg-slate-50 border-slate-300 text-slate-800 hover:bg-slate-100
             dark:bg-slate-900/10 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-900/30"
      :href="`/blog/${t}`"
    >
      {{ t }}
    </Link>
  </div>
</div>


      <!-- LATEST POSTS -->
      <div class="mt-10">
  <div class="flex items-center justify-between">
    <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Latest</h2>
    <a href="/blog/cs2" class="text-sm text-slate-600 hover:underline dark:text-slate-400">Browse hubs →</a>
  </div>

  <div class="grid md:grid-cols-3 gap-4 mt-4">
    <Link
      v-for="p in latestGaming"
      :key="p.id"
      class="block rounded-2xl border p-4 transition
             bg-white border-slate-200 hover:bg-slate-50
             dark:bg-slate-900/20 dark:border-slate-800 dark:hover:bg-slate-900/40"
      :href="p.topic ? `/blog/${p.topic}/${p.slug}` : `/pages/${p.slug}`"
    >
      <div class="flex items-center justify-between gap-3">
        <div class="text-xs text-slate-500 dark:text-slate-400 truncate">
          {{ p.topic }}
        </div>
        <span
          class="text-[11px] px-2 py-0.5 rounded-full border
                 border-slate-300 text-slate-700
                 dark:border-slate-700 dark:text-slate-300"
        >
          Gaming
        </span>
      </div>

      <div class="mt-2 font-semibold text-slate-900 dark:text-white line-clamp-2">
        {{ p.title }}
      </div>

      <div class="mt-2 text-sm text-slate-600 dark:text-slate-400 line-clamp-3">
        {{ excerpt(p) || 'Read more…' }}
      </div>

      <div class="mt-4 text-sm font-medium text-slate-900 dark:text-slate-100">
        Read →
      </div>
    </Link>
  </div>

  <div v-if="!latestGaming.length" class="mt-6 text-slate-600 dark:text-slate-400">
    No gaming posts yet.
  </div>
</div>


      <!-- INTERNAL LINKING: CTA to Downloads -->
      <div class="mt-12 rounded-2xl border p-6
                  bg-slate-100/70 border-slate-200
                  dark:bg-slate-900/30 dark:border-slate-800">
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
