<template>
  <PublicLayout :seo="seo">
    <div :class="wrapperClass">
      <article class="mx-auto max-w-5xl px-4 py-10">
        <!-- Breadcrumb -->
        <nav class="mb-6 flex flex-wrap items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
          <a href="/blog" class="transition hover:text-slate-900 hover:underline dark:hover:text-white">
            Blog
          </a>
          <span>/</span>
          <a
            v-if="page.topic"
            :href="`/blog/${page.topic}`"
            class="transition hover:text-slate-900 hover:underline dark:hover:text-white"
          >
            {{ formatTopic(page.topic) }}
          </a>
          <span v-if="page.topic">/</span>
          <span class="text-slate-700 dark:text-slate-200 line-clamp-1">
            {{ page.title }}
          </span>
        </nav>

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
            <span>By Almir Redzic / Norevia</span>
            <span v-if="page.published_at">• Published {{ formatDate(page.published_at) }}</span>
            <span v-if="showUpdatedDate">• Updated {{ formatDate(page.updated_at) }}</span>
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

        <!-- Quick nav / trust row -->
        <div class="mb-8 grid gap-4 md:grid-cols-2">
          <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-950/40">
            <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">
              Continue exploring
            </div>

            <div class="mt-3 flex flex-wrap gap-2">
              <a
                v-if="page.topic"
                :href="`/blog/${page.topic}`"
                class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-800 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-900"
              >
                Back to {{ formatTopic(page.topic) }} →
              </a>

              <a
                href="/blog"
                class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-800 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-900"
              >
                All blog topics →
              </a>

              <a
                href="/downloads"
                class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-800 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-900"
              >
                Related downloads →
              </a>
            </div>
          </div>

          <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-800 dark:bg-slate-950/40">
            <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">
              About this guide
            </div>
            <p class="mt-3 text-sm leading-6 text-slate-600 dark:text-slate-400">
              Norevia publishes practical guides focused on gaming performance, Windows optimization,
              and creator workflows. Articles are written to be useful, measurable, and easy to apply
              in real setups.
            </p>
          </div>
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

        <!-- Author / editorial note -->
        <section class="mt-10">
          <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-950/40">
            <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">
              Editorial note
            </div>

            <h2 class="mt-2 text-xl font-bold tracking-tight text-slate-900 dark:text-white">
              Practical performance and workflow content
            </h2>

            <p class="mt-3 text-sm leading-7 text-slate-600 dark:text-slate-400">
              This article is part of the Norevia knowledge hub for {{ formatTopic(page.topic) }}.
              The goal is to provide practical steps, cleaner structure, and useful follow-up resources
              instead of generic filler content.
            </p>

            <div class="mt-4 flex flex-wrap gap-2">
              <a
                v-if="page.topic"
                :href="`/blog/${page.topic}`"
                class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-200"
              >
                Explore {{ formatTopic(page.topic) }} hub
              </a>

              <a
                href="/blog"
                class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-800 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-900"
              >
                Browse all guides
              </a>
            </div>
          </div>
        </section>

        <!-- AdSense 2 -->
        <div class="mt-10">
          <div class="rounded-3xl border border-slate-200 bg-white/95 p-4 shadow-sm dark:border-slate-800 dark:bg-slate-950/40">
            <AdSense />
          </div>
        </div>

        <!-- Related -->
        <section v-if="related?.length" class="mt-14 border-t border-slate-200 pt-10 dark:border-slate-800">
          <div class="flex items-center justify-between gap-4">
            <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">
              Related posts
            </h2>

            <a
              v-if="page.topic"
              :href="`/blog/${page.topic}`"
              class="text-sm font-semibold text-slate-600 transition hover:text-slate-900 hover:underline dark:text-slate-400 dark:hover:text-white"
            >
              More from {{ formatTopic(page.topic) }} →
            </a>
          </div>

          <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <a
              v-for="r in related"
              :key="`${r.topic}-${r.slug}`"
              :href="`/blog/${r.topic}/${r.slug}`"
              class="group rounded-2xl border border-slate-200 bg-white p-5 transition hover:-translate-y-0.5 hover:shadow-md dark:border-slate-800 dark:bg-slate-900"
            >
              <div class="flex items-start gap-4">
                <div
                  class="h-20 w-24 shrink-0 overflow-hidden rounded-xl border border-slate-200 bg-slate-100 dark:border-slate-800 dark:bg-slate-950/50"
                >
                  <img
                    v-if="r.featured_image"
                    :src="r.featured_image"
                    :alt="r.title"
                    class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.04]"
                    loading="lazy"
                  />
                  <div
                    v-else
                    class="flex h-full w-full items-center justify-center text-xl text-slate-400 dark:text-slate-600"
                  >
                    📄
                  </div>
                </div>

                <div class="min-w-0 flex-1">
                  <div class="text-lg font-bold text-slate-900 group-hover:underline dark:text-white">
                    {{ r.title }}
                  </div>

                  <div class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                    {{ formatTopic(r.topic) }}
                    <span v-if="r.published_at">• {{ formatDate(r.published_at) }}</span>
                  </div>

                  <div
                    v-if="r.meta_description || r.excerpt"
                    class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-400 line-clamp-3"
                  >
                    {{ r.meta_description || r.excerpt }}
                  </div>
                </div>
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

const showUpdatedDate = computed(() => {
  if (!props.page?.updated_at || !props.page?.published_at) return false

  const published = new Date(props.page.published_at).getTime()
  const updated = new Date(props.page.updated_at).getTime()

  if (Number.isNaN(published) || Number.isNaN(updated)) return false

  return updated > published + 24 * 60 * 60 * 1000
})
</script>