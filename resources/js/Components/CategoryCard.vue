<!-- resources/js/components/CategoryCard.vue -->
<template>
  <Link :href="'/' + category.name.toLowerCase()" class="block group">
    <div class="bg-slate-800/50 rounded-xl p-6 border border-slate-700 hover:border-slate-600 transition-all duration-300 hover:bg-slate-800/70">
      <div class="flex items-start justify-between mb-4">
        <div class="text-4xl">{{ category.icon }}</div>
        <span class="text-xs font-semibold px-3 py-1 rounded-full bg-slate-700/50 text-gray-300">
          {{ category.items_count }} items
        </span>
      </div>
      
      <h3 class="text-xl font-bold text-gray-100 mb-2 group-hover:text-white">{{ category.name }}</h3>
      <p class="text-gray-400 text-sm">{{ category.description }}</p>
      
      <div class="flex items-center text-blue-400 group-hover:text-blue-300 mt-4">
        <span class="text-sm font-semibold">Explore</span>
        <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
        </svg>
      </div>
    </div>
  </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  category: Object
})
</script>