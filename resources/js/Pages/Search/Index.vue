<template>
  <MainLayout>
    <SeoHead :seo="seo" />

    <div class="max-w-7xl mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold text-white mb-4">Search</h1>

      <!-- Search Box -->
      <div class="mb-8">
        <div class="relative">
          <input
            v-model="query"
            type="text"
            placeholder="Search for tools, apps, guides..."
            class="w-full rounded-lg bg-slate-800 border border-slate-600 px-4 py-3 text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
            @keyup.enter="search"
          >
          <button
            @click="search"
            class="absolute right-0 top-0 h-full px-6 bg-blue-600 hover:bg-blue-700 text-white rounded-r-lg font-medium"
          >
            Search
          </button>
        </div>

        <div v-if="query" class="mt-2 text-gray-400">
          Showing results for: <span class="text-blue-400">{{ query }}</span>
        </div>
      </div>

      <!-- Results -->
      <div v-if="results.length > 0" class="space-y-6">
        <div
          v-for="item in results"
          :key="item.id"
          class="bg-slate-800/50 rounded-xl p-6 border border-slate-700 hover:border-slate-600 transition"
        >
          <div class="flex items-start gap-4">
            <div class="text-4xl">{{ item.icon }}</div>
            <div class="flex-1">
              <div class="flex items-center gap-2 mb-2">
                <span class="text-xs font-semibold px-3 py-1 rounded-full bg-slate-700/50 text-gray-300">{{ item.category }}</span>
                <span v-if="item.is_free" class="text-xs font-bold px-3 py-1 rounded-full bg-green-900/50 text-green-300">FREE</span>
                <span v-else class="text-xs font-bold px-3 py-1 rounded-full bg-blue-900/50 text-blue-300">{{ item.price }}</span>
              </div>
              <h3 class="text-xl font-bold text-white mb-2">{{ item.title }}</h3>
              <p class="text-gray-400 mb-4">{{ item.description }}</p>

              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <span class="text-yellow-400 mr-1">★</span>
                  <span class="text-sm font-semibold text-gray-300">{{ item.rating }}</span>
                  <span class="text-xs text-gray-500 ml-2">({{ item.downloads }})</span>
                </div>

                <!-- For now keep as plain link; later map to real routes -->
                <a
                  :href="`/${item.category.toLowerCase()}/${item.slug}`"
                  class="text-blue-400 hover:text-blue-300 text-sm font-semibold"
                >
                  View Details →
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- No Results -->
      <div v-else class="text-center py-12">
        <div class="text-5xl mb-4">🔍</div>
        <h3 class="text-xl font-bold text-white mb-2">No results found</h3>
        <p class="text-gray-400">Try a different keyword.</p>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import SeoHead from '@/Components/SeoHead.vue'
import { siteUrl } from '@/Utils/seo'

const page = usePage()
const query = ref(page.props.q || '')

const seo = computed(() => ({
  title: query.value ? `Search: ${query.value} | Norevia` : 'Search | Norevia',
  description: 'Site search results. Use this page to find tools, downloads and guides.',
  canonical: siteUrl('/search'),
  robots: 'noindex,follow',
  og: {
    'og:title': query.value ? `Search: ${query.value} | Norevia` : 'Search | Norevia',
    'og:description': 'Site search results. Use this page to find tools, downloads and guides.',
    'og:type': 'website',
    'og:url': siteUrl('/search'),
  },
}))

// Mock data (OK for now)
const allItems = [
  { id: 1, title: 'GameBoost Pro', slug: 'gameboost-pro', description: 'AI game optimization tool', category: 'Gaming', rating: 4.8, downloads: '12.5K', is_free: true, icon: '🎮' },
  { id: 2, title: 'DevSuite Pro', slug: 'devsuite-pro', description: 'Complete development environment', category: 'Tools', rating: 4.6, downloads: '8.2K', is_free: false, price: '$49.99', icon: '⚙️' },
  { id: 3, title: 'FlowTime', slug: 'flowtime', description: 'Productivity timer app', category: 'Apps', rating: 4.9, downloads: '15.3K', is_free: true, icon: '📱' },
  { id: 4, title: 'FPS Optimizer', slug: 'fps-optimizer', description: 'Boost FPS in games', category: 'Gaming', rating: 4.5, downloads: '8.7K', is_free: false, price: '$19.99', icon: '🎮' },
  { id: 5, title: 'API Tester', slug: 'api-tester', description: 'Test REST APIs easily', category: 'Tools', rating: 4.7, downloads: '5.4K', is_free: true, icon: '⚙️' },
  { id: 6, title: 'NoteMaster', slug: 'notemaster', description: 'Note taking application', category: 'Apps', rating: 4.8, downloads: '9.1K', is_free: true, icon: '📱' },
]

const results = computed(() => {
  if (!query.value.trim()) return []
  const q = query.value.toLowerCase()
  return allItems.filter(item =>
    item.title.toLowerCase().includes(q) ||
    item.description.toLowerCase().includes(q) ||
    item.category.toLowerCase().includes(q)
  )
})

function search() {
  const q = query.value.trim()
  if (!q) return
  router.get('/search', { q }, { preserveState: true, replace: true })
}
</script>
