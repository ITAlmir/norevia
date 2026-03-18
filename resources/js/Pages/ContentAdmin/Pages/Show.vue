<template>
  <PublicLayout>
    <div class="max-w-7xl mx-auto px-4 py-6">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-8">
          <div :class="`rounded-3xl border p-4 md:p-6 ${surfaceClass}`">
            <div :class="wrapperClass">
              <!-- HERO -->
              <section v-if="page.layout === 'hero'" class="mb-10">
                <div class="relative rounded-3xl overflow-hidden border border-slate-200 dark:border-slate-800">
                  <img
                    v-if="page.featured_image"
                    :src="page.featured_image"
                    :alt="page.title"
                    class="w-full h-[24rem] md:h-[30rem] object-cover"
                  />
                  <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent"></div>

                  <div class="absolute bottom-0 left-0 right-0 p-6 md:p-10">
                    <h1 :class="theme.titleHero">{{ page.title }}</h1>

                    <div :class="`mt-3 text-sm flex flex-wrap gap-x-3 gap-y-1 ${theme.metaHero}`">
                      <span>By Norevia Team</span>
                      <span v-if="page.published_at">• {{ formatDate(page.published_at) }}</span>
                      <span v-if="typeof page.views !== 'undefined'">• {{ page.views }} views</span>
                    </div>

                    <p v-if="page.image_caption" class="mt-3 text-slate-200/80 text-sm">
                      {{ page.image_caption }}
                    </p>
                  </div>
                </div>
              </section>

              <!-- NORMAL HEADER -->
              <header
                v-else
                :class="`mb-8 p-6 rounded-3xl border ${theme.headerBg}`"
              >
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                  <div>
                    <h1 :class="theme.title">{{ page.title }}</h1>
                    <div :class="`mt-2 flex flex-wrap items-center gap-3 text-sm ${theme.meta}`">
                      <span>By Norevia Team</span>
                      <span v-if="page.published_at">• {{ formatDate(page.published_at) }}</span>
                      <span v-if="typeof page.views !== 'undefined'">• {{ page.views }} views</span>
                    </div>
                  </div>

                  <button
                    v-if="canCopy"
                    type="button"
                    @click="copyUrl"
                    :class="`px-4 py-2 rounded-xl text-sm font-medium transition-colors ${theme.btn}`"
                  >
                    Copy URL
                  </button>
                </div>
              </header>

              <!-- TOP ADSENSE SLOT -->
              <div class="my-8">
                <AdSense />
              </div>

              <!-- MAGAZINE -->
              <div v-if="page.layout === 'magazine'" class="grid grid-cols-1 gap-8">
                <main>
                  <ContentBlocks
                    :blocks="page.blocks"
                    :content="page.content"
                    :cardClass="theme.card"
                    :proseClass="theme.prose"
                  />
                </main>
              </div>

              <!-- OTHER LAYOUTS -->
              <div v-else>
                <div v-if="page.featured_image && page.layout !== 'hero'" class="mb-8">
                  <img
                    :src="page.featured_image"
                    :alt="page.title"
                    class="w-full h-72 md:h-96 object-cover rounded-3xl border border-slate-200 dark:border-slate-800"
                  />
                  <p
                    v-if="page.image_caption"
                    class="text-center text-slate-500 dark:text-slate-400 mt-3 text-sm"
                  >
                    {{ page.image_caption }}
                  </p>
                </div>

                <ContentBlocks
                  :blocks="page.blocks"
                  :content="page.content"
                  :cardClass="theme.card"
                  :proseClass="theme.prose"
                />

                <!-- SECOND ADSENSE SLOT -->
              </div>
            </div>             
          </div>
        </div>

        <!-- Optional right side space kept open for future widgets -->
        <aside class="hidden lg:block lg:col-span-4">
          <div class="sticky top-24 space-y-6">
            <div class="rounded-3xl border border-slate-200 bg-white/90 p-5 shadow-sm dark:border-slate-800 dark:bg-slate-950/40">
              <div class="text-lg font-bold text-slate-900 dark:text-white">Explore more</div>
              <p class="mt-2 text-sm leading-7 text-slate-600 dark:text-slate-400">
                Keep browsing related and latest content below.
              </p>
            </div>
          </div>
        </aside>
      </div>

      <!-- TOAST -->
      <div v-if="showToast" class="fixed top-6 right-6 z-50">
        <div
          class="px-5 py-4 rounded-xl shadow-2xl border backdrop-blur-sm bg-emerald-900/90 border-emerald-700 text-emerald-100"
        >
          <span class="font-medium">{{ toastMessage }}</span>
        </div>
      </div>     

      <div class="mt-10 mb-6">
  <div class="rounded-3xl border border-slate-200 bg-white/95 p-4 shadow-sm dark:border-slate-800 dark:bg-slate-950/40">
    <AdSense />
  </div>
</div>

      <footer class="mt-16 pt-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
          <!-- More -->
          <div class="lg:col-span-4">
            <div class="text-slate-900 dark:text-white font-semibold mb-3">More</div>
            <div class="space-y-3">
              <Link
                v-for="p in related.slice(0, 4)"
                :key="p.id"
                :href="pageHref(p)"
                class="group flex gap-3 rounded-2xl border border-slate-200 bg-white/95 hover:bg-slate-50 transition-colors p-3 shadow-sm dark:border-slate-800 dark:bg-slate-950/40 dark:hover:bg-slate-900/40"
              >
                <div class="w-20 h-14 rounded-lg overflow-hidden shrink-0 border border-slate-200 dark:border-slate-800">
                  <img v-if="getThumb(p)" :src="getThumb(p)" :alt="p.title" class="w-full h-14 object-cover" />
                  <div v-else class="w-full h-full bg-slate-100 dark:bg-slate-900/40"></div>
                </div>

                <div class="min-w-0">
                  <div class="text-xs flex items-center justify-between gap-2 text-slate-500 dark:text-slate-400">
                    <span>{{ formatDate(p.published_at) }}</span>
                  </div>
                  <div class="text-sm font-semibold truncate text-slate-900 group-hover:text-sky-700 dark:text-slate-100 dark:group-hover:text-white">
                    {{ p.title }}
                  </div>
                  <div class="mt-1">
                    <span class="text-[11px] px-2 py-0.5 rounded-full border bg-slate-100 border-slate-200 text-slate-700 dark:bg-black/50 dark:border-white/10 dark:text-slate-200">
                      {{ typeLabel(p.page_type) }}
                    </span>
                  </div>
                </div>
              </Link>
            </div>
          </div>

          <!-- Newest -->
          <div class="lg:col-span-4">
            <div class="text-slate-900 dark:text-white font-semibold mb-3">Newest</div>
            <div class="space-y-3">
              <Link
                v-for="p in latest.slice(0, 4)"
                :key="p.id"
                :href="pageHref(p)"
                class="group flex gap-3 rounded-2xl border border-slate-200 bg-white/95 hover:bg-slate-50 transition-colors p-3 shadow-sm dark:border-slate-800 dark:bg-slate-950/40 dark:hover:bg-slate-900/40"
              >
                <div class="w-20 h-14 rounded-lg overflow-hidden shrink-0 border border-slate-200 dark:border-slate-800">
                  <img v-if="getThumb(p)" :src="getThumb(p)" :alt="p.title" class="w-full h-14 object-cover" />
                  <div v-else class="w-full h-full bg-slate-100 dark:bg-slate-900/40"></div>
                </div>

                <div class="min-w-0">
                  <div class="text-xs text-slate-500 dark:text-slate-400 flex items-center justify-between gap-2">
                    <span>{{ formatDate(p.published_at) }}</span>
                  </div>
                  <div class="text-sm font-semibold text-slate-900 truncate group-hover:text-sky-700 dark:text-slate-100 dark:group-hover:text-white">
                    {{ p.title }}
                  </div>
                  <div class="mt-1">
                    <span class="text-[11px] px-2 py-0.5 rounded-full bg-slate-100 border border-slate-200 text-slate-700 dark:bg-black/50 dark:border-white/10 dark:text-slate-200">
                      {{ typeLabel(p.page_type) }}
                    </span>
                  </div>
                </div>
              </Link>
            </div>
          </div>

          <!-- Popular -->
          <div class="lg:col-span-4">
            <div class="text-slate-900 dark:text-white font-semibold mb-3">Popular</div>
            <div class="space-y-3">
              <Link
                v-for="p in popular"
                :key="p.id"
                :href="pageHref(p)"
                class="group flex gap-3 rounded-2xl border border-slate-200 bg-white/95 hover:bg-slate-50 transition-colors p-3 shadow-sm dark:border-slate-800 dark:bg-slate-950/40 dark:hover:bg-slate-900/40"
              >
                <div class="w-20 h-14 rounded-lg overflow-hidden shrink-0 border border-slate-200 dark:border-slate-800">
                  <img v-if="getThumb(p)" :src="getThumb(p)" :alt="p.title" class="w-full h-14 object-cover" />
                  <div v-else class="w-full h-full bg-slate-100 dark:bg-slate-900/40"></div>
                </div>

                <div class="min-w-0">
                  <div class="text-xs text-slate-500 dark:text-slate-400 flex items-center justify-between gap-2">
                    <span>{{ formatDate(p.published_at) }}</span>
                    <span class="text-[11px]">{{ p.views }} views</span>
                  </div>
                  <div class="text-sm font-semibold text-slate-900 truncate group-hover:text-sky-700 dark:text-slate-100 dark:group-hover:text-white">
                    {{ p.title }}
                  </div>
                  <div class="mt-1">
                    <span class="text-[11px] px-2 py-0.5 rounded-full bg-slate-100 border border-slate-200 text-slate-700 dark:bg-black/50 dark:border-white/10 dark:text-slate-200">
                      {{ typeLabel(p.page_type) }}
                    </span>
                  </div>
                </div>
              </Link>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </PublicLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import PublicLayout from '@/Layouts/PublicLayout.vue'
import ContentBlocks from '@/Components/ContentBlocks.vue'
import { usePage, Link } from '@inertiajs/vue3'
import AdSense from '@/Components/AdSense.vue'

const inertia = usePage()

const props = defineProps({
  page: Object,
  related: { type: Array, default: () => [] },
  latest: { type: Array, default: () => [] },
  popular: { type: Array, default: () => [] },
})

const pageHref = (p) => {
  if (p?.type === 'blog' && p?.topic) return `/blog/${p.topic}/${p.slug}`
  return `/pages/${p.slug}`
}

const surfaceClass = computed(() => {
  if (props.page?.page_type === 'horoscope') {
    return 'bg-white/85 border-slate-200 text-slate-900 dark:bg-slate-900/40 dark:border-slate-800 dark:text-slate-100'
  }

  return 'bg-white/75 border-slate-200 text-slate-900 dark:bg-slate-950/20 dark:border-slate-800 dark:text-slate-100'
})

const wrapperClass = computed(() => {
  const w =
    props.page.layout === 'minimal' ? 'max-w-4xl' :
    props.page.layout === 'classic' ? 'max-w-5xl' :
    props.page.layout === 'magazine' ? 'max-w-6xl' :
    props.page.layout === 'editorial' ? 'max-w-5xl' :
    props.page.layout === 'story' ? 'max-w-4xl' :
    props.page.layout === 'docs' ? 'max-w-5xl' :
    props.page.layout === 'hero' ? 'max-w-6xl' :
    'max-w-5xl'

  return `${w} mx-auto`
})

const theme = computed(() => {
  switch (props.page.layout) {
    case 'minimal':
      return {
        headerBg: 'bg-white/90 border-slate-200 dark:bg-slate-950/40 dark:border-slate-800',
        title: 'text-3xl md:text-4xl font-semibold tracking-tight text-slate-900 dark:text-white',
        meta: 'text-slate-600 dark:text-slate-400',
        btn: 'bg-slate-900 text-white hover:bg-slate-800 dark:bg-slate-800 dark:hover:bg-slate-700',
        card: 'bg-transparent border-transparent p-0 shadow-none',
        prose: '',
        titleHero: 'text-4xl md:text-5xl font-extrabold leading-tight text-white',
        metaHero: 'text-slate-200/85',
      }

    case 'classic':
      return {
        headerBg: 'bg-white/90 border-slate-200 dark:bg-slate-950/40 dark:border-slate-800',
        title: 'text-3xl md:text-4xl font-bold text-slate-900 dark:text-slate-100',
        meta: 'text-slate-600 dark:text-slate-400',
        btn: 'bg-slate-900 hover:bg-slate-800 text-white',
        card: '',
        prose: '',
        titleHero: 'text-4xl md:text-5xl font-extrabold leading-tight text-white',
        metaHero: 'text-slate-200/85',
      }

    case 'magazine':
      return {
        headerBg: 'bg-white/90 border-slate-200 dark:bg-slate-950/40 dark:border-slate-800',
        title: 'text-4xl md:text-5xl font-extrabold tracking-tight text-slate-900 dark:text-white',
        meta: 'text-slate-600 dark:text-slate-400',
        btn: 'bg-emerald-600 hover:bg-emerald-700 text-white',
        card: '',
        prose: '',
        titleHero: 'text-4xl md:text-5xl font-extrabold leading-tight text-white',
        metaHero: 'text-slate-200/85',
      }

    case 'hero':
      return {
        headerBg: 'bg-white/90 border-slate-200 dark:bg-slate-950/40 dark:border-slate-800',
        title: 'text-4xl md:text-6xl font-black leading-tight text-slate-900 dark:text-white',
        meta: 'text-slate-600 dark:text-slate-400',
        btn: 'bg-slate-900 text-white hover:bg-slate-800',
        card: '',
        prose: '',
        titleHero: 'text-4xl md:text-6xl font-black leading-tight text-white',
        metaHero: 'text-slate-200/85',
      }

    case 'editorial':
      return {
        headerBg: 'bg-white/95 border-slate-200 dark:bg-slate-950/40 dark:border-slate-800',
        title: 'text-3xl md:text-5xl font-black tracking-tight text-slate-900 dark:text-white',
        meta: 'text-slate-600 dark:text-slate-400',
        btn: 'bg-sky-600 text-white hover:bg-sky-700',
        card: '',
        prose: '',
        titleHero: 'text-4xl md:text-5xl font-black leading-tight text-white',
        metaHero: 'text-slate-200/85',
      }

    case 'story':
      return {
        headerBg: 'bg-sky-50 border-sky-100 dark:bg-slate-950/40 dark:border-slate-800',
        title: 'text-3xl md:text-4xl font-black tracking-tight text-slate-900 dark:text-white',
        meta: 'text-slate-600 dark:text-slate-400',
        btn: 'bg-fuchsia-600 text-white hover:bg-fuchsia-700',
        card: '',
        prose: '',
        titleHero: 'text-4xl md:text-5xl font-black leading-tight text-white',
        metaHero: 'text-slate-200/85',
      }

    case 'docs':
      return {
        headerBg: 'bg-slate-50 border-slate-200 dark:bg-slate-950/40 dark:border-slate-800',
        title: 'text-3xl md:text-4xl font-bold tracking-tight text-slate-900 dark:text-white',
        meta: 'text-slate-600 dark:text-slate-400',
        btn: 'bg-slate-900 text-white hover:bg-slate-800',
        card: '',
        prose: '',
        titleHero: 'text-4xl md:text-5xl font-extrabold leading-tight text-white',
        metaHero: 'text-slate-200/85',
      }

    default:
      return {
        headerBg: 'bg-white/90 border-slate-200 dark:bg-slate-950/40 dark:border-slate-800',
        title: 'text-3xl font-bold text-slate-900 dark:text-white',
        meta: 'text-slate-600 dark:text-slate-400',
        btn: 'bg-slate-900 text-white hover:bg-slate-800',
        card: '',
        prose: '',
        titleHero: 'text-4xl md:text-5xl font-extrabold leading-tight text-white',
        metaHero: 'text-slate-200/85',
      }
  }
})

const getThumb = (p) => {
  if (p?.featured_image) return p.featured_image
  const firstImg = (p?.blocks || []).find(b => b.type === 'image' && b.src)
  return firstImg?.src || ''
}

const typeLabel = (t) => {
  if (t === 'news') return 'News'
  if (t === 'gaming') return 'Gaming'
  if (t === 'horoscope') return 'Horoscope'
  return 'Post'
}

const showToast = ref(false)
const toastMessage = ref('')
let toastTimeout = null

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const copyUrl = () => {
  navigator.clipboard.writeText(window.location.href)
    .then(() => {
      toastMessage.value = 'URL copied!'
      showToast.value = true
      clearTimeout(toastTimeout)
      toastTimeout = setTimeout(() => (showToast.value = false), 2000)
    })
    .catch(() => {
      toastMessage.value = 'Copy failed'
      showToast.value = true
      clearTimeout(toastTimeout)
      toastTimeout = setTimeout(() => (showToast.value = false), 2000)
    })
}

const canCopy = computed(() => {
  const role = inertia.props.auth?.user?.role
  return role === 'super_admin' || role === 'content_admin'
})
</script>