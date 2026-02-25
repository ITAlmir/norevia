<!-- resources/js/pages/Items/Show.vue -->
<template>
  <MainLayout>
    <div class="max-w-7xl mx-auto px-4 py-8">
      <div class="bg-slate-800/50 rounded-xl p-8 border border-slate-700">
        <div class="flex items-start gap-6">
          <div class="text-5xl">{{ item.icon }}</div>
          <div class="flex-1">
            <div class="flex items-center gap-2 mb-2">
              <span class="text-xs font-semibold px-3 py-1 rounded-full bg-slate-700/50 text-gray-300">{{ item.category }}</span>
              <span v-if="item.is_free" class="text-xs font-bold px-3 py-1 rounded-full bg-green-900/50 text-green-300">FREE</span>
              <span v-else class="text-xs font-bold px-3 py-1 rounded-full bg-blue-900/50 text-blue-300">{{ item.price }}</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-100 mb-4">{{ item.title }}</h1>
            <p class="text-gray-300 mb-6">{{ item.description }}</p>
            
            <div class="grid grid-cols-2 gap-4 mb-6">
              <div><span class="text-gray-400">Rating:</span> <span class="text-yellow-400 font-bold">{{ item.rating }}/5.0</span></div>
              <div><span class="text-gray-400">Downloads:</span> <span class="text-gray-300">{{ item.downloads }}</span></div>
              <div><span class="text-gray-400">Platform:</span> <span class="text-gray-300">{{ item.platform || 'Windows' }}</span></div>
              <div><span class="text-gray-400">Price:</span> <span class="text-gray-300">{{ item.is_free ? 'Free' : 'Paid' }}</span></div>
            </div>
            
            <button class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-colors">
              Visit Website
            </button>
             <div class="mt-8 pt-8 border-t border-slate-700">
    <button @click="handleDownload" 
            class="w-full py-4 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl text-lg font-bold">
      Download
    </button>
    
    <p class="text-gray-400 text-sm mt-4 text-center">
      {{ item.is_free ? 'Free download - no registration required' : 'Premium download requires login' }}
    </p>
  </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()

// Mock data za sada
const item = computed(() => ({
  id: 1,
  title: page.props.item || 'GameBoost Pro',
  category: page.props.category || 'Gaming',
  description: 'AI-powered game optimization tool that automatically adjusts settings for best performance in any game.',
  rating: 4.8,
  downloads: '12.5K',
  is_free: true,
  price: '$49.99',
  platform: 'Windows, Linux',
  icon: '🎮'
}))
</script>