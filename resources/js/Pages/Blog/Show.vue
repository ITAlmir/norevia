<template>
  <PublicLayout :seo="seo">
    <div :class="wrapperClass">
      <article class="mx-auto max-w-5xl px-4 py-10">
        <!-- Header -->
        <div class="mb-8">
          <div class="text-sm font-medium uppercase tracking-[0.18em] text-sky-500 dark:text-sky-400">
            {{ formatTopic(page.topic) }}
          </div>

          <h1 class="mt-3 text-3xl md:text-5xl font-black tracking-tight text-slate-900 dark:text-white">
            {{ page.title }}
          </h1>

          <p
            v-if="page.meta_description"
            class="mt-4 text-lg leading-8 text-slate-600 dark:text-slate-400"
          >
            {{ page.meta_description }}
          </p>

          <div class="mt-5 flex flex-wrap gap-3 text-sm text-slate-500 dark:text-slate-400">
            <span>By Norevia</span>
            <span v-if="page.published_at">• {{ formatDate(page.published_at) }}</span>
          </div>
        </div>

        <!-- Featured image -->
        <div v-if="page.featured_image" class="mb-10">
          <img
            :src="page.featured_image"
            :alt="page.title"
            class="w-full rounded-3xl border border-slate-200 object-cover shadow-sm dark:border-slate-800"
          />
          <p
            v-if="page.image_caption"
            class="mt-3 text-center text-sm text-slate-500 dark:text-slate-400"
          >
            {{ page.image_caption }}
          </p>
        </div>

        <!-- AdSense 1 -->
        <div class="mb-8">
          <div class="rounded-3xl border border-slate-200 bg-white/95 p-4 shadow-sm dark:border-slate-800 dark:bg-slate-950/40">
            <AdSense />
          </div>
        </div>

        <!-- Content -->
        <div :class="contentShellClass">
          <ContentBlocks
            :blocks="page.blocks"
            :content="page.content"
            :cardClass="theme.card"
            :proseClass="theme.prose"
          />
        </div>

        <!-- AdSense 2 -->
        <div class="mt-10">
          <div class="rounded-3xl border border-slate-200 bg-white/95 p-4 shadow-sm dark:border-slate-800 dark:bg-slate-950/40">
            <AdSense />
          </div>
        </div>

        <!-- Related -->
        <section v-if="related?.length" class="mt-14 border-t border-slate-200 pt-10 dark:border-slate-800">
          <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">
            Related posts
          </h2>

          <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <a
              v-for="r in related"
              :key="`${r.topic}-${r.slug}`"
              :href="`/blog/${r.topic}/${r.slug}`"
              class="rounded-2xl border border-slate-200 bg-white p-5 transition hover:-translate-y-0.5 hover:shadow-md dark:border-slate-800 dark:bg-slate-900"
            >
              <div class="text-lg font-bold text-slate-900 dark:text-white">
                {{ r.title }}
              </div>
              <div class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                {{ formatTopic(r.topic) }}
              </div>
            </a>
          </div>
        </section>
      </article>
    </div>
  </PublicLayout>
</template>

<script setup>
import { computed } from 'vue'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import ContentBlocks from '@/Components/ContentBlocks.vue'
import AdSense from '@/Components/AdSense.vue'

const props = defineProps({
  page: { type: Object, required: true },
  related: { type: Array, default: () => [] },
  seo: { type: Object, default: () => ({}) },
})

const wrapperClass = computed(() => {
  const map = {
    minimal: 'bg-white text-slate-900 dark:bg-slate-950 dark:text-slate-100',
    classic: 'bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100',
    magazine: 'bg-white text-slate-900 dark:bg-slate-950 dark:text-slate-100',
    hero: 'bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100',
    editorial: 'bg-white text-slate-900 dark:bg-slate-950 dark:text-slate-100',
    story: 'bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100',
    docs: 'bg-white text-slate-900 dark:bg-slate-950 dark:text-slate-100',
  }
  return map[props.page.layout] || map.minimal
})

const contentShellClass = computed(() => {
  const map = {
    minimal: 'rounded-3xl border border-slate-200 bg-white p-6 md:p-10 dark:border-slate-800 dark:bg-slate-950',
    classic: 'rounded-3xl border border-slate-200 bg-white p-6 md:p-10 shadow-sm dark:border-slate-800 dark:bg-slate-950',
    magazine: 'rounded-3xl border border-slate-200 bg-slate-50 p-6 md:p-10 dark:border-slate-800 dark:bg-slate-900',
    hero: 'rounded-3xl border border-slate-200 bg-white p-6 md:p-10 shadow-sm dark:border-slate-800 dark:bg-slate-950',
    editorial: 'rounded-3xl border border-slate-200 bg-white p-6 md:p-12 dark:border-slate-800 dark:bg-slate-950',
    story: 'rounded-3xl border border-slate-200 bg-slate-50 p-6 md:p-12 dark:border-slate-800 dark:bg-slate-900',
    docs: 'rounded-3xl border border-slate-200 bg-white p-6 md:p-10 dark:border-slate-800 dark:bg-slate-950',
  }
  return map[props.page.layout] || map.minimal
})

const theme = computed(() => {
  return {
    card: '',
    prose: '',
  }
})

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

const formatTopic = (topic) => {
  if (!topic) return 'Blog'
  return String(topic)
    .replace(/-/g, ' ')
    .replace(/\b\w/g, (m) => m.toUpperCase())
}
</script>