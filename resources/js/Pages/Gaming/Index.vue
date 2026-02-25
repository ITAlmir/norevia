<!-- resources/js/pages/Gaming/Index.vue -->
<template>
  <MainLayout>
    <div class="max-w-7xl mx-auto px-4 py-8">
      <div class="text-center mb-12">
        <div class="text-6xl mb-4">🎮</div>
        <h1 class="text-4xl font-bold text-gray-100 mb-4">Gaming</h1>
        <p class="text-lg text-gray-400">Tools, mods, and software for gamers</p>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <ItemCard 
          v-for="item in items" 
          :key="item.id" 
          :item="item" 
        />
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
    title: 'GameBoost Pro', 
    description: 'AI game optimization', 
    category: 'Gaming', 
    rating: 4.8, 
    downloads: '12.5K', 
    is_free: true, 
    icon: '🎮',
    platform: 'Windows'
  },
  { 
    id: 2, 
    title: 'FPS Optimizer', 
    description: 'Boost FPS in games', 
    category: 'Gaming', 
    rating: 4.5, 
    downloads: '8.7K', 
    is_free: false, 
    price: '$19.99', 
    icon: '🎮',
    platform: 'Windows'
  },
]
</script>