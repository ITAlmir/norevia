<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Link } from '@inertiajs/vue3'
import SeoHead from '@/Components/SeoHead.vue'
import { makeMeta, siteUrl } from '@/Utils/seo'
import AdSense from '@/Components/AdSense.vue'

const props = defineProps({
  topic: { type: String, required: true },
  posts: { type: Object, required: true },
})

const hub = (() => {
  const t = (props.topic || '').toLowerCase()

  if (t === 'cs2') {
    return {
      title: 'CS2 Guides & Tools',
      desc: 'CS2 performance guides: FPS, input lag, config and network tuning—linked with utility tools in Downloads.',
      faqs: [
        { q: 'How do I increase FPS in CS2?', a: 'Start with video settings, driver sanity, and Windows power plan. Then apply safe optimizations and test.' },
        { q: 'Does input lag tuning help?', a: 'Yes—proper fullscreen and latency settings plus stable frametimes usually make the biggest difference.' },
        { q: 'Where can I get CS2 utilities?', a: 'Check Norevia Downloads for CS2 tools and optimization utilities.' },
      ],
      accent: 'from-cyan-500/15 via-blue-500/10 to-slate-100 dark:from-cyan-500/10 dark:via-blue-500/10 dark:to-slate-900/20',
    }
  }

  if (t === 'pc-optimization') {
    return {
      title: 'PC Optimization Guides',
      desc: 'Windows 11 optimization: latency, drivers, background processes, power plans and stability—practical and measurable.',
      faqs: [
        { q: 'What is the safest Windows optimization?', a: 'Remove bloat, keep drivers stable, tune power plan, and avoid risky registry tweaks.' },
        { q: 'Should I disable services?', a: 'Only specific ones, and only if you understand their impact. Start with measurable changes.' },
        { q: 'Any tools for optimization?', a: 'Yes—Norevia Downloads has small utilities for cleanup, monitoring and tuning.' },
      ],
      accent: 'from-emerald-500/15 via-lime-500/10 to-slate-100 dark:from-emerald-500/10 dark:via-lime-500/10 dark:to-slate-900/20',
    }
  }

  if (t === 'creator-tools') {
    return {
      title: 'Creator Tools & Workflow',
      desc: 'Guides and utilities for creators: recording, editing, audio, uploads and productivity—focused on a fast, stable workflow.',
      faqs: [
        { q: 'Best settings for recording?', a: 'Use consistent bitrate, stable FPS, and hardware encoding when possible.' },
        { q: 'How to speed up editing?', a: 'Use proxy workflow, SSD scratch space, and optimized export presets.' },
        { q: 'Any utilities for creators?', a: 'Yes—check Norevia Downloads for creator-focused tools.' },
      ],
      accent: 'from-fuchsia-500/15 via-pink-500/10 to-slate-100 dark:from-fuchsia-500/10 dark:via-pink-500/10 dark:to-slate-900/20',
    }
  }

  return {
    title: `${props.topic} Hub`,
    desc: `Guides and tools for ${props.topic}.`,
    faqs: [],
    accent: 'from-slate-200 via-slate-100 to-white dark:from-slate-800 dark:via-slate-900/30 dark:to-slate-900/20',
  }
})()

const path = `/blog/${props.topic}`
const seo = makeMeta({
  title: `${hub.title} | Norevia`,
  description: hub.desc,
  path,
  type: 'website',
})

const jsonLd = {
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "BreadcrumbList",
      "itemListElement": [
        { "@type": "ListItem", "position": 1, "name": "Home", "item": siteUrl("/") },
        { "@type": "ListItem", "position": 2, "name": "Blog", "item": siteUrl("/blog") },
        { "@type": "ListItem", "position": 3, "name": props.topic, "item": siteUrl(path) },
      ],
    },
    ...(hub.faqs.length ? [{
      "@type": "FAQPage",
      "mainEntity": hub.faqs.map(f => ({
        "@type": "Question",
        "name": f.q,
        "acceptedAnswer": { "@type": "Answer", "text": f.a },
      })),
    }] : []),
  ],
}

const postRowClass = (index) => {
  const variants = [
    'bg-white dark:bg-slate-900/30',
    'bg-slate-50 dark:bg-slate-900/50',
    'bg-blue-50/60 dark:bg-slate-900/30',
    'bg-emerald-50/60 dark:bg-slate-900/50',
  ]
  return variants[index % variants.length]
}
</script>

<template>
  <PublicLayout>
    <SeoHead :seo="seo" :jsonLd="jsonLd" />

    <div class="max-w-6xl mx-auto px-4 py-10">
      <!-- Breadcrumb -->
      <div class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-400">
        <Link class="hover:underline" href="/blog">Blog</Link>
        <span class="text-slate-400 dark:text-slate-600">/</span>
        <span class="font-semibold text-slate-900 dark:text-white">{{ topic }}</span>
      </div>

      <!-- Header -->
      <div class="mt-4 rounded-3xl border overflow-hidden border-slate-200 dark:border-slate-800">
        <div class="p-6 md:p-8 bg-gradient-to-r" :class="hub.accent">
          <h1 class="text-3xl md:text-4xl font-bold tracking-tight text-slate-900 dark:text-white">
            {{ hub.title }}
          </h1>

          <p class="mt-3 max-w-3xl text-slate-600 dark:text-slate-400">
            {{ hub.desc }}
          </p>

          <div class="mt-5 flex flex-wrap gap-2">
            <a
              href="/downloads"
              class="px-4 py-2 rounded-xl border text-sm font-medium transition
                     bg-white border-slate-300 text-slate-900 hover:bg-slate-100
                     dark:bg-slate-900/20 dark:border-slate-700 dark:text-slate-100 dark:hover:bg-slate-900/40"
            >
              Related downloads →
            </a>

            <a
              href="/blog"
              class="px-4 py-2 rounded-xl border text-sm font-medium transition
                     bg-white border-slate-300 text-slate-900 hover:bg-slate-100
                     dark:bg-slate-900/20 dark:border-slate-700 dark:text-slate-100 dark:hover:bg-slate-900/40"
            >
              All topics →
            </a>
          </div>
        </div>

        <div class="rounded-3xl border border-slate-800 bg-slate-950/40 p-4">
          <AdSense ad-slot="6645163613" />
        </div>
      </div>

      <!-- Posts list -->
      <div class="mt-6 rounded-2xl border overflow-hidden border-slate-200 dark:border-slate-800">
        <Link
          v-for="(p, index) in posts.data"
          :key="p.id"
          :href="`/blog/${p.topic}/${p.slug}`"
          class="group block border-b last:border-b-0 border-slate-200 dark:border-slate-800 transition hover:bg-white dark:hover:bg-slate-900/60"
          :class="postRowClass(index)"
        >
          <div class="p-5 md:p-6 flex items-start gap-4 md:gap-6">
            <div
              class="shrink-0 h-20 w-20 rounded-xl overflow-hidden border
                     border-slate-200 dark:border-slate-800
                     bg-slate-100 dark:bg-slate-900/40"
            >
              <img
                v-if="p.featured_image"
                :src="p.featured_image"
                alt=""
                class="h-full w-full object-cover transition-transform duration-200 group-hover:scale-[1.03]"
                loading="lazy"
              />
              <div v-else class="h-full w-full flex items-center justify-center text-xl">
                📄
              </div>
            </div>

            <div class="min-w-0 flex-1">
              <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 dark:text-slate-400">
                <span class="uppercase tracking-wide">{{ p.topic }}</span>
                <span>•</span>
                <span>
                  {{ new Date(p.published_at || Date.now()).toLocaleDateString('en-US', { year:'numeric', month:'short', day:'numeric' }) }}
                </span>
              </div>

              <div class="mt-2 text-xl font-semibold text-slate-900 dark:text-white group-hover:underline">
                {{ p.title }}
              </div>

              <div class="mt-2 text-sm text-slate-600 dark:text-slate-400 line-clamp-2">
                {{ p.meta_description || 'Open to read the full post.' }}
              </div>
            </div>

            <div class="hidden md:block shrink-0 text-sm font-semibold text-slate-900 dark:text-slate-100">
              Read →
            </div>
          </div>
        </Link>

        <div
          v-if="!posts.data?.length"
          class="p-6 bg-white text-slate-600 dark:bg-slate-900/20 dark:text-slate-400"
        >
          No posts yet for this hub. Create one in Content Admin → Pages → Create Blog.
        </div>
      </div>

      <!-- FAQ -->
      <div v-if="hub.faqs.length" class="mt-12">
        <h2 class="text-xl font-semibold text-slate-900 dark:text-white">FAQ</h2>
        <div class="mt-4 grid md:grid-cols-3 gap-4">
          <div
            v-for="(f, i) in hub.faqs"
            :key="i"
            class="rounded-2xl border p-5
                   bg-white border-slate-200
                   dark:bg-slate-900/30 dark:border-slate-800"
          >
            <div class="font-semibold text-slate-900 dark:text-white">{{ f.q }}</div>
            <div class="mt-2 text-sm text-slate-600 dark:text-slate-400">{{ f.a }}</div>
          </div>
        </div>
      </div>
    </div>
  </PublicLayout>
  </template>