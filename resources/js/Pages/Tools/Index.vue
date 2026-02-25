<template>
  <MainLayout>
    <div class="max-w-7xl mx-auto px-4 py-8">
      <div class="text-center mb-12">
        <div class="text-6xl mb-4">⚙️</div>
        <h1 class="text-4xl font-bold text-gray-100 mb-4">Tools</h1>
        <p class="text-lg text-gray-400">Essential software for developers</p>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
      </div>
    </div>
    <div class="max-w-7xl mx-auto px-4 py-10">
      <LatestDownloads />
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
import LatestDownloads from '@/Components/LatestDownloads.vue'

const items = [
  { 
    id: 1, 
    title: 'DevSuite Pro', 
    description: 'Development environment', 
    category: 'Tools', 
    rating: 4.6, 
    downloads: '8.2K', 
    is_free: false, 
    price: '$49.99', 
    icon: '⚙️',
    platform: 'Cross-platform'
  },
]
</script>