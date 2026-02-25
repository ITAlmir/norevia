<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Link } from '@inertiajs/vue3'
import SeoHead from '@/Components/SeoHead.vue'
import { makeMeta, siteUrl } from '@/Utils/seo'
import ContentBlocks from '@/Components/ContentBlocks.vue'

const props = defineProps({
  page: { type: Object, required: true },
  related: { type: Array, default: () => [] },
})

const title = props.page.meta_title || props.page.title
const desc = props.page.meta_description || ''
const path = `/blog/${props.page.topic}/${props.page.slug}`

const seo = makeMeta({
  title: `${title} | Norevia Blog`,
  description: desc,
  path,
  type: 'article',
})

// JSON-LD: BlogPosting + BreadcrumbList
const jsonLd = {
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "BreadcrumbList",
      "itemListElement": [
        { "@type": "ListItem", "position": 1, "name": "Home", "item": siteUrl("/") },
        { "@type": "ListItem", "position": 2, "name": "Blog", "item": siteUrl("/blog") },
        { "@type": "ListItem", "position": 3, "name": props.page.topic, "item": siteUrl(`/blog/${props.page.topic}`) },
        { "@type": "ListItem", "position": 4, "name": props.page.title, "item": siteUrl(path) },
      ],
    },
    {
      "@type": "BlogPosting",
      "headline": title,
      "description": desc,
      "mainEntityOfPage": siteUrl(path),
      "url": siteUrl(path),
      "datePublished": props.page.published_at || props.page.created_at,
      "dateModified": props.page.updated_at,
      "author": { "@type": "Organization", "name": "Norevia" },
      "publisher": { "@type": "Organization", "name": "Norevia" },
    },
  ],
}
</script>

<template>
  <PublicLayout>
    <SeoHead :seo="seo" :jsonLd="jsonLd" />

    <div class="max-w-3xl mx-auto px-4 py-10">
      <div class="text-sm text-slate-600 dark:text-slate-400">
        <Link class="hover:underline" href="/blog">Blog</Link>
        <span class="text-slate-400 dark:text-slate-600"> / </span>
        <Link class="hover:underline" :href="`/blog/${page.topic}`">{{ page.topic }}</Link>
      </div>

      <h1 class="text-3xl md:text-4xl font-semibold mt-3 text-slate-900 dark:text-white">
        {{ page.title }}
      </h1>

      <!-- Content -->
<div class="mt-6">
  <ContentBlocks
    v-if="page.blocks && page.blocks.length"
    :blocks="page.blocks"
    :content="page.content"
    cardClass="border border-slate-200 bg-white/70 dark:border-slate-800 dark:bg-slate-900/20"
    proseClass="prose max-w-none prose-slate dark:prose-invert"
  />

  <div v-else class="prose max-w-none prose-slate dark:prose-invert">
    <div v-html="page.content"></div>
  </div>
</div>

      <!-- Internal linking CTA -> Downloads -->
      <div class="mt-10 rounded-2xl border p-5
                  bg-slate-100/70 border-slate-200
                  dark:bg-slate-900/30 dark:border-slate-800">
        <div class="font-semibold text-slate-900 dark:text-white">Useful tools</div>
        <div class="mt-1 text-sm text-slate-600 dark:text-slate-400">
          Explore related utilities in Downloads (CS2, Windows, optimization).
        </div>
        <div class="mt-4">
          <a
            href="/downloads"
            class="inline-flex items-center px-4 py-2 rounded-xl font-semibold transition
                   bg-slate-900 text-white hover:bg-slate-800
                   dark:bg-emerald-600 dark:hover:bg-emerald-700"
          >
            Open Downloads →
          </a>
        </div>
      </div>

      <!-- Related -->
      <div v-if="related?.length" class="mt-12">
        <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Related</h2>
        <div class="mt-3 grid md:grid-cols-2 gap-3">
          <Link
            v-for="r in related"
            :key="r.slug"
            class="rounded-2xl border p-4 transition
                   bg-white border-slate-200 hover:bg-slate-50
                   dark:bg-slate-900/20 dark:border-slate-800 dark:hover:bg-slate-900/40"
            :href="`/blog/${r.topic}/${r.slug}`"
          >
            <div class="font-semibold text-slate-900 dark:text-white line-clamp-2">{{ r.title }}</div>
            <div class="mt-2 text-sm text-slate-600 dark:text-slate-400 line-clamp-2">
              {{ r.meta_description || '' }}
            </div>
          </Link>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>
