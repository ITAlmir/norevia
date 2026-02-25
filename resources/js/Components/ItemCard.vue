<!-- resources/js/components/ItemCard.vue - MODIFIKOVANA -->
<template>
  <div class="bg-slate-800/50 rounded-xl p-6 border border-slate-700 hover:border-slate-600 transition-all duration-300 hover:scale-[1.02] hover:bg-slate-800/70 h-full flex flex-col">
    <!-- Gornji dio ostaje isti -->
    <div class="flex items-start justify-between mb-4">
      <div class="text-3xl">{{ item.icon }}</div>
      <div class="flex flex-col items-end gap-1">
        <span class="text-xs font-semibold px-2 py-1 rounded bg-slate-700/50 text-gray-300">{{ item.category }}</span>
        <span v-if="item.is_free" class="text-xs font-bold px-2 py-1 rounded bg-green-900/50 text-green-300">FREE</span>
        <span v-else class="text-xs font-bold px-2 py-1 rounded bg-blue-900/50 text-blue-300">{{ item.price }}</span>
      </div>
    </div>
    
    <h3 class="text-lg font-bold text-gray-100 mb-2">{{ item.title }}</h3>
    <p class="text-gray-400 text-sm mb-4 flex-grow">{{ item.description }}</p>
    
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center">
        <span class="text-yellow-400 mr-1">★</span>
        <span class="text-sm font-semibold text-gray-300">{{ item.rating }}</span>
        <span class="text-xs text-gray-500 ml-2">({{ item.downloads }})</span>
      </div>
    </div>
    
    <!-- AKCIJE: View Details ili Download -->
    <div class="mt-auto pt-4 border-t border-slate-700">
      <div class="flex justify-between items-center">
        <!-- VIEW DETAILS link -->
        <Link :href="'/' + item.category.toLowerCase() + '/' + item.slug" 
              class="text-blue-400 hover:text-blue-300 text-sm font-semibold">
          View Details →
        </Link>
        
        <!-- DOWNLOAD button (samo ako je item dostupan za download) -->
        <button v-if="item.downloadable"
                @click="handleDownload"
                :class="downloadButtonClass"
                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
        >
          {{ downloadButtonText }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  item: Object
})

const isLoggedIn = computed(() => props.$page?.props?.auth?.user)

const downloadButtonText = computed(() => {
  if (props.item.is_free) return 'Download Free'
  return isLoggedIn.value ? 'Download' : 'Login to Download'
})

const downloadButtonClass = computed(() => {
  if (props.item.is_free) {
    return 'bg-green-600 hover:bg-green-700 text-white'
  }
  return isLoggedIn.value ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-gray-600 hover:bg-gray-700 text-gray-300'
})

function handleDownload() {
  if (props.item.is_free) {
    window.location.href = `/download/free/${props.item.id}`
  } else {
    if (!isLoggedIn.value) {
      router.visit('/login', {
        data: { 
          message: 'Please login to download premium items',
          redirect: `/download/${props.item.id}`
        }
      })
    } else {
      window.location.href = `/download/${props.item.id}`
    }
  }
}
</script>