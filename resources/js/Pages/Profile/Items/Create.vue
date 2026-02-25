<template>
  <MainLayout>
    <div class="max-w-4xl mx-auto px-4 py-10">
      <h1 class="text-3xl font-bold text-white mb-6">
        Create New
      </h1>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <!-- ADD DOWNLOAD -->
        <a
          href="/content-admin/downloads/create"
          class="bg-slate-800 border border-slate-700 rounded-xl p-6 
                 hover:bg-slate-700 transition"
        >
          <div class="text-lg font-semibold text-white">
            Add Download
          </div>
          <div class="text-gray-400 text-sm mt-2">
            Upload a new tool, app or gaming utility.
          </div>
        </a>

      </div>

      <div class="mt-8">
        <a
          href="/dashboard"
          class="text-blue-400 hover:text-blue-300"
        >
          ← Back to Dashboard
        </a>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
</script>
