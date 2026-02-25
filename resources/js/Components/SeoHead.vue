<!-- resources/js/Components/SeoHead.vue -->
<script setup>
import { computed } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import { siteUrl, stripHtml, clamp } from '@/Utils/seo'
import { useJsonLd } from '@/Utils/useJsonLd'

const page = usePage()

const props = defineProps({
  // optional override (preferred when you want per-page logic)
  seo: { type: Object, default: null },
  // optional JSON-LD override (for hubs, downloads, posts, etc.)
  jsonLd: { type: Object, default: null },
})

const brand = computed(() => page.props.brand || {
  site_name: 'Norevia',
  title_suffix: ' | Norevia',
  default_description: '',
})

// Detect admin-like areas where you don’t want SEO injection
const isAdmin = computed(() =>
  page.url.startsWith('/content-admin') ||
  page.url.startsWith('/super-admin') ||
  page.url.startsWith('/dashboard')
)

// Choose SEO source: props.seo -> page.props.seo -> {}
const rawSeo = computed(() => props.seo || page.props.seo || {})
const effectiveSeo = computed(() => (isAdmin.value ? {} : rawSeo.value))

const robots = computed(() => (effectiveSeo.value.robots || 'index,follow'))

const title = computed(() => {
  const t = (effectiveSeo.value.title || '').trim()
  if (!t) return brand.value.site_name
  if (t.includes(brand.value.site_name)) return t
  return t + brand.value.title_suffix
})

const description = computed(() => {
  const d = (effectiveSeo.value.description || brand.value.default_description || '').trim()
  return clamp(stripHtml(d), 160)
})

const canonical = computed(() => {
  if (effectiveSeo.value.canonical) return effectiveSeo.value.canonical
  // IMPORTANT: page.url includes query. For canonical you should usually pass explicit canonical via seo.canonical.
  return siteUrl(page.url)
})

const og = computed(() => effectiveSeo.value.og || {
  'og:title': title.value,
  'og:description': description.value,
  'og:type': 'website',
  'og:url': canonical.value,
})

const twitter = computed(() => effectiveSeo.value.twitter || {
  'twitter:card': effectiveSeo.value.image ? 'summary_large_image' : 'summary',
  'twitter:title': title.value,
  'twitter:description': description.value,
})

// JSON-LD (prefer props.jsonLd, then schemaGraph, then schema)
const jsonLd = computed(() => {
  if (props.jsonLd) return props.jsonLd
  if (effectiveSeo.value.schemaGraph) {
    return { '@context': 'https://schema.org', '@graph': effectiveSeo.value.schemaGraph }
  }
  if (effectiveSeo.value.schema) return effectiveSeo.value.schema
  return null
})

// ✅ inject JSON-LD into <head> via DOM API (no <script> in template)
useJsonLd(jsonLd, 'norevia-jsonld')
</script>

<template>
  <Head>
    <title>{{ title }}</title>
    <link rel="canonical" :href="canonical" />
    <meta name="description" :content="description" />
    <meta name="robots" :content="robots" />

    <meta v-for="(v, k) in og" :key="'og-'+k" :property="k" :content="v" />
    <meta v-for="(v, k) in twitter" :key="'tw-'+k" :name="k" :content="v" />
  </Head>
</template>
