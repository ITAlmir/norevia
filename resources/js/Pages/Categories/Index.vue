<template>
  <MainLayout>
    <div class="max-w-7xl mx-auto px-4 py-8">

      <h1 class="text-3xl font-bold text-white mb-2">Categories</h1>
      <p class="text-gray-400 mb-8">
        Manage and organize your content categories.
      </p>

      <div class="flex flex-wrap gap-4">

        <Link :href="route('contentAdmin.downloads.index')"
        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white rounded-lg transition" 
        >Downloads (Admin)
      </Link>

        <Link
          :href="route('contentAdmin.downloads.analytics')"
          class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white rounded-lg transition"
        >
          Download Analytics
        </Link>

         <Link
          href="/dashboard"
          class="px-6 py-3 bg-pink-700 hover:bg-pink-600 text-white rounded-lg transition"
        >
          <-- Back to Dashboard
        </Link>
      </div>

    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
import { Link } from '@inertiajs/vue3'
</script>
