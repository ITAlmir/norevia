<template>
  <MainLayout>
    <div class="max-w-4xl mx-auto px-4 py-8">
      <!-- HEADER -->
      <div class="mb-8 p-6 bg-slate-800/40 rounded-xl border border-slate-700">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-white mb-2">{{ page.title }}</h1>
            <div class="flex flex-wrap items-center gap-3 text-gray-400 text-sm">
              <span>By {{ page.author?.name || 'Unknown' }}</span>
              <span>•</span>
              <span>{{ formatDate(page.published_at) }}</span>
              <span v-if="page.views !== undefined">•</span>
              <span v-if="page.views !== undefined">{{ page.views }} views</span>
            </div>
          </div>

          <button
            @click="copyUrl"
            class="px-5 py-2.5 bg-slate-700 hover:bg-slate-600 text-white rounded-lg font-medium transition-colors flex items-center gap-2"
          >
            Copy URL
          </button>
        </div>
      </div>

      <!-- FEATURED IMAGE -->
      <div v-if="page.featured_image" class="mb-8">
        <img
          :src="page.featured_image"
          :alt="page.title"
          class="w-full h-96 object-cover rounded-xl shadow-lg"
        />
        <p v-if="page.image_caption" class="text-center text-gray-400 mt-3 text-sm">
          {{ page.image_caption }}
        </p>
      </div>

      <!-- CONTENT -->
      <div class="bg-slate-800/30 rounded-xl border border-slate-700 p-8">
        <div class="prose prose-invert max-w-none whitespace-pre-line break-words">
          <template v-if="page.blocks && page.blocks.length">
            <div v-for="(block, i) in page.blocks" :key="i" class="mb-6">
              <div v-if="block.type === 'text'" v-html="nl2brSafe(block.content)"></div>

              <figure v-else-if="block.type === 'image'">
                <img :src="block.src" class="w-full rounded-xl" />
                <figcaption v-if="block.caption" class="text-center text-gray-400 text-sm mt-2">
                  {{ block.caption }}
                </figcaption>
              </figure>
            </div>
          </template>

          <div v-else v-html="nl2brSafe(page.content)"></div>
        </div>
      </div>

      <!-- RELATED -->
      <div v-if="related?.length" class="mt-10 border-t border-slate-700 pt-8">
        <h3 class="text-xl font-bold text-white mb-4">Još iz iste kategorije</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <a
            v-for="r in related"
            :key="r.id"
            :href="`/pages/${r.slug}`"
            class="p-4 rounded-xl border border-slate-700 bg-slate-900/30 hover:bg-slate-900/50 text-gray-200"
          >
            <div class="font-semibold">{{ r.title }}</div>
            <div class="text-xs text-gray-400 mt-1">{{ formatDate(r.published_at) }}</div>
          </a>
        </div>
      </div>
    </div>

    <!-- TOAST -->
    <div v-if="showToast" class="fixed top-6 right-6 z-50">
      <div class="px-5 py-4 rounded-xl shadow-2xl border backdrop-blur-sm bg-green-900/90 border-green-700 text-green-100">
        <span class="font-medium">{{ toastMessage }}</span>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
import { ref } from 'vue'

const props = defineProps({
  page: Object,
  related: {
    type: Array,
    default: () => []
  }
})

const showToast = ref(false)
const toastMessage = ref('')
let toastTimeout = null

const showNotification = (message) => {
  toastMessage.value = message
  showToast.value = true

  clearTimeout(toastTimeout)
  toastTimeout = setTimeout(() => {
    showToast.value = false
  }, 2500)
}

const formatDate = (date) => {
  if (!date) return 'Not published yet'
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const copyUrl = () => {
  navigator.clipboard.writeText(window.location.href)
    .then(() => showNotification('URL copied to clipboard!'))
    .catch(() => showNotification('Failed to copy URL'))
}

function nl2brSafe(text) {
  if (!text) return ''
  // ako već ima HTML tagove, ne diraj (da ne dupliraš br)
  if (/<[a-z][\s\S]*>/i.test(text)) return text
  return text
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/\r\n/g, '\n')
    .replace(/\r/g, '\n')
    .replace(/\n/g, '<br>')
}
</script>
<style scoped>
/* Važi za content koji dolazi kroz v-html */
.prose :deep(p),
.prose :deep(li),
.prose :deep(div) {
  white-space: pre-line;
}
</style>