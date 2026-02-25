<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Link } from '@inertiajs/vue3'
import SeoHead from '@/Components/SeoHead.vue'
import { makeMeta, siteUrl } from '@/Utils/seo'

const props = defineProps({
  topic: { type: String, required: true },
  posts: { type: Object, required: true },
})

// map canonical hubova -> naslov/intro/faq
const hub = (() => {
  const t = (props.topic || '').toLowerCase()

  if (t === 'cs2') {
    return {
      title: 'CS2 Guides & Tools',
      desc: 'desc: CS2 performance guides: FPS, input lag, config and network tuning—linked with utility tools in Downloads.',
      faqs: [
        { q: 'How do I increase FPS in CS2?', a: 'Start with video settings, driver sanity, and Windows power plan. Then apply safe optimizations and test.' },
        { q: 'Does input lag tuning help?', a: 'Yes—proper fullscreen/latency settings + stable frametimes usually make the biggest difference.' },
        { q: 'Where can I get CS2 utilities?', a: 'Check Norevia Downloads → CS2 tools and optimization utilities.' },
      ],
    }
  }

  if (t === 'pc-optimization') {
    return {
      title: 'PC Optimization Guides',
      desc: 'desc: Windows 11 optimization: latency, drivers, background processes, power plans and stability—practical and measurable.',
      faqs: [
        { q: 'What is the safest Windows optimization?', a: 'Remove bloat, keep drivers stable, tune power plan, and avoid risky registry “tweaks”.' },
        { q: 'Should I disable services?', a: 'Only specific ones, and only if you understand impact. Start with measurable changes.' },
        { q: 'Any tools for optimization?', a: 'Yes—Norevia Downloads has small utilities for cleanup, monitoring and tuning.' },
      ],
    }
  }

  if (t === 'creator-tools') {
    return {
      title: 'Creator Tools & Workflow',
      desc: 'Guides and utilities for creators: recording, editing, audio, uploads and productivity—focused on fast, stable workflow.',
      faqs: [
        { q: 'Best settings for recording?', a: 'Use consistent bitrate, stable FPS, and hardware encoding when possible.' },
        { q: 'How to speed up editing?', a: 'Proxy workflow + SSD scratch + optimized export presets.' },
        { q: 'Any utilities for creators?', a: 'Yes—check Norevia Downloads for creator-focused tools.' },
      ],
    }
  }

  // default fallback
  return {
    title: `${props.topic} Hub`,
    desc: `Guides and tools for ${props.topic}.`,
    faqs: [],
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
</script>

<template>
  <PublicLayout>
    <SeoHead :seo="seo" :jsonLd="jsonLd" />
    <div class="max-w-6xl mx-auto px-4 py-10">
      <!-- Breadcrumb -->
      <div class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-400">
        <Link class="hover:underline" :href="'/blog'">Blog</Link>
        <span class="text-slate-400 dark:text-slate-600">/</span>
        <span class="font-semibold text-slate-900 dark:text-white">{{ topic }}</span>
      </div>

      <!-- Hub header -->
      <div class="mt-4 rounded-3xl border p-6 md:p-8
                  bg-slate-100/70 border-slate-200
                  dark:bg-slate-900/30 dark:border-slate-800">
        <h1 class="text-3xl md:text-4xl font-semibold text-slate-900 dark:text-white">
          {{ hub.title }}
        </h1>
        <p class="mt-3 text-slate-600 dark:text-slate-400 max-w-3xl">
          {{ hub.desc }}
        </p>

        <!-- internal linking: hub -> downloads -->
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

     <!-- Posts list -->
<div class="mt-8 grid md:grid-cols-3 gap-4">
  <Link
    v-for="p in posts.data"
    :key="p.id"
    :href="`/blog/${p.topic}/${p.slug}`"
    class="group block rounded-2xl p-[1px] transition
           bg-gradient-to-r from-slate-200 via-white to-slate-200
           dark:from-slate-800 dark:via-slate-900/40 dark:to-slate-800
           shadow-[0_12px_40px_-26px_rgba(2,6,23,0.45)]
           hover:shadow-[0_18px_55px_-30px_rgba(2,6,23,0.55)]"
  >
    <div
      class="rounded-2xl border p-4 transition
             bg-white/90 border-white/70 backdrop-blur
             dark:bg-slate-950/45 dark:border-slate-800/70
             hover:bg-white dark:hover:bg-slate-950/60"
    >
      <div class="flex gap-4">
        <!-- left content -->
        <div class="min-w-0 flex-1">
          <div class="text-xs text-slate-500 dark:text-slate-400">
            {{ new Date(p.published_at || Date.now()).toLocaleDateString('en-US', { year:'numeric', month:'short', day:'numeric' }) }}
          </div>

          <div class="mt-1 font-semibold text-slate-900 dark:text-white line-clamp-2">
            {{ p.title }}
          </div>

          <div class="mt-2 text-sm text-slate-600 dark:text-slate-400 line-clamp-3">
            {{ p.meta_description || 'Open to read the full post.' }}
          </div>

          <div class="mt-4 text-sm font-semibold text-slate-900 dark:text-slate-100">
            Read →
          </div>
        </div>

        <!-- right thumbnail -->
        <div
          class="shrink-0 h-20 w-20 rounded-xl overflow-hidden border
                 border-slate-200/80 dark:border-slate-800/80
                 bg-gradient-to-br from-slate-50 to-white
                 dark:from-slate-900/60 dark:to-slate-950/20
                 shadow-sm"
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
      </div>
    </div>
  </Link>

  <div
    v-if="!posts.data?.length"
    class="md:col-span-3 rounded-2xl border p-6
           bg-white border-slate-200 text-slate-600
           dark:bg-slate-900/20 dark:border-slate-800 dark:text-slate-400"
  >
    No posts yet for this hub. Create one in Content Admin → Pages → Create Blog.
  </div>
</div>

      <!-- FAQ (visible content, not only schema) -->
      <div v-if="hub.faqs.length" class="mt-12">
        <h2 class="text-xl font-semibold text-slate-900 dark:text-white">FAQ</h2>
        <div class="mt-4 space-y-3">
          <div
            v-for="(f, i) in hub.faqs"
            :key="i"
            class="rounded-2xl border p-4
                   bg-slate-100/70 border-slate-200
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
