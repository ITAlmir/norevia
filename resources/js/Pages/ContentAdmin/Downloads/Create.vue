<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { useForm } from '@inertiajs/vue3'

const form = useForm({
  title: '',
  category: 'tools',
  description: '',
  version: '',
  os: '',
  is_published: true,
  file: null,
  thumbnail: null,
  is_featured: false,
  featured_order: '',

})

function submit() {
  form.post('/content-admin/downloads', { forceFormData: true })
}

const insertTemplate = () => {
  form.description = `Tool short introduction.

Built for gamers, creators and PC users.

Features

• Feature one
• Feature two
• Feature three

Who is it for?

• Target group one
• Target group two

System Requirements

• Windows 10 / 11
• 64-bit system`
}

</script>


<template>
  <MainLayout>
    <div class="max-w-4xl mx-auto px-4 py-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-black dark:text-white">Create Download</h1>
        <p class="text-gray-400 mt-2">Upload a new file to Downloads section</p>
      </div>

      <!-- Form Card -->
      <div class="bg-slate-800/50 rounded-xl border border-slate-700 p-6">
        <form @submit.prevent="submit" class="space-y-6">

          <!-- Title -->
          <div>
            <label class="block text-gray-300 text-sm font-medium mb-2">Title *</label>
            <input
              v-model="form.title"
              class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-gray-200
                     focus:outline-none focus:ring-2 focus:ring-blue-600"
              placeholder="Ping Monitor"
            />
            <div v-if="form.errors.title" class="text-red-400 text-sm mt-2">
              {{ form.errors.title }}
            </div>
          </div>

          <!-- Category -->
          <div>
            <label class="block text-gray-300 text-sm font-medium mb-2">Category</label>
            <select
              v-model="form.category"
              class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-gray-200
                     focus:outline-none focus:ring-2 focus:ring-blue-600"
            >
              <option value="gaming">gaming</option>
              <option value="tools">tools</option>
              <option value="apps">apps</option>
            </select>
          </div>

          <!-- Description -->
          <div>
            <label class="block text-gray-300 text-sm font-medium mb-2">Description</label>
            <button
              type="button"
              @click="insertTemplate"
              class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
                    border border-emerald-300 dark:border-emerald-700/50
                    text-emerald-700 dark:text-emerald-300
                    text-sm font-semibold
                    hover:bg-emerald-50 dark:hover:bg-emerald-900/20
                    transition-all duration-200"
            >
              <span>＋</span>
              Insert Template
            </button>
            <textarea
              v-model="form.description"
              rows="4"
              class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-gray-200
                     focus:outline-none focus:ring-2 focus:ring-blue-600"
              placeholder="Short description of the tool..."
            />
          </div>

          <!-- Version + OS -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-gray-300 text-sm font-medium mb-2">Version</label>
              <input
                v-model="form.version"
                class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-gray-200
                       focus:outline-none focus:ring-2 focus:ring-blue-600"
                placeholder="1.0.0"
              />
            </div>
            <div>
              <label class="block text-gray-300 text-sm font-medium mb-2">OS</label>
              <input
                v-model="form.os"
                class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-gray-200
                       focus:outline-none focus:ring-2 focus:ring-blue-600"
                placeholder="windows/mac/linux"
              />
            </div>
          </div>

          <!-- Published -->
          <div class="flex items-center gap-3">
            <input
              id="pub"
              type="checkbox"
              v-model="form.is_published"
              class="h-4 w-4 rounded border-slate-600 bg-slate-900 text-blue-600"
            />
            <label for="pub" class="text-gray-300">Published</label>
          </div>

          <!-- Featured (ako si dodao) -->
          <div v-if="form.is_featured !== undefined" class="rounded-xl border border-slate-700 bg-slate-900/40 p-4">
            <label class="inline-flex items-center gap-3 text-gray-300">
              <input
                type="checkbox"
                v-model="form.is_featured"
                class="h-4 w-4 rounded border-slate-600 bg-slate-900 text-blue-600"
              />
              Featured
            </label>

            <div class="mt-3">
              <label class="block text-gray-300 text-sm font-medium mb-2">Featured order (optional)</label>
              <input
                v-model="form.featured_order"
                type="number"
                class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-gray-200
                       focus:outline-none focus:ring-2 focus:ring-blue-600"
                placeholder="1 = top, 2 = next..."
              />
            </div>
          </div>

          <!-- File -->
          <div>
            <label class="block text-gray-300 text-sm font-medium mb-2">File *</label>

            <div class="border-2 border-dashed border-slate-700 rounded-lg p-6 bg-slate-900/30">
              <input
                type="file"
                class="block w-full text-sm text-gray-300
                       file:mr-4 file:py-2 file:px-4
                       file:rounded-lg file:border-0
                       file:bg-slate-700 file:text-gray-100
                       hover:file:bg-slate-600"
                @change="e => form.file = e.target.files[0]"
              />
              <div v-if="form.errors.file" class="text-red-400 text-sm mt-2">
                {{ form.errors.file }}
              </div>
            </div>
          </div>

          <!-- Thumbnail -->
          <div>
            <label class="block text-gray-300 text-sm font-medium mb-2">Thumbnail (optional)</label>
            <input
              type="file"
              accept="image/*"
              class="block w-full text-sm text-gray-300
                     file:mr-4 file:py-2 file:px-4
                     file:rounded-lg file:border-0
                     file:bg-slate-700 file:text-gray-100
                     hover:file:bg-slate-600"
              @change="e => form.thumbnail = e.target.files[0]"
            />
            <div v-if="form.errors.thumbnail" class="text-red-400 text-sm mt-2">
              {{ form.errors.thumbnail }}
            </div>
          </div>

          <!-- Submit -->
          <div class="flex items-center justify-end gap-3 pt-2">
            <a
              href="/admin/items/create"
              class="px-6 py-3 border border-slate-600 text-gray-300 rounded-lg font-medium hover:bg-slate-700"
            >
              Cancel
            </a>

            <button
              type="submit"
              :disabled="form.processing"
              class="px-6 py-3 rounded-lg font-medium text-white
                     bg-gradient-to-r from-blue-700 to-slate-800
                     hover:from-blue-600 hover:to-slate-700
                     disabled:opacity-50"
            >
              <span v-if="form.processing">Uploading...</span>
              <span v-else>Create Download</span>
            </button>
          </div>

          <div v-if="form.progress" class="text-sm text-gray-300">
            Upload: {{ form.progress.percentage }}%
          </div>

        </form>
      </div>
    </div>
  </MainLayout>
</template>


