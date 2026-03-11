<script setup>
import { computed } from 'vue'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import { Link } from '@inertiajs/vue3'
import SeoHead from '@/Components/SeoHead.vue'
import { makeMeta, siteUrl } from '@/Utils/seo'
import ContentBlocks from '@/Components/ContentBlocks.vue'
import AdSense from '@/Components/AdSense.vue'

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

const jsonLd = {
  '@context': 'https://schema.org',
  '@graph': [
    {
      '@type': 'BreadcrumbList',
      itemListElement: [
        { '@type': 'ListItem', position: 1, name: 'Home', item: siteUrl('/') },
        { '@type': 'ListItem', position: 2, name: 'Blog', item: siteUrl('/blog') },
        { '@type': 'ListItem', position: 3, name: props.page.topic, item: siteUrl(`/blog/${props.page.topic}`) },
        { '@type': 'ListItem', position: 4, name: props.page.title, item: siteUrl(path) },
      ],
    },
    {
      '@type': 'BlogPosting',
      headline: title,
      description: desc,
      mainEntityOfPage: siteUrl(path),
      url: siteUrl(path),
      datePublished: props.page.published_at || props.page.created_at,
      dateModified: props.page.updated_at,
      author: { '@type': 'Organization', name: 'Norevia' },
      publisher: { '@type': 'Organization', name: 'Norevia' },
    },
  ],
}

const formatDate = (value) => {
  if (!value) return ''
  return new Date(value).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const wrapperClass = computed(() => {
  const w =
    props.page.layout === 'minimal' ? 'max-w-4xl'
    : props.page.layout === 'classic' ? 'max-w-5xl'
    : props.page.layout === 'magazine' ? 'max-w-7xl'
    : props.page.layout === 'hero' ? 'max-w-6xl'
    : 'max-w-5xl'

  return `${w} mx-auto px-4`
})

const lightCard = 'bg-white/80 text-slate-900 border border-slate-200'
const darkCard = 'dark:bg-slate-900/20 dark:text-slate-100 dark:border-slate-800'

const lightHeader = 'bg-white/80 border-slate-200'
const darkHeader = 'dark:bg-slate-900/20 dark:border-slate-800'

const theme = computed(() => {
  switch (props.page.layout) {
    case 'minimal':
      return {
        headerBg: `${lightHeader} ${darkHeader}`,
        title: 'text-3xl md:text-4xl font-semibold tracking-tight text-slate-900 dark:text-white',
        meta: 'text-slate-600 dark:text-slate-400',
        card: 'bg-transparent border-transparent p-0',
        prose: 'prose max-w-none prose-slate dark:prose-invert',
        titleHero: 'text-4xl md:text-5xl font-extrabold leading-tight text-white',
        metaHero: 'text-slate-200/80',
      }

    case 'classic':
      return {
        headerBg: `${lightHeader} ${darkHeader}`,
        title: 'text-3xl md:text-4xl font-bold text-slate-900 dark:text-slate-100',
        meta: 'text-slate-600 dark:text-slate-400',
        card: 'border-slate-200 bg-white hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-950/40 dark:hover:bg-slate-900/40',
        prose: 'prose max-w-none prose-slate dark:prose-invert',
        titleHero: 'text-4xl md:text-5xl font-extrabold leading-tight text-white',
        metaHero: 'text-slate-200/80',
      }

    case 'magazine':
      return {
        headerBg: `${lightHeader} ${darkHeader}`,
        title: 'text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900 dark:text-white',
        meta: 'text-slate-600 dark:text-slate-400',
        card: `${lightCard} ${darkCard} p-6 md:p-8`,
        prose: 'prose max-w-none prose-slate dark:prose-invert',
        titleHero: 'text-4xl md:text-5xl font-extrabold leading-tight text-white',
        metaHero: 'text-slate-200/80',
      }

    case 'hero':
      return {
        headerBg: `${lightHeader} ${darkHeader}`,
        title: 'text-4xl md:text-6xl font-black leading-tight text-slate-900 dark:text-white',
        meta: 'text-slate-600 dark:text-slate-400',
        card: `${lightCard} ${darkCard} p-6 md:p-8`,
        prose: 'prose max-w-none prose-slate dark:prose-invert',
        titleHero: 'text-4xl md:text-6xl font-black leading-tight text-white',
        metaHero: 'text-slate-200/80',
      }

    default:
      return {
        headerBg: `${lightHeader} ${darkHeader}`,
        title: 'text-3xl font-bold text-slate-900 dark:text-white',
        meta: 'text-slate-600 dark:text-slate-400',
        card: `${lightCard} ${darkCard} p-6 md:p-8`,
        prose: 'prose max-w-none prose-slate dark:prose-invert',
        titleHero: 'text-4xl md:text-5xl font-extrabold leading-tight text-white',
        metaHero: 'text-slate-200/80',
      }
  }
})

const relatedClass = (index) => {
  const variants = [
    'bg-white dark:bg-slate-900/30',
    'bg-slate-50 dark:bg-slate-900/50',
    'bg-blue-50/60 dark:bg-slate-900/30',
    'bg-violet-50/60 dark:bg-slate-900/50',
  ]
  return variants[index % variants.length]
}
</script>

<template>
  <PublicLayout>
    <SeoHead :seo="seo" :jsonLd="jsonLd" />

    <div :class="wrapperClass" class="py-10">
      <!-- Breadcrumb -->
      <div class="text-sm text-slate-600 dark:text-slate-400">
        <Link class="hover:underline" href="/blog">Blog</Link>
        <span class="text-slate-400 dark:text-slate-600"> / </span>
        <Link class="hover:underline" :href="`/blog/${page.topic}`">{{ page.topic }}</Link>
      </div>

      <div class="mt-4 rounded-3xl border p-6 md:p-10 bg-white/70 border-slate-200 text-slate-900 dark:bg-black/20 dark:border-slate-800 dark:text-slate-100">
        <div :class="wrapperClass">
          <!-- HERO LAYOUT -->
          <section v-if="page.layout === 'hero'" class="mb-10">
            <div class="relative rounded-3xl overflow-hidden border border-slate-200 dark:border-slate-800">
              <img
                v-if="page.featured_image"
                :src="page.featured_image"
                :alt="page.title"
                class="w-full h-[30rem] object-cover"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent"></div>

              <div class="absolute top-5 left-5 z-10">
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold border bg-white/90 border-slate-300 text-slate-700 dark:bg-slate-900/70 dark:border-slate-700 dark:text-slate-300">
                  {{ page.topic }}
                </span>
              </div>

              <div class="absolute bottom-0 left-0 right-0 p-6 md:p-10">
                <h1 :class="theme.titleHero">{{ page.title }}</h1>

                <div :class="`mt-3 text-sm flex flex-wrap gap-x-3 gap-y-1 ${theme.metaHero}`">
                  <span>By Norevia Team</span>
                  <span v-if="page.published_at || page.created_at">• {{ formatDate(page.published_at || page.created_at) }}</span>
                </div>

                <p v-if="page.meta_description" class="mt-4 max-w-3xl text-slate-200/90 text-base md:text-lg">
                  {{ page.meta_description }}
                </p>

                <p v-if="page.image_caption" class="mt-3 text-slate-200/70 text-sm">
                  {{ page.image_caption }}
                </p>
              </div>
            </div>
          </section>

          <!-- NON-HERO HEADER -->
          <header v-else :class="`mb-8 p-6 rounded-2xl border ${theme.headerBg}`">
            <div class="flex flex-col gap-4">
              <div class="flex flex-wrap items-center gap-3">
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold border bg-white border-slate-300 text-slate-700 dark:bg-slate-900/30 dark:border-slate-700 dark:text-slate-300">
                  {{ page.topic }}
                </span>

                <span v-if="page.published_at || page.created_at" class="text-sm text-slate-500 dark:text-slate-400">
                  {{ formatDate(page.published_at || page.created_at) }}
                </span>
              </div>

              <div>
                <h1 :class="theme.title">{{ page.title }}</h1>

                <p
                  v-if="page.meta_description"
                  class="mt-4 text-base md:text-lg text-slate-600 dark:text-slate-400"
                >
                  {{ page.meta_description }}
                </p>
              </div>
            </div>
          </header>

          <div class="my-8">
            <AdSense ad-slot="6645163613" />
          </div>

          <!-- MAGAZINE LAYOUT -->
          <div v-if="page.layout === 'magazine'" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <main class="lg:col-span-8">
              <ContentBlocks
                v-if="page.blocks && page.blocks.length"
                :blocks="page.blocks"
                :content="page.content"
                :cardClass="theme.card"
                :proseClass="theme.prose"
              />

              <div
                v-else
                class="rounded-2xl border border-slate-200 bg-white/90 p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/30"
              >
                <div class="prose prose-lg max-w-none prose-slate dark:prose-invert whitespace-pre-line" v-html="page.content"></div>
              </div>
            </main>

            <aside class="lg:col-span-4">
              <div class="rounded-2xl border border-slate-200 bg-white/80 p-5 dark:border-slate-800 dark:bg-slate-900/20">
                <div class="text-sm font-semibold text-slate-900 dark:text-white">In this article</div>
                <div class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                  Read the full guide and explore more posts from this topic below.
                </div>
              </div>
            </aside>
          </div>

          <!-- CLASSIC / MINIMAL / DEFAULT -->
          <div v-else>
            <div v-if="page.featured_image && page.layout !== 'hero'" class="mb-8">
              <img
                :src="page.featured_image"
                :alt="page.title"
                class="w-full h-72 md:h-96 object-cover rounded-2xl border border-slate-200 dark:border-slate-800"
              />
              <p v-if="page.image_caption" class="text-center text-slate-500 dark:text-slate-400 mt-3 text-sm">
                {{ page.image_caption }}
              </p>
            </div>

            <ContentBlocks
              v-if="page.blocks && page.blocks.length"
              :blocks="page.blocks"
              :content="page.content"
              :cardClass="theme.card"
              :proseClass="theme.prose"
            />

            <div
              v-else
              class="rounded-2xl border border-slate-200 bg-white/90 p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900/30"
            >
              <div class="prose prose-lg max-w-none prose-slate dark:prose-invert whitespace-pre-line" v-html="page.content"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tools CTA -->
      <div class="mt-10 rounded-2xl border p-5 bg-gradient-to-r from-slate-100 via-white to-slate-100 border-slate-200 dark:from-slate-900/40 dark:via-slate-900/20 dark:to-slate-900/40 dark:border-slate-800">
        <div class="font-semibold text-slate-900 dark:text-white">Useful tools</div>
        <div class="mt-1 text-sm text-slate-600 dark:text-slate-400">
          Explore related utilities in Downloads: CS2, Windows, optimization, and creator helpers.
        </div>
        <div class="mt-4">
          <a
            href="/downloads"
            class="inline-flex items-center px-4 py-2 rounded-xl font-semibold transition bg-slate-900 text-white hover:bg-slate-800 dark:bg-emerald-600 dark:hover:bg-emerald-700"
          >
            Open Downloads →
          </a>
        </div>
      </div>

      <!-- Related -->
      <div v-if="related?.length" class="mt-12">
        <div class="flex items-center justify-between gap-4">
          <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Related articles</h2>
          <Link :href="`/blog/${page.topic}`" class="text-sm text-slate-600 hover:underline dark:text-slate-400">
            View all →
          </Link>
        </div>

        <div class="mt-4 rounded-2xl border overflow-hidden border-slate-200 dark:border-slate-800">
          <Link
            v-for="(r, index) in related"
            :key="r.slug"
            :href="`/blog/${r.topic}/${r.slug}`"
            class="block border-b last:border-b-0 border-slate-200 dark:border-slate-800 transition hover:bg-white dark:hover:bg-slate-900/60"
            :class="relatedClass(index)"
          >
            <div class="p-5">
              <div class="text-sm font-semibold text-slate-900 dark:text-white line-clamp-2">
                {{ r.title }}
              </div>
              <div class="mt-2 text-sm text-slate-600 dark:text-slate-400 line-clamp-2">
                {{ r.meta_description || '' }}
              </div>
            </div>
          </Link>
        </div>
      </div>
    </div>
  </PublicLayout>
</template>