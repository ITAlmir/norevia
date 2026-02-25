<!-- resources/js/Pages/ContentAdmin/Dashboard.vue - Popravljeno -->
<template>
  <MainLayout>
    <div class="max-w-7xl mx-auto px-4 py-8">
      <!-- Header -->
      <div class="mb-8 bg-white border border-slate-200 text-slate-900
            dark:bg-slate-900/40 dark:border-slate-700 dark:text-slate-100">
        <h1 class="text-3xl font-bold">Content Admin Dashboard</h1>
        <p class="text-slate-600 dark:text-slate-300">
Manage your pages and content</p>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-slate-800/50 rounded-xl p-6 border border-slate-700">
          <div class="text-3xl font-bold text-blue-400">{{ stats?.totalPages || 0 }}</div>
          <div class="text-gray-400 text-sm">Total Pages</div>
        </div>
        <div class="bg-slate-800/50 rounded-xl p-6 border border-slate-700">
          <div class="text-3xl font-bold text-green-400">{{ stats?.publishedPages || 0 }}</div>
          <div class="text-gray-400 text-sm">Published</div>
        </div>
        <div class="bg-slate-800/50 rounded-xl p-6 border border-slate-700">
          <div class="text-3xl font-bold text-purple-400">{{ stats?.totalItems || 0 }}</div>
          <div class="text-gray-400 text-sm">Total Items</div>
        </div>
        <div class="bg-slate-800/50 rounded-xl p-6 border border-slate-700">
          <div class="text-3xl font-bold text-yellow-400">{{ stats?.totalCategories || 0 }}</div>
          <div class="text-gray-400 text-sm">Categories</div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="bg-slate-800/50 rounded-xl border border-slate-700 p-6 mb-8">
        <h2 class="text-xl font-bold text-gray-100 mb-4">Quick Actions</h2>
        <div class="flex flex-wrap gap-4">
          <Link 
            href="/content-admin/pages/create" 
            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium flex items-center"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create New Page
          </Link>
          <Link 
            href="/content-admin/pages" 
            class="px-4 py-2 rounded-lg font-medium text-white
                 bg-gradient-to-r from-green-700 to-green-800
                 hover:from-green-600 hover:to-slate-700"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
            </svg>
            View All Pages
          </Link>
          <Link
          href="/dashboard"
          class="px-4 py-2 rounded-lg font-medium text-white
                 bg-gradient-to-r from-pink-500 to-slate-600
                 hover:from-pink-600 hover:to-slate-700"
        >
          <- Back To Dashboard
        </Link>
        </div>
      </div>

      <!-- Recent Pages -->
      <div class="bg-slate-800/50 rounded-xl border border-slate-700 overflow-hidden">
        <div class="p-6 border-b border-slate-700">
          <div class="flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-100">Recent Pages</h2>
            <Link href="/content-admin/pages" class="text-blue-400 hover:text-blue-300 text-sm">
              View all →
            </Link>
          </div>
        </div>
        
        <div v-if="recentPages && recentPages.length > 0">
          <div v-for="page in recentPages" :key="page.id" class="p-6 border-b border-slate-700 last:border-b-0 hover:bg-slate-800/30">
            <div class="flex justify-between items-start">
              <div class="flex-1">
                <h3 class="text-lg font-medium text-gray-100 mb-1">{{ page.title }}</h3>
                <p class="text-gray-400 text-sm mb-2">{{ page.excerpt }}</p>
                <div class="flex items-center space-x-4">
                  <span :class="['px-2 py-1 text-xs rounded-full', 
                    page.status === 'published' ? 'bg-green-900/30 text-green-400' :
                    page.status === 'draft' ? 'bg-yellow-900/30 text-yellow-400' :
                    'bg-slate-700 text-gray-300'
                  ]">
                    {{ page.status }}
                  </span>
                  <span class="text-gray-500 text-sm">
                    Created: {{ formatDate(page.created_at) }}
                  </span>
                </div>
              </div>
              <div class="flex space-x-2 ml-4">
                <Link 
                  :href="`/content-admin/pages/${page.id}/edit`" 
                  class="px-3 py-1 text-sm text-blue-400 hover:text-blue-300 border border-blue-700 rounded-md"
                >
                  Edit
                </Link>
                <a 
  :href="`/content-admin/pages/${page.id}`" 
  target="_blank"
  class="px-3 py-1 text-sm text-green-400 hover:text-green-300 border border-green-700 rounded-md"
>
  View
</a>
                  
              </div>
            </div>
            
            <!-- URL za kopiranje -->
            <div class="mt-4 flex items-center bg-slate-900/50 rounded-md p-2">
              <input 
                type="text" 
                readonly 
                :value="`${$page.props.url}/pages/${page.slug}`" 
                class="flex-1 bg-transparent text-gray-400 text-sm px-2 py-1 focus:outline-none"
              />
              <button 
                @click="copyUrl(`${$page.props.url}/pages/${page.slug}`)"
                class="ml-2 px-3 py-1 bg-slate-700 hover:bg-slate-600 text-gray-300 text-sm rounded-md"
              >
                Copy URL
              </button>
            </div>
          </div>
        </div>
        
        <div v-else class="p-12 text-center">
          <svg class="w-12 h-12 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <p class="text-gray-500 mb-4">You haven't created any pages yet.</p>
          <Link 
            href="/content-admin/pages/create" 
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
          >
            Create Your First Page
          </Link>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  stats: {
    type: Object,
    default: () => ({})
  },
  recentPages: {
    type: Array,
    default: () => []
  }
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const copyUrl = async (url) => {
  try {
    await navigator.clipboard.writeText(url)
    console.log('Copied')
  } catch (e) {
    console.error(e)
  }
}

</script>