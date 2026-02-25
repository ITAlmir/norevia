<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  featured: { type: Array, default: () => [] },
  items: { type: Object, default: () => ({ data: [], links: [] }) }, // paginator
  filters: { type: Object, default: () => ({ search: '', category: '', sort: 'new' }) },
})

function updateFilters(extra) {
  router.get('/downloads', { ...props.filters, ...extra }, {
    preserveScroll: true,
    preserveState: true,
    replace: true,
  })
}

/** ✅ Category sections (hard-coded UI groups) */
const sections = [
  { key: 'gaming', title: 'Gaming', icon: '🎮', desc: 'FPS, latency, launch utilities and CS2 helpers.' },
  { key: 'tools', title: 'Tools', icon: '🧩', desc: 'Small utilities for workflow, cleanup and tuning.' },
  { key: 'apps', title: 'Apps', icon: '📦', desc: 'Helpful apps for creators and daily productivity.' },
]

/** ✅ thumbnail resolver (adjust field names to your Item model) */
function thumb(d) {
  return d?.thumbnail || d?.thumbnail_url || d?.image || d?.featured_image || null
}

/** ✅ group items by category safely */
const grouped = computed(() => {
  const map = { gaming: [], tools: [], apps: [] }
  const arr = props.items?.data || []

  for (const it of arr) {
    const k = String(it?.category || '').toLowerCase()
    if (map[k]) map[k].push(it)
  }

  return map
})
</script>

<template>
  <MainLayout>
    <div class="max-w-7xl mx-auto px-4 py-10">
      <div class="mb-6">
        <h1 class="text-3xl font-bold ui-page-title">Downloads</h1>
        <p class="ui-page-subtitle mt-1">Clean, practical tools with a dedicated download confirmation page.</p>
      </div>

      <!-- Filters -->
      <div class="flex flex-wrap gap-3 items-center mb-8">
        <input
          class="ui-input w-full md:w-96"
          placeholder="Search downloads..."
          :value="filters.search || ''"
          @input="e => updateFilters({ search: e.target.value })"
        />

        <select
          class="ui-select"
          :value="filters.category || ''"
          @change="e => updateFilters({ category: e.target.value || '' })"
        >
          <option value="">All categories</option>
          <option value="gaming">Gaming</option>
          <option value="tools">Tools</option>
          <option value="apps">Apps</option>
        </select>

        <select
          class="ui-select"
          :value="filters.sort || 'new'"
          @change="e => updateFilters({ sort: e.target.value })"
        >
          <option value="new">Newest</option>
          <option value="top">Most downloaded</option>
        </select>
      </div>

      <!-- Sections -->
      <!-- Categories list -->
<div v-for="s in sections" :key="s.key" class="mb-10">
  <div class="flex items-start justify-between gap-4 mb-3">
    <div>
      <h2 class="ui-page-title font-semibold text-lg flex items-center gap-2">
        <span class="text-xl">{{ s.icon }}</span>
        <span>{{ s.title }}</span>
      </h2>
      <p class="ui-page-subtitle mt-1">{{ s.desc }}</p>
    </div>

    <span class="text-gray-500 text-sm">
      {{ (grouped[s.key] || []).length }} items
    </span>
  </div>

  <div v-if="(grouped[s.key] || []).length" class="space-y-2">
    <div
      v-for="d in grouped[s.key]"
      :key="d.id"
      class="relative rounded-2xl p-[1px]
             bg-gradient-to-r from-slate-200 via-white to-slate-200
             dark:from-slate-800 dark:via-slate-900/40 dark:to-slate-800
             shadow-[0_10px_30px_-18px_rgba(2,6,23,0.35)]
             hover:shadow-[0_16px_50px_-22px_rgba(2,6,23,0.45)]
             transition"
    >
      <!-- whole row opens DETAILS -->
      <Link
        :href="`/downloads/${d.slug}`"
        class="group block rounded-2xl px-4 py-3 md:px-5 md:py-4
               bg-white/95 backdrop-blur
               dark:bg-slate-950/45
               border border-white/70 dark:border-slate-800/70
               hover:bg-white
               dark:hover:bg-slate-950/60
               transition"
      >
        <div class="flex items-center gap-4">
          <!-- thumbnail -->
          <div
            class="shrink-0 h-16 w-16 md:h-20 md:w-20 rounded-xl overflow-hidden
                   border border-slate-200/80 dark:border-slate-800/80
                   bg-gradient-to-br from-slate-100 to-white
                   dark:from-slate-900/60 dark:to-slate-950/20
                   shadow-sm"
          >
            <img v-if="thumb(d)" :src="thumb(d)" class="h-full w-full object-cover" />
            <div v-else class="h-full w-full flex items-center justify-center text-2xl">
              ⬇️
            </div>
          </div>

          <div class="min-w-0 flex-1">
            <div class="flex items-start justify-between gap-3">
              <div class="min-w-0">
                <div class="flex items-center gap-2">
                  <span
                    v-if="d.is_featured"
                    class="text-[11px] px-2 py-0.5 rounded-full border
                           border-emerald-200 text-emerald-700 bg-emerald-50
                           dark:border-emerald-700/60 dark:text-emerald-300 dark:bg-emerald-900/20"
                  >
                    Featured
                  </span>

                  <span
                    class="text-[11px] px-2 py-0.5 rounded-full border
                           border-slate-200 text-slate-600 bg-slate-50
                           dark:border-slate-800 dark:text-slate-300 dark:bg-slate-900/30"
                  >
                    {{ (d.scan_status || 'unknown') }}
                  </span>
                </div>

                <div class="mt-2 font-semibold text-slate-900 dark:text-white truncate">
                  {{ d.title }}
                </div>

                <div class="mt-1 text-sm text-slate-600 dark:text-slate-400 line-clamp-2">
                  {{ d.short_description || d.description || d.meta_description || 'Open to see details and download.' }}
                </div>
              </div>

              <!-- actions -->
              <div class="shrink-0 flex flex-col items-end gap-2">
                <div class="text-xs text-slate-500 dark:text-slate-400 hidden sm:block">
                  v{{ d.version || '-' }} • {{ d.os || 'Windows' }}
                </div>

                <!-- Download button goes to CONFIRM page -->
                <Link
                  :href="`/downloads/${d.slug}`"
                  class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold
                         bg-slate-900 text-white hover:bg-slate-800
                         dark:bg-emerald-600 dark:hover:bg-emerald-700
                         shadow-sm transition"
                  @click.stop
                >
                  Download <span class="opacity-90">→</span>
                </Link>
              </div>
            </div>

            <div class="mt-3 text-xs text-slate-500 dark:text-slate-400">
              {{ d.download_count ?? 0 }} downloads • <span class="group-hover:underline">Open details →</span>
            </div>
          </div>
        </div>
      </Link>
    </div>
  </div>

  <div
    v-else
    class="rounded-2xl border p-4 text-sm
           bg-white border-slate-200 text-slate-600
           dark:bg-slate-900/20 dark:border-slate-800 dark:text-slate-400"
  >
    No {{ s.title.toLowerCase() }} items yet.
  </div>
</div>

      <!-- Pagination -->
      <div v-if="items.links && items.links.length" class="mt-10 flex flex-wrap gap-2">
        <template v-for="(l, idx) in items.links" :key="idx">
          <span
            v-if="!l.url"
            class="px-3 py-2 rounded-lg border border-slate-300 text-slate-500 dark:border-slate-700 dark:text-gray-500"
            v-html="l.label"
          />
          <Link
            v-else
            :href="l.url"
            class="px-3 py-2 rounded-lg border border-slate-700"
            :class="l.active
              ? 'bg-slate-900 text-white border-slate-900 dark:bg-slate-700 dark:border-slate-700'
              : 'bg-white text-slate-700 border-slate-300 hover:bg-slate-50 dark:bg-slate-900/40 dark:text-gray-300 dark:border-slate-700 dark:hover:bg-slate-800'"
            v-html="l.label"
          />
        </template>
      </div>
    </div>
  </MainLayout>
</template>