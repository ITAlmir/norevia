<script setup>
import { computed, onMounted, watch, nextTick } from 'vue'

const mediaUrl = (v) => {
  const s = String(v || '').trim()
  if (!s) return ''

  if (s.startsWith('data:image')) return s
  if (s.startsWith('http://') || s.startsWith('https://')) return s
  if (s.startsWith('/storage/')) return s
  if (s.startsWith('storage/')) return '/' + s

  return '/storage/' + s.replace(/^\/+/, '')
}

const props = defineProps({
  blocks: { type: Array, default: () => [] },
  content: { type: String, default: '' },
  cardClass: { type: String, default: '' },
  proseClass: { type: String, default: '' },
})

const blocksSafe = computed(() => Array.isArray(props.blocks) ? props.blocks : [])

const hasBlocks = () => blocksSafe.value.length > 0

const hasAdsense = computed(() =>
  blocksSafe.value.some(b => b?.type === 'ad' && b?.kind === 'adsense' && b?.adsense_slot)
)

function renderAdsenseSafely() {
  if (typeof window === 'undefined') return
  if (!hasAdsense.value) return
  if (!window.adsbygoogle) return

  try {
    document.querySelectorAll('.adsbygoogle').forEach((el) => {
      if (!el.dataset.adsbygoogleStatus) {
        try {
          window.adsbygoogle.push({})
        } catch (e) {
          // ignore repeated init issues
        }
      }
    })
  } catch (e) {
    // ignore
  }
}

onMounted(async () => {
  await nextTick()
  renderAdsenseSafely()
})

watch(
  blocksSafe,
  async () => {
    await nextTick()
    renderAdsenseSafely()
  },
  { deep: true }
)

function escapeHtml(s) {
  return String(s ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
}

function linkify(text) {
  const urlRe = /((https?:\/\/|www\.)[^\s<]+)/gi
  return text.replace(urlRe, (match) => {
    const href = match.startsWith('http') ? match : `https://${match}`
    return `<a href="${href}" target="_blank" rel="nofollow noopener noreferrer"
              class="text-sky-600 underline underline-offset-2 hover:text-sky-700 dark:text-sky-300 dark:hover:text-sky-200 break-words">${match}</a>`
  })
}

function decorateInlineText(s) {
  let out = s

  out = out.replace(/^✔\s*(.+)$/gm, `<span class="text-emerald-600 dark:text-emerald-300 font-semibold">✔ $1</span>`)
  out = out.replace(/(^|\s)(#[A-Za-z0-9_]+)/g, `$1<span class="text-fuchsia-600 dark:text-fuchsia-300 font-semibold">$2</span>`)
  out = linkify(out)

  return out
}

function wrapPlainTextToHtml(text) {
  if (!text) return ''

  const normalized = String(text)
    .replace(/\r\n/g, '\n')
    .replace(/\r/g, '\n')
    .trim()

  if (!normalized) return ''

  const chunks = normalized.split(/\n{2,}/)

  return chunks.map((chunk) => {
    const lines = chunk
      .split('\n')
      .map(line => line.trim())
      .filter(Boolean)

    if (!lines.length) return ''

    if (lines.every(line => /^[-*•]\s+/.test(line))) {
      const items = lines.map((line) => {
        const item = line.replace(/^[-*•]\s+/, '')
        let safe = escapeHtml(item)
        safe = decorateInlineText(safe)
        return `<li>${safe}</li>`
      }).join('')
      return `<ul>${items}</ul>`
    }

    let paragraph = escapeHtml(lines.join('<br>'))
    paragraph = decorateInlineText(paragraph)

    return `<p>${paragraph}</p>`
  }).join('')
}

function formatTextSafe(text) {
  if (!text) return ''

  const raw = String(text)

  // Ako već ima HTML (npr <br>, <strong>, itd) — NE DIRAJ
  if (/<[a-z][\s\S]*>/i.test(raw)) {
    return raw
      .replace(/\r\n/g, '\n')
      .replace(/\r/g, '\n')
  }

  return wrapPlainTextToHtml(raw)
}

function ctaClass(variant = 'primary') {
  const base =
    'inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-semibold ' +
    'transition-all duration-200 hover:-translate-y-0.5 focus:outline-none focus:ring-2 '

  if (variant === 'secondary') {
    return base + ' border border-slate-300 bg-slate-100 text-slate-900 hover:bg-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700'
  }

  if (variant === 'outline') {
    return base + ' border border-emerald-500/60 text-emerald-700 hover:bg-emerald-50 dark:text-emerald-300 dark:hover:bg-emerald-900/20'
  }

  return base +
    ' text-white bg-gradient-to-r from-emerald-600 to-sky-600 ' +
    'hover:from-emerald-500 hover:to-sky-500 shadow-sm'
}
</script>

<template>
  <div v-if="hasBlocks()" class="space-y-5">
    <template v-for="(b, i) in blocksSafe" :key="i">
      <!-- TEXT BLOCK -->
      <div
        v-if="b.type === 'text'"
        :class="[
          'p-0 text-slate-900 dark:text-slate-100',
          cardClass
        ]"
      >
        <div
          :class="[
            'content-prose prose max-w-none break-words prose-slate dark:prose-invert',
            proseClass
          ]"
          v-html="formatTextSafe(b.content)"
        ></div>
      </div>

      <!-- CTA BLOCK -->
      <div
        v-else-if="b.type === 'cta' && b.url"
        :class="[
          'rounded-3xl p-5 md:p-6',
          'bg-slate-50 text-slate-900',
          'dark:bg-slate-900/40 dark:text-slate-100',
          cardClass
        ]"
      >
        <div class="flex flex-col gap-3 items-start">
          <div
            v-if="b.title"
            class="text-xl font-bold text-slate-900 dark:text-slate-100"
          >
            {{ b.title }}
          </div>

          <div
            v-if="b.text"
            class="text-sm whitespace-pre-line break-words text-slate-700 dark:text-slate-300 leading-7"
          >
            {{ b.text }}
          </div>

          <a
            :href="b.url"
            target="_blank"
            rel="nofollow noopener noreferrer"
            :class="b.btnClass ? b.btnClass : ctaClass(b.variant)"
          >
            <span>{{ b.label || 'Open' }}</span>
            <span class="opacity-90">→</span>
          </a>

          <div
            v-if="b.note"
            class="text-xs text-slate-600 dark:text-slate-400"
          >
            {{ b.note }}
          </div>
        </div>
      </div>

      <!-- IMAGE BLOCK -->
      <figure
        v-else-if="b.type === 'image' && b.src"
        class="rounded-3xl bg-transparent p-0"
      >
        <img
          :src="mediaUrl(b.src)"
          :alt="b.caption || 'Image'"
          class="w-full max-h-[34rem] object-cover rounded-2xl shadow-sm"
          loading="lazy"
        />
        <figcaption
          v-if="b.caption"
          class="mt-3 text-sm text-slate-600 dark:text-slate-300 text-center leading-relaxed"
          v-html="formatTextSafe(b.caption)"
        ></figcaption>
      </figure>

      <!-- AD / BANNER BLOCK -->
      <div v-else-if="b.type === 'ad'" class="my-8">
        <div v-if="b.note" class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-3">
          {{ b.note }}
        </div>

        <!-- AdSense -->
        <div v-if="b.kind === 'adsense' && b.adsense_slot" class="overflow-hidden">
          <ins
            class="adsbygoogle"
            style="display:block"
            data-ad-client="ca-pub-4474320596321568"
            :data-ad-slot="b.adsense_slot"
            data-ad-format="auto"
            data-full-width-responsive="true"
          ></ins>
        </div>

        <!-- Affiliate / HTML -->
        <div
          v-else-if="b.html"
          class="prose max-w-none prose-slate dark:prose-invert"
          v-html="b.html"
        ></div>

        <div v-else class="text-sm text-slate-500 dark:text-slate-400">
          Ad block not configured.
        </div>
      </div>

      <!-- UNKNOWN -->
      <div v-else class="text-sm text-slate-500 dark:text-slate-400">
        Unsupported block: {{ b.type }}
      </div>
    </template>
  </div>

  <!-- Fallback -->
  <div
    v-else
    class="p-0 text-slate-900 dark:text-slate-100"
  >
    <div
      class="content-prose prose max-w-none break-words prose-slate dark:prose-invert"
      v-html="formatTextSafe(content)"
    ></div>
  </div>
</template>

<style scoped>
.content-prose :deep(h1) {
  @apply mt-8 mb-4 text-3xl md:text-4xl font-black tracking-tight text-slate-900 dark:text-white;
}

.content-prose :deep(h2) {
  @apply mt-8 mb-4 text-2xl md:text-3xl font-black tracking-tight text-slate-900 dark:text-white;
}

.content-prose :deep(h3) {
  @apply mt-7 mb-3 text-xl md:text-2xl font-bold text-slate-900 dark:text-white;
}

.content-prose :deep(p) {
  @apply my-4 text-[17px] leading-8 text-slate-700 dark:text-slate-300 whitespace-pre-line break-words;
}

.content-prose :deep(p.lead) {
  @apply text-xl leading-9 text-slate-700 dark:text-slate-300;
}

.content-prose :deep(p.small-text) {
  @apply text-sm leading-7 text-slate-500 dark:text-slate-400;
}

.content-prose :deep(blockquote) {
  @apply my-8 border-l-4 border-sky-500 bg-sky-50 px-5 py-4 text-lg italic text-slate-700 dark:border-sky-400 dark:bg-sky-950/20 dark:text-slate-300 rounded-r-2xl;
}

.content-prose :deep(div.highlight-box) {
  @apply my-6 rounded-2xl border border-amber-200 bg-amber-50 p-5 text-slate-700 dark:border-amber-900/40 dark:bg-amber-950/20 dark:text-slate-300;
}

.content-prose :deep(ul) {
  @apply my-5 list-disc pl-6;
}

.content-prose :deep(li) {
  @apply my-2 text-[17px] leading-8 text-slate-700 dark:text-slate-300;
}

.content-prose :deep(a) {
  @apply underline underline-offset-4;
}

.content-prose :deep(strong) {
  @apply font-bold text-slate-900 dark:text-white;
}

.content-prose :deep(em) {
  @apply italic;
}

.content-prose :deep(img) {
  @apply rounded-2xl;
}
</style>