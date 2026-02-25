<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const items = ref([])
const loading = ref(true)

onMounted(async () => {
  try {
    const res = await axios.get('/partials/latest-downloads')
    items.value = res.data.items || []
  } finally {
    loading.value = false
  }
})
const badgeClass = (status) => {
  if (status === 'clean') return 'bg-emerald-500/15 border-emerald-400/30 text-emerald-200'
  if (status === 'flagged') return 'bg-red-500/15 border-red-400/30 text-red-200'
  return 'bg-slate-900/50 border-slate-700 text-gray-300'
}
</script>

<template>
  <div class="bg-slate-900/40 border border-slate-700 rounded-2xl p-5">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h3 class="text-white font-semibold text-lg">Latest downloads</h3>
        <p class="text-gray-400 text-sm">Fresh tools & utilities</p>
      </div>
      <a href="/downloads" class="text-blue-300 hover:text-blue-200 text-sm">View all →</a>
    </div>

    <div v-if="loading" class="text-gray-400 text-sm">Loading...</div>

    <div v-else-if="!items.length" class="text-gray-400 text-sm">
      No downloads yet.
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <a
        v-for="d in items"
        :key="d.id"
        :href="`/downloads/${d.slug}`"
        class="block bg-slate-800/40 border border-slate-700 rounded-xl p-4 hover:border-slate-500 transition"
      >
        <div class="flex items-center justify-between gap-2">
          <div class="text-gray-200 font-semibold truncate">{{ d.title }}</div>

          <div class="flex items-center gap-2 shrink-0">
            <span
              v-if="d.is_featured"
              class="text-[11px] px-2 py-0.5 rounded-full bg-amber-500/20 border border-amber-400/30 text-amber-200"
            >
              Featured
            </span>

            <span
    v-if="d.os"
    class="text-[11px] px-2 py-0.5 rounded-full bg-indigo-500/15 border border-indigo-400/30 text-indigo-200"
  >
    {{ d.os }}
  </span>
          </div>
        </div>

        <div class="text-gray-500 text-xs mt-1">{{ d.category }} • {{ d.version || '-' }}</div>
        <div class="text-gray-400 text-xs mt-2">Downloads: {{ d.download_count }}</div>
      </a>
    </div>
  </div>
</template>
