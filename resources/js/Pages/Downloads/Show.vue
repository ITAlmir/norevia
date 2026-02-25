<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
import { Link } from '@inertiajs/vue3' 
import { computed } from 'vue'

const props = defineProps({
  item: { type: Object, required: true },
  related: { type: Array, default: () => [] },
})

function bytes(n) {
  if (!n) return '-'
  const units = ['B','KB','MB','GB']
  let i = 0
  let v = Number(n)
  while (v >= 1024 && i < units.length - 1) { v /= 1024; i++ }
  return `${v.toFixed(i === 0 ? 0 : 2)} ${units[i]}`
}

function thumb(x) {
  if (!x) return ''
  if (x.thumbnail_url) return x.thumbnail_url
  if (x.thumbnail_path) return `/storage/${x.thumbnail_path}` // ✅ najbitnije kod tebe
  if (x.featured_image) return x.featured_image
  return ''
}

const rawDesc = computed(() => (props.item?.description || '').trim())

function parseCta(text) {
  // format: [BUTTON: Label | https://...]
  const re = /\[BUTTON:\s*([^\|\]]+)\|\s*([^\]]+)\]/i
  const m = String(text || '').match(re)
  if (!m) return { clean: text, cta: null }

  const label = m[1].trim()
  const url = m[2].trim()

  // ukloni marker iz teksta
  const clean = String(text).replace(re, '').trim()

  return {
    clean,
    cta: { label, url }
  }
}

const parsedDesc = computed(() => parseCta(rawDesc.value))

function splitSections(text) {
  if (!text) {
    return { intro: '', sections: [] }
  }

  // Normalizuj linije
  const t = text.replace(/\r\n/g, '\n').replace(/\r/g, '\n')

  // Sekcije koje podržavamo
  const headers = [
    'Features',
    'Who is it for?',
    'System Requirements',
    'Requirements',
    'Notes',
  ]

  // Nađi prvi header index
  let firstHeaderIndex = -1
  let firstHeaderName = null

  for (const h of headers) {
    const idx = t.indexOf('\n' + h + '\n')
    const idx2 = t.startsWith(h + '\n') ? 0 : -1
    const realIdx = idx2 === 0 ? 0 : idx
    if (realIdx !== -1 && (firstHeaderIndex === -1 || realIdx < firstHeaderIndex)) {
      firstHeaderIndex = realIdx
      firstHeaderName = h
    }
  }

  // Intro: sve prije prvog headera
  const intro = firstHeaderIndex === -1 ? t : t.slice(0, firstHeaderIndex).trim()

  if (firstHeaderIndex === -1) {
    return { intro, sections: [] }
  }

  // Raspodijeli sekcije
  const rest = t.slice(firstHeaderIndex).trim()
  const lines = rest.split('\n')

  const sections = []
  let current = null

  const isHeader = (line) => headers.includes(line.trim())

  for (const line of lines) {
    const l = line.trim()

    if (isHeader(l)) {
      if (current) {
        current.content = current.content.trim()
        sections.push(current)
      }
      current = { title: l, content: '' }
      continue
    }

    if (!current) {
      // safety fallback
      current = { title: firstHeaderName, content: '' }
    }

    current.content += (current.content ? '\n' : '') + line
  }

  if (current) {
    current.content = current.content.trim()
    sections.push(current)
  }

  // Cleanup: ukloni prazne
  return {
    intro,
    sections: sections.filter(s => s.content && s.content.trim().length > 0),
  }
}

const parsed = computed(() => splitSections(rawDesc.value))

// Helper za "bullets" (• ili -) -> lista
function toBullets(block) {
  const lines = String(block || '')
    .split('\n')
    .map(x => x.trim())
    .filter(Boolean)

  const bulletLines = lines.filter(l => l.startsWith('•') || l.startsWith('-'))
  if (bulletLines.length >= 2) {
    return bulletLines.map(l => l.replace(/^(\•|\-)\s*/, ''))
  }
  return null
}

</script>

<template>
  <MainLayout>
    <div class="max-w-5xl mx-auto px-4 py-10">
      <!-- soft background accents -->
      <div class="relative">
        <div class="pointer-events-none absolute -top-10 -left-10 h-52 w-52 rounded-full blur-3xl opacity-40
                    bg-gradient-to-br from-emerald-300 via-sky-200 to-indigo-300
                    dark:from-emerald-900/40 dark:via-sky-900/30 dark:to-indigo-900/30" />
        <div class="pointer-events-none absolute -top-12 -right-8 h-56 w-56 rounded-full blur-3xl opacity-30
                    bg-gradient-to-br from-fuchsia-200 via-amber-200 to-rose-200
                    dark:from-fuchsia-900/25 dark:via-amber-900/20 dark:to-rose-900/20" />
      </div>

      <!-- TOP AD / BANNER container -->
      <div class="relative rounded-2xl p-[1px] mb-6
                  bg-gradient-to-r from-slate-200 via-white to-slate-200
                  dark:from-slate-800 dark:via-slate-900/40 dark:to-slate-800
                  shadow-[0_16px_50px_-28px_rgba(2,6,23,0.45)]">
        <div class="rounded-2xl p-5
                    bg-white/80 backdrop-blur
                    dark:bg-slate-950/40
                    border border-white/60 dark:border-slate-800/70">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
              <div class="text-xs font-semibold text-slate-600 dark:text-slate-300">
                Sponsored / Banner
              </div>
              <div class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Placeholder container — later you can drop AdSense code here.
              </div>
            </div>
            <div class="text-xs text-slate-400 dark:text-slate-500">
              728×90 or responsive
            </div>
          </div>
        </div>
      </div>

      <!-- HERO CARD -->
      <div
        class="relative rounded-3xl p-[1px]
               bg-gradient-to-r from-emerald-200 via-white to-sky-200
               dark:from-emerald-900/35 dark:via-slate-900/35 dark:to-sky-900/30
               shadow-[0_22px_70px_-38px_rgba(2,6,23,0.55)]"
      >
        <div
          class="rounded-3xl p-6 md:p-8
                 bg-white/85 backdrop-blur
                 dark:bg-slate-950/45
                 border border-white/70 dark:border-slate-800/70"
        >
          <div class="flex flex-col md:flex-row md:items-start gap-6">
            <!-- THUMB -->
            <div
              class="shrink-0 w-full md:w-44 md:h-44 h-40 rounded-2xl overflow-hidden
                     border border-slate-200/80 dark:border-slate-800/80
                     bg-gradient-to-br from-slate-50 to-white
                     dark:from-slate-900/60 dark:to-slate-950/20
                     shadow-[0_10px_30px_-18px_rgba(2,6,23,0.35)]"
            >
              <img
                v-if="thumb(item)"
                :src="thumb(item)"
                class="h-full w-full object-cover"
                alt="Thumbnail"
              />
              <div v-else class="h-full w-full flex items-center justify-center">
                <div class="text-center">
                  <div class="text-4xl">⬇️</div>
                  <div class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                    No thumbnail
                  </div>
                </div>
              </div>
            </div>

            <!-- CONTENT -->
            <div class="min-w-0 flex-1">
              <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                  <div class="inline-flex items-center gap-2">
                    <span class="text-[11px] px-2 py-0.5 rounded-full border
                                 border-emerald-200 bg-emerald-50 text-emerald-800
                                 dark:border-emerald-700/60 dark:bg-emerald-900/20 dark:text-emerald-300">
                      {{ item.category || 'Utility' }}
                    </span>

                    <span class="text-[11px] px-2 py-0.5 rounded-full border
                                 border-slate-200 bg-slate-50 text-slate-700
                                 dark:border-slate-800 dark:bg-slate-900/30 dark:text-slate-300">
                      {{ item.scan_status || 'unknown' }}
                    </span>
                  </div>

                  <h1 class="mt-3 text-3xl md:text-4xl font-black tracking-tight text-slate-900 dark:text-white">
                    {{ item.title }}
                  </h1>

                  <div v-if="parsed.intro" class="mt-3 text-slate-600 dark:text-slate-300 whitespace-pre-line break-words">
                    {{ parsed.intro }}
                  </div>

<!-- Ako nema intro, fallback -->
<div v-else class="mt-3 text-slate-600 dark:text-slate-300">
  No description.
</div>
                </div>

                <!-- CTA -->
                <a
                  class="shrink-0 px-5 py-3 rounded-2xl text-white font-semibold
                         bg-gradient-to-r from-emerald-600 to-sky-600
                         hover:from-emerald-500 hover:to-sky-500
                         shadow-[0_16px_40px_-22px_rgba(16,185,129,0.55)]
                         hover:shadow-[0_22px_55px_-28px_rgba(14,165,233,0.55)]
                         transition transform hover:-translate-y-0.5"
                  :href="`/downloads/${item.slug}/download`"
                >
                  Download →
                </a>
              </div>

              <!-- META CHIPS -->
              <div class="mt-5 flex flex-wrap gap-2">
                <span class="px-3 py-1 rounded-full text-sm border
                             bg-white/70 text-slate-700 border-slate-200
                             dark:bg-slate-900/30 dark:text-slate-200 dark:border-slate-800">
                  Version: {{ item.version || '-' }}
                </span>

                <span class="px-3 py-1 rounded-full text-sm border
                             bg-white/70 text-slate-700 border-slate-200
                             dark:bg-slate-900/30 dark:text-slate-200 dark:border-slate-800">
                  OS: {{ item.os || '-' }}
                </span>

                <span class="px-3 py-1 rounded-full text-sm border
                             bg-white/70 text-slate-700 border-slate-200
                             dark:bg-slate-900/30 dark:text-slate-200 dark:border-slate-800">
                  Size: {{ bytes(item.size_bytes) }}
                </span>

                <span class="px-3 py-1 rounded-full text-sm border
                             bg-white/70 text-slate-700 border-slate-200
                             dark:bg-slate-900/30 dark:text-slate-200 dark:border-slate-800">
                  Downloads: {{ item.download_count ?? 0 }}
                </span>
              </div>

              <!-- subtle divider -->
              <div class="mt-6 h-px bg-gradient-to-r from-transparent via-slate-200 to-transparent dark:via-slate-800" />

              <!-- mini note -->
              <div class="mt-4 text-sm text-slate-500 dark:text-slate-400">
                Clicking <span class="font-semibold text-slate-700 dark:text-slate-200">Download</span> opens the confirmation step.
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- DETAILS SECTIONS (auto iz description) -->
<div v-if="parsed.sections.length" class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-4">
  <div
    v-for="(s, idx) in parsed.sections"
    :key="idx"
    class="rounded-2xl p-[1px]
           bg-gradient-to-r from-slate-200 via-white to-slate-200
           dark:from-slate-800 dark:via-slate-900/40 dark:to-slate-800
           shadow-[0_10px_30px_-18px_rgba(2,6,23,0.35)]"
  >
    <div class="rounded-2xl p-5 bg-white/90 dark:bg-slate-950/45 border border-white/70 dark:border-slate-800/70">
      <h2 class="text-base font-semibold text-slate-900 dark:text-white">
        {{ s.title }}
      </h2>

      <!-- Ako liči na bullet listu -->
      <ul v-if="toBullets(s.content)" class="mt-3 list-disc pl-5 text-sm text-slate-600 dark:text-slate-300 space-y-2">
        <li v-for="(b, i) in toBullets(s.content)" :key="i">{{ b }}</li>
      </ul>

      <!-- Inače prikaži kao tekst -->
      <div v-else class="mt-3 text-sm text-slate-600 dark:text-slate-300 whitespace-pre-line break-words">
        {{ s.content }}
      </div>
    </div>
  </div>
</div>
      <!-- MID AD container -->
      <div class="mt-8 rounded-2xl p-[1px]
                  bg-gradient-to-r from-slate-200 via-white to-slate-200
                  dark:from-slate-800 dark:via-slate-900/40 dark:to-slate-800
                  shadow-[0_14px_45px_-26px_rgba(2,6,23,0.45)]">
        <div class="rounded-2xl p-5
                    bg-white/80 backdrop-blur
                    dark:bg-slate-950/40
                    border border-white/60 dark:border-slate-800/70">
          <div class="text-xs font-semibold text-slate-600 dark:text-slate-300">Ad / Banner</div>
          <div class="mt-1 text-sm text-slate-500 dark:text-slate-400">
            Another clean slot for monetization (responsive).
          </div>
        </div>
      </div>

      <!-- RELATED -->
      <div v-if="related.length" class="mt-10">
        <div class="flex items-end justify-between gap-4 mb-4">
          <div>
            <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Related</h2>
            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
              More from {{ item.category }}
            </p>
          </div>
          <span class="text-sm text-slate-500 dark:text-slate-400">{{ related.length }} items</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
          <Link
            v-for="r in related"
            :key="r.id"
            :href="`/downloads/${r.slug}`"
            class="group rounded-2xl p-[1px]
                   bg-gradient-to-r from-slate-200 via-white to-slate-200
                   dark:from-slate-800 dark:via-slate-900/40 dark:to-slate-800
                   shadow-[0_10px_30px_-18px_rgba(2,6,23,0.35)]
                   hover:shadow-[0_16px_50px_-22px_rgba(2,6,23,0.45)]
                   transition"
          >
            <div class="rounded-2xl p-4
                        bg-white/90 dark:bg-slate-950/45
                        border border-white/70 dark:border-slate-800/70
                        hover:bg-white dark:hover:bg-slate-950/60 transition">
              <div class="flex items-center gap-3">
                <div class="h-12 w-12 rounded-xl overflow-hidden border
                            border-slate-200/80 dark:border-slate-800/80
                            bg-slate-50 dark:bg-slate-900/40 shrink-0">
                  <img v-if="thumb(r)" :src="thumb(r)" class="h-full w-full object-cover" />
                  <div v-else class="h-full w-full flex items-center justify-center">⬇️</div>
                </div>

                <div class="min-w-0 flex-1">
                  <div class="font-semibold text-slate-900 dark:text-white truncate">
                    {{ r.title }}
                  </div>
                  <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    {{ r.category }} • {{ r.version || '-' }}
                  </div>
                  <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    {{ r.download_count ?? 0 }} downloads
                    <span class="ml-2 group-hover:underline">Open →</span>
                  </div>
                </div>
              </div>
            </div>
          </Link>
        </div>
      </div>

      <!-- back -->
      <div class="mt-8">
        <a href="/downloads" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white">
          <span class="text-lg">←</span>
          <span class="underline">Back to downloads</span>
        </a>
      </div>
    </div>
  </MainLayout>
</template>