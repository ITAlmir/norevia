<!-- resources/js/Pages/ContentAdmin/Pages/Index.vue -->
<template>
  <MainLayout>
    <div class="max-w-7xl mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold text-white mb-2">My Pages</h1>
      <p class="text-gray-400 mb-6">Manage your created pages</p>
      
      <!-- HIGHLIGHT HEADER -->
      <div class="flex justify-between items-center mb-8 p-6 bg-slate-800/40 rounded-xl border border-slate-700">
        <h2 class="text-xl font-bold text-gray-100">All Pages</h2>
        <Link
          href="/dashboard"
          class="px-4 py-2 rounded-lg font-medium text-white
                 bg-gradient-to-r from-blue-700 to-slate-800
                 hover:from-blue-600 hover:to-slate-700"
        >
          <- Back To Dashboard
        </Link>
        <Link href="/content-admin/pages/create" 
           class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors hover:scale-105">
          Create New Page
        </Link>
      </div>
      
      <!-- Filter UI -->
      <div class="mb-6 p-4 bg-slate-800/40 rounded-xl border border-slate-700">
  <div class="grid grid-cols-1 md:grid-cols-6 gap-3">
    <!-- Search -->
    <input
      v-model="filterForm.search"
      @keyup.enter="applyFilters"
      placeholder="Search title/slug/meta..."
      class="md:col-span-2 w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-gray-200"
    />

    <!-- Status -->
    <select v-model="filterForm.status" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-gray-200">
      <option value="">All status</option>
      <option value="draft">Draft</option>
      <option value="published">Published</option>
      <option value="archived">Archived</option>
    </select>

    <!-- Category (page_type) -->
    <select v-model="filterForm.page_type" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-gray-200">
      <option value="">All categories</option>
      <option value="post">Post</option>
      <option value="news">News</option>
      <option value="gaming">Gaming</option>
      <option value="horoscope">Horoscope</option>
    </select>

    <!-- Kind (type) -->
    <select v-model="filterForm.type" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-gray-200">
      <option value="">All kinds</option>
      <option value="page">Page</option>
      <option value="blog">Blog post</option>
    </select>

    <!-- Layout -->
    <select v-model="filterForm.layout" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-gray-200">
      <option value="">All layouts</option>
      <option value="minimal">Minimal</option>
      <option value="classic">Classic</option>
      <option value="magazine">Magazine</option>
      <option value="hero">Hero</option>
    </select>

    <!-- Topic (only meaningful for blog) -->
    <select v-model="filterForm.topic" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-gray-200">
      <option value="">All topics</option>
      <option value="cs2">CS2</option>
      <option value="pc-optimization">PC Optimization</option>
      <option value="creator-tools">Creator Tools</option>
      <option value="gaming">Gaming</option>
    </select>
  </div>

  <div class="mt-3 flex items-center justify-between gap-3">
    <select v-model="filterForm.sort" class="bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-gray-200">
      <option value="latest">Newest first</option>
      <option value="oldest">Oldest first</option>
      <option value="title">Title A→Z</option>
    </select>

    <div class="flex gap-2">
      <button @click="applyFilters" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
        Apply
      </button>
      <button @click="resetFilters" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-gray-200 rounded-lg">
        Reset
      </button>
    </div>
  </div>
</div>


      <div class="bg-slate-800/30 rounded-xl border border-slate-700 overflow-hidden">

  <!-- ✅ MOBILE CARDS -->
  <div class="lg:hidden p-4 space-y-3">
    <div
      v-for="page in (pages.data || pages)"
      :key="page.id"
      class="rounded-xl border border-slate-700 bg-slate-900/30 p-4"
    >
      <!-- TITLE -->
      <div
        class="font-semibold bg-gradient-to-r from-amber-300 via-yellow-400 to-amber-500 bg-clip-text text-transparent"
      >
        {{ page.title }}
      </div>

      <div class="mt-2 flex flex-wrap gap-2 items-center">
        <span class="px-2 py-1 rounded-full text-xs text-cyan-200 border border-cyan-400/30">
          {{ page.type === 'blog' ? 'Blog post' : 'Page' }}
        </span>

        <span class="px-2 py-1 rounded-full text-xs bg-cyan-100/10 text-cyan-200 border border-cyan-400/20">
          {{ page.page_type }}
        </span>

        <span :class="[
          'px-2 py-1 rounded-full text-xs font-semibold',
          page.status === 'published' ? 'bg-green-900/40 text-green-300 border border-green-800/50' :
          page.status === 'draft' ? 'bg-yellow-900/40 text-yellow-300 border border-yellow-800/50' :
          'bg-slate-700/50 text-gray-300 border border-slate-600'
        ]">
          {{ page.status }}
        </span>

        <span class="px-2 py-1 rounded-full text-xs text-cyan-300 border border-cyan-500/20">
          {{ page.layout }}
        </span>

        <span v-if="page.type === 'blog' && page.topic" class="text-xs text-slate-400">
          Hub: <span class="text-slate-200">{{ page.topic }}</span>
        </span>
      </div>

      <div class="mt-3 text-sm text-slate-300">
        <span class="text-slate-500">Author:</span>
        {{ page.author?.name || 'Unknown' }}
      </div>

      <div class="mt-3 rounded-lg border border-slate-700 bg-slate-950/40 p-3">
        <div class="text-xs text-slate-400">
          {{ formatDate(page.created_at) }}
        </div>

        <Link
          :href="getPublicUrl(page)"
          target="_blank"
          class="mt-1 block text-blue-400 hover:text-blue-300 text-xs hover:underline truncate"
          :title="getPublicUrl(page)"
        >
          {{ shortUrl(getPublicUrl(page)) }}
        </Link>

        <div class="mt-2 flex flex-wrap gap-2">
          <button
            v-if="$page.props.auth.user?.role === 'content_admin' || $page.props.auth.user?.role === 'super_admin'"
            @click="copyPublicUrl(page)"
            class="px-3 py-1.5 bg-slate-700 hover:bg-slate-600 text-gray-200 rounded-lg text-xs"
          >
            Copy URL
          </button>

          <Link
            :href="`/content-admin/pages/${page.id}/edit`"
            class="px-3 py-1.5 bg-slate-700 hover:bg-slate-600 text-gray-200 rounded-lg text-xs"
          >
            Edit
          </Link>

          <button
            @click="confirmDelete(page)"
            class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs"
          >
            Delete
          </button>
        </div>
      </div>
    </div>
  </div>
          <div class="hidden lg:block overflow-x-auto">
    <table class="w-full divide-y divide-slate-700 table-fixed">
            <thead>
              <tr class="bg-slate-800/50">
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Title/Kind</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Status/Category</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Author/Layout</th>
                <th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider w-[280px]">
  URL
</th>

<th class="px-6 py-4 text-left text-xs font-medium text-gray-400 uppercase tracking-wider w-[220px]">
  Actions
</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800 bg-slate-800/20">
              <tr v-if="!pages || (pages.data && pages.data.length === 0) || (!pages.data && pages.length === 0)">
                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                  <div class="flex flex-col items-center">
                    <svg class="w-12 h-12 text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-lg">No pages created yet.</p>
                    <p class="text-sm text-gray-600 mt-1">Create your first page to get started</p>
                  </div>
                </td>
              </tr>
              
              <template v-if="pages && (pages.data || pages.length > 0)">
                <tr 
                  v-for="page in (pages.data || pages)" 
                  :key="page.id" 
                  class="hover:bg-slate-800/40 transition-colors duration-150"
                >
                  <!-- Title -->
                  <td class="px-6 py-4">
                    <div class="font-semibold bg-gradient-to-r from-amber-300 via-yellow-400 to-amber-500 bg-clip-text text-transparent">
  {{ page.title }}
</div>

<div class="text-xs text-slate-400">
  <span :class="[
                          'px-2 py-1 rounded text-xs font-small',
                          ' text-cyan-200 border border-cyan-400/30',
                          'rounded-full'
                        ]">
  {{ page.type === 'blog' ? 'Blog post' : 'Page' }}
  </span>
</div>
<div v-if="page.type === 'blog' && page.topic" class="mt-1 text-xs text-slate-500">
  Hub: <span class="text-slate-300">{{ page.topic }}</span>
</div>
                  </td>
                  
                  <!-- Status -->
                  <td class="px-6 py-4">
                    <div>
                    <span :class="[
                      'px-3 py-1.5 text-xs rounded-full font-semibold tracking-wide',
                      page.status === 'published' ? 'bg-green-900/40 text-green-300 border border-green-800/50' :
                      page.status === 'draft' ? 'bg-yellow-900/40 text-yellow-300 border border-yellow-800/50' :
                      'bg-slate-700/50 text-gray-300 border border-slate-600'
                    ]">
                      {{ page.status }}
                    </span>
                    </div>
                    <div class="text-xs text-slate-400">
                      <span :class="[
                          'px-2 py-1 rounded text-xs font-small',
                          'bg-cyan-100/10 text-cyan-200 border border-cyan-400/20 backdrop-blur-sm',
                          'rounded-full'
                        ]">
  {{ page.page_type }}
                      </span>
</div>
                  </td>
                  
                  <!-- Author -->
                  <td class="px-6 py-4">
                    <div v-if="page.author" class="text-sm">
                      <div class="font-medium text-gray-300">{{ page.author.name }}</div>
                      <div class="mt-1">
                        <span :class="[
                          'px-2 py-1 rounded text-xs font-medium',
                          ' text-cyan-300 border border-cyan-500/20'
                        ]">
                          {{ page.layout }}
                        </span>
                      </div>
                    </div>
                    <div v-else class="text-sm text-gray-500 italic">
                      Unknown
                    </div>
                  </td>
                  
                  <!-- URL -->
                  <td class="px-6 py-4">
                    <div class="space-y-2">
                      <div class="flex items-center gap-2">
                        <div class="flex flex-col">
  <span class="text-slate-200 text-sm">
    {{ formatDate(page.created_at) }}
  </span>

  <Link
    :href="getPublicUrl(page)"
    target="_blank"
    class="text-blue-400 hover:text-blue-300 text-xs hover:underline truncate"
    :title="getPublicUrl(page)"
  >
  </Link>
</div>
                          
                        <!-- SAMO ZA ADMINE -->
                        <template v-if="$page.props.auth.user?.role === 'content_admin' || $page.props.auth.user?.role === 'super_admin'">
                          <button 
                            @click="copyPublicUrl(page)"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-slate-700 hover:bg-slate-600 text-gray-300 hover:text-white rounded-lg text-xs font-medium transition-colors hover:scale-105"
                            title="Copy public URL"
                          >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            Copy
                          </button>
                        </template>
                      </div>
                      <div class="text-xs text-gray-500 truncate bg-slate-900/30 p-2.5 rounded-lg font-mono">
                            {{ shortUrl(getPublicUrl(page)) }}
                      </div>
                    </div>
                  </td>
                  
                  <!-- Actions -->
                  <td class="px-6 py-4">
  <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-end">

    <div class="flex flex-wrap gap-2">
      <Link
        :href="`/content-admin/pages/${page.id}/edit`"
        class="px-3.5 py-1.5 bg-slate-700 hover:bg-slate-600 text-gray-300 hover:text-white rounded-lg text-sm font-medium inline-flex items-center gap-1.5 transition-colors"
      >
        <!-- icon -->
        Edit
      </Link>

      <Link
        :href="getPublicUrl(page)"
        target="_blank"
        class="px-3.5 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium inline-flex items-center gap-1.5 transition-colors"
      >
        View
      </Link>
    </div>

    <button
      @click="confirmDelete(page)"
      class="px-3.5 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium inline-flex items-center gap-1.5 transition-colors"
    >
      Delete
    </button>
  </div>
</td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="pages && pages.links" class="px-6 py-5 border-t border-slate-700 bg-slate-800/20">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-400">
              Showing {{ pages.from }} to {{ pages.to }} of {{ pages.total }} pages
            </div>
            <div class="flex space-x-1">
              <Link
                v-for="link in pages.links" 
                :key="link.label"
                :href="link.url || '#'"
                :class="[
                  'px-3 py-1.5 rounded-lg text-sm font-medium transition-colors',
                  link.active ? 'bg-blue-600 text-white' : 'bg-slate-700 text-gray-300 hover:bg-slate-600 hover:text-white',
                  !link.url ? 'text-gray-500 cursor-not-allowed opacity-50' : 'hover:scale-105'
                ]"
                v-html="link.label"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- DELETE MODAL -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center">
      <!-- Overlay -->
      <div class="fixed inset-0 bg-black/80 backdrop-blur-sm" @click="closeDeleteModal"></div>
      
      <!-- Modal -->
      <div class="relative bg-slate-800 border-2 border-slate-700 rounded-2xl max-w-md w-full p-8 mx-4 shadow-2xl">
        <div class="text-center mb-6">
          <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-red-900/40 mb-4">
            <svg class="h-7 w-7 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
          </div>
          
          <h3 class="text-2xl font-bold text-white mb-2">Delete Page</h3>
          <p class="text-gray-300">
            Delete "<span class="text-white font-semibold">{{ pageToDelete?.title }}</span>"?
          </p>
          <p class="text-red-400 text-sm mt-2">
            This action cannot be undone
          </p>
        </div>
        
        <div class="flex gap-4">
          <button 
            @click="closeDeleteModal"
            class="flex-1 px-5 py-3 bg-slate-700 hover:bg-slate-600 text-gray-300 hover:text-white rounded-xl text-lg font-medium transition-colors hover:scale-105"
          >
            Cancel
          </button>
          <button 
            @click="deletePage"
            class="flex-1 px-5 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl text-lg font-medium transition-colors hover:scale-105"
          >
            Delete
          </button>
        </div>
      </div>
    </div>

    <!-- TOAST NOTIFICATION -->
    <div v-if="showToast" class="fixed top-6 right-6 z-50 animate-toast-in">
      <div :class="[
        'px-5 py-4 rounded-xl shadow-2xl border backdrop-blur-sm',
        toastType === 'success' ? 'bg-green-900/90 border-green-700 text-green-100' :
        toastType === 'error' ? 'bg-red-900/90 border-red-700 text-red-100' :
        'bg-blue-900/90 border-blue-700 text-blue-100'
      ]">
        <div class="flex items-center gap-3">
          <svg v-if="toastType === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
          <svg v-else-if="toastType === 'error'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
          <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <span class="font-medium">{{ toastMessage }}</span>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'


const props = defineProps({
  pages: {
    type: [Array, Object],
    default: () => []
  },
  filters: { type: Object, default: () => ({}) },
})

//filterForm table
const filterForm = ref({
  search: props.filters.search ?? '',
  status: props.filters.status ?? '',
  page_type: props.filters.page_type ?? '',
  type: props.filters.type ?? '',
  topic: props.filters.topic ?? '',
  layout: props.filters.layout ?? '',
  sort: props.filters.sort ?? 'latest',
})

const applyFilters = () => {
  router.get('/content-admin/pages', filterForm.value, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
  })
}

const resetFilters = () => {
  filterForm.value = {
    search: '',
    status: '',
    page_type: '',
    type: '',
    topic: '',
    layout: '',
    sort: 'latest',
  }
  applyFilters()
}
// Delete modal state
const showDeleteModal = ref(false)
const pageToDelete = ref(null)

// Toast state
const showToast = ref(false)
const toastMessage = ref('')
const toastType = ref('success')
let toastTimeout = null

// Toast functions
const showNotification = (message, type = 'success') => {
  toastMessage.value = message
  toastType.value = type
  showToast.value = true
  
  clearTimeout(toastTimeout)
  toastTimeout = setTimeout(() => {
    showToast.value = false
  }, 3000)
}

// Delete functions
const confirmDelete = (page) => {
  pageToDelete.value = page
  showDeleteModal.value = true
}

const deletePage = () => {
  if (!pageToDelete.value) return
  
  router.delete(`/content-admin/pages/${pageToDelete.value.id}`, {
  preserveScroll: true,
  onSuccess: () => {
    showNotification('Page deleted successfully', 'success')
    router.reload({ preserveScroll: true })
  },
  onError: () => {
    showNotification('Error deleting page', 'error')
  }
})
  
  showDeleteModal.value = false
  pageToDelete.value = null
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  pageToDelete.value = null
}

// URL functions
const getPublicPath = (page) => {
  if (page.type === 'blog' && page.topic) return `/blog/${page.topic}/${page.slug}`
  return `/pages/${page.slug}`
}

const getPublicUrl = (page) => {
  return window.location.origin + getPublicPath(page)
}


const copyPublicUrl = (page) => {
  const url = getPublicUrl(page)
  navigator.clipboard.writeText(url)
    .then(() => {
      showNotification('URL copied to clipboard', 'success')
    })
    .catch(err => {
      console.error('Copy failed:', err)
      showNotification('Failed to copy URL', 'error')
    })
}

const formatDate = (iso) => {
  if (!iso) return '-'
  const d = new Date(iso)
  return d.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: '2-digit',
  })
}

const shortUrl = (url) => {
  const s = String(url || '')
  if (!s) return ''

  const cleaned = s.replace(/^https?:\/\//i, '')

  return cleaned.length > 50
    ? cleaned.slice(0, 50) + '…'
    : cleaned
}
</script>

<style scoped>
/* Toast animation */
@keyframes toast-in {
  0% {
    opacity: 0;
    transform: translateX(100%) translateY(-20px);
  }
  100% {
    opacity: 1;
    transform: translateX(0) translateY(0);
  }
}

.animate-toast-in {
  animation: toast-in 0.3s ease-out forwards;
}

/* Table row hover */
tr:hover {
  background: rgba(30, 41, 59, 0.5) !important;
}

/* Smooth button hover */
button, a {
  transition: all 0.2s ease;
}

button:hover:not(:disabled), a:hover {
  transform: translateY(-2px);
}

/* Modal backdrop blur */
.backdrop-blur-sm {
  backdrop-filter: blur(4px);
}
</style>