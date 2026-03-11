<script setup>
import { computed, onMounted, watch, nextTick } from 'vue'

const mediaUrl = (v) => {
  const s = String(v || '').trim()
  if (!s) return ''

  // previews
  if (s.startsWith('data:image')) return s

  // already absolute
  if (s.startsWith('http://') || s.startsWith('https://')) return s

  // already correct public path
  if (s.startsWith('/storage/')) return s
  if (s.startsWith('storage/')) return '/' + s

  // if DB stores "uploads/xxx.jpg" or "pages/xxx.jpg"
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
    window.adsbygoogle.push({})
  } catch (e) {
    // ignore repeated AdSense init errors
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
              class="text-sky-300 underline underline-offset-2 hover:text-sky-200 break-words">${match}</a>`
  })
}

function decorateInlineText(s) {
  let out = s

  // highlight check lines
  out = out.replace(/^✔\s*(.+)$/gm, `<span class="text-emerald-300 font-semibold">✔ $1</span>`)

  // highlight hashtags
  out = out.replace(/(^|\s)(#[A-Za-z0-9_]+)/g, `$1<span class="text-fuchsia-300 font-semibold">$2</span>`)

  // links
  out = linkify(out)

  return out
}

function formatTextSafe(text) {
  if (!text) return ''

  const raw = String(text)

  // if already contains HTML, do not transform structure
  if (/<[a-z][\s\S]*>/i.test(raw)) return raw

  const normalized = raw
    .replace(/\r\n/g, '\n')
    .replace(/\r/g, '\n')
    .trim()

  if (!normalized) return ''

  const chunks = normalized.split(/\n{2,}/)

  const html = chunks.map((chunk) => {
    const lines = chunk
      .split('\n')
      .map(line => line.trim())
      .filter(Boolean)

    if (!lines.length) return ''

    // List block
    if (lines.every(line => /^[-*•]\s+/.test(line))) {
      const items = lines.map((line) => {
        const item = line.replace(/^[-*•]\s+/, '')
        let safe = escapeHtml(item)
        safe = decorateInlineText(safe)
        return `<li>${safe}</li>`
      }).join('')

      return `<ul>${items}</ul>`
    }

    // Single short line => heading
    if (
      lines.length === 1 &&
      lines[0].length <= 90 &&
      !/[.!?:]$/.test(lines[0])
    ) {
      let safe = escapeHtml(lines[0])
      safe = decorateInlineText(safe)
      return `<h2>${safe}</h2>`
    }

    // Paragraph
    let paragraph = escapeHtml(lines.join(' '))
    paragraph = decorateInlineText(paragraph)

    return `<p>${paragraph}</p>`
  }).join('')

  return html
}

function ctaClass(variant = 'primary') {
  const base =
    'inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl font-semibold ' +
    'transition-all duration-200 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 ' +
    'focus:ring-offset-slate-950'

  if (variant === 'secondary') {
    return base + ' border border-slate-700 bg-slate-900/40 text-slate-100 hover:bg-slate-900/60 focus:ring-slate-400'
  }

  if (variant === 'outline') {
    return base + ' border border-emerald-600/60 text-emerald-300 hover:bg-emerald-900/20 focus:ring-emerald-400'
  }

  return base +
    ' text-white bg-gradient-to-r from-emerald-600 to-sky-600 ' +
    'hover:from-emerald-500 hover:to-sky-500 ' +
    'shadow-[0_16px_40px_-22px_rgba(16,185,129,0.55)] ' +
    'hover:shadow-[0_22px_55px_-28px_rgba(14,165,233,0.55)] ' +
    'focus:ring-emerald-400'
}
</script>

<template>
  <!-- Prefer blocks -->
  <div v-if="hasBlocks()" class="space-y-6">
    <template v-for="(b, i) in blocksSafe" :key="i">
      <!-- TEXT BLOCK -->
      <div
        v-if="b.type === 'text'"
        :class="[
          'rounded-2xl border p-5',
          'border-slate-200 bg-white/90 text-slate-900',
          'dark:border-slate-800 dark:bg-slate-950/40 dark:text-slate-100',
          cardClass
        ]"
      >
        <div
          :class="['prose max-w-none break-words dark:prose-invert', proseClass]"
          v-html="formatTextSafe(b.content)"
        ></div>
      </div>

      <!-- CTA BLOCK -->
      <div
        v-else-if="b.type === 'cta' && b.url"
        :class="[
          'rounded-2xl border p-5',
          'border-slate-200 bg-white/90 text-slate-900',
          'dark:border-slate-800 dark:bg-slate-950/40 dark:text-slate-100',
          cardClass
        ]"
      >
        <div class="flex flex-col gap-3 items-start">
          <div
            v-if="b.title"
            class="text-lg font-bold text-slate-900 dark:text-slate-100"
          >
            {{ b.title }}
          </div>

          <div
            v-if="b.text"
            class="text-sm whitespace-pre-line break-words text-slate-700 dark:text-slate-200"
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
        class="rounded-2xl border border-slate-200 bg-white/90 p-4 dark:border-slate-800 dark:bg-slate-950/40"
      >
        <img
          :src="mediaUrl(b.src)"
          :alt="b.caption || 'Image'"
          class="w-full max-h-[32rem] object-cover rounded-xl"
          loading="lazy"
        />
        <figcaption
          v-if="b.caption"
          class="mt-3 text-sm text-slate-600 dark:text-slate-300 text-center leading-relaxed"
          v-html="formatTextSafe(b.caption)"
        ></figcaption>
      </figure>

      <!-- AD / BANNER BLOCK -->
      <div
        v-else-if="b.type === 'ad'"
        :class="[
          'rounded-2xl border p-5',
          'border-slate-200 bg-white/90 text-slate-900',
          'dark:border-slate-800 dark:bg-slate-950/40 dark:text-slate-100',
          cardClass
        ]"
      >
        <div v-if="b.note" class="text-xs text-slate-600 dark:text-slate-400 mb-3">
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
          class="prose max-w-none dark:prose-invert"
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

  <!-- Fallback to content if no blocks -->
  <div
    v-else
    class="rounded-2xl border p-5 border-slate-200 bg-white/90 text-slate-900 dark:border-slate-800 dark:bg-slate-950/40 dark:text-slate-100"
  >
    <div
      class="prose max-w-none break-words dark:prose-invert"
      v-html="formatTextSafe(content)"
    ></div>
  </div>
</template>