<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({ item: Object })

const form = useForm({
    _method: 'put',
  title: props.item.title ?? '',
  category: props.item.category ?? 'tools',
  description: props.item.description ?? '',
  version: props.item.version ?? '',
  os: props.item.os ?? '',
  is_published: !!props.item.is_published,

  is_featured: !!props.item.is_featured,
  featured_order: props.item.featured_order ?? '',

  file: null,
  thumbnail: null,
})

function submit() {
  form.post(route('contentAdmin.downloads.update', props.item.id), {
    forceFormData: true,
    preserveScroll: true,
    onBefore: () => {
      form._method = 'put'
    },
    onError: (errors) => {
      console.log('validation errors', errors)
      console.log('payload title:', form.title)
      console.log('payload category:', form.category)
    },
  })
}


</script>

<template>
  <MainLayout>
    <div class="max-w-4xl mx-auto px-4 py-8">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-white">Edit Download</h1>
        <p class="text-gray-400 mt-2">Update item details and optionally replace files.</p>
      </div>

      <div class="bg-slate-800/50 rounded-xl border border-slate-700 p-6">
        <form @submit.prevent="submit" class="space-y-6">

          <div>
            <label class="block text-gray-300 text-sm font-medium mb-2">Title *</label>
            <input v-model="form.title"
              class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-gray-200
                     focus:outline-none focus:ring-2 focus:ring-blue-600" />
            <div v-if="form.errors.title" class="text-red-400 text-sm mt-2">{{ form.errors.title }}</div>
          </div>

          <div>
            <label class="block text-gray-300 text-sm font-medium mb-2">Category</label>
            <select v-model="form.category"
              class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-gray-200
                     focus:outline-none focus:ring-2 focus:ring-blue-600">
              <option value="gaming">gaming</option>
              <option value="tools">tools</option>
              <option value="apps">apps</option>
            </select>
          </div>

          <div>
            <label class="block text-gray-300 text-sm font-medium mb-2">Description</label>
            <textarea v-model="form.description" rows="4"
              class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-gray-200
                     focus:outline-none focus:ring-2 focus:ring-blue-600" />
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-gray-300 text-sm font-medium mb-2">Version</label>
              <input v-model="form.version"
                class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-gray-200
                       focus:outline-none focus:ring-2 focus:ring-blue-600" />
            </div>
            <div>
              <label class="block text-gray-300 text-sm font-medium mb-2">OS</label>
              <input v-model="form.os"
                class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-gray-200
                       focus:outline-none focus:ring-2 focus:ring-blue-600" />
            </div>
          </div>

          <div class="flex items-center gap-3">
            <input id="pub" type="checkbox" v-model="form.is_published"
              class="h-4 w-4 rounded border-slate-600 bg-slate-900 text-blue-600" />
            <label for="pub" class="text-gray-300">Published</label>
          </div>

          <div class="rounded-xl border border-slate-700 bg-slate-900/40 p-4">
            <label class="inline-flex items-center gap-3 text-gray-300">
              <input type="checkbox" v-model="form.is_featured"
                class="h-4 w-4 rounded border-slate-600 bg-slate-900 text-blue-600" />
              Featured
            </label>

            <div class="mt-3">
              <label class="block text-gray-300 text-sm font-medium mb-2">Featured order (optional)</label>
              <input v-model="form.featured_order" type="number"
                class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-gray-200
                       focus:outline-none focus:ring-2 focus:ring-blue-600" />
            </div>
          </div>

          <div class="border-t border-slate-700 pt-6 space-y-6">
            <div>
              <label class="block text-gray-300 text-sm font-medium mb-2">Replace file (optional)</label>
              <input type="file"
                class="block w-full text-sm text-gray-300
                       file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                       file:bg-slate-700 file:text-gray-100 hover:file:bg-slate-600"
                @change="e => form.file = e.target.files[0]" />
              <div v-if="form.errors.file" class="text-red-400 text-sm mt-2">{{ form.errors.file }}</div>
            </div>

            <div>
              <label class="block text-gray-300 text-sm font-medium mb-2">Replace thumbnail (optional)</label>
              <input type="file" accept="image/*"
                class="block w-full text-sm text-gray-300
                       file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                       file:bg-slate-700 file:text-gray-100 hover:file:bg-slate-600"
                @change="e => form.thumbnail = e.target.files[0]" />
              <div v-if="form.errors.thumbnail" class="text-red-400 text-sm mt-2">{{ form.errors.thumbnail }}</div>
            </div>
          </div>

          <div class="flex items-center justify-end gap-3 pt-2">
            <a :href="route('contentAdmin.downloads.index')"
              class="px-6 py-3 border border-slate-600 text-gray-300 rounded-lg font-medium hover:bg-slate-700">
              Back
            </a>

            <button type="submit" :disabled="form.processing"
              class="px-6 py-3 rounded-lg font-medium text-white
                     bg-gradient-to-r from-blue-700 to-slate-800
                     hover:from-blue-600 hover:to-slate-700 disabled:opacity-50">
              <span v-if="form.processing">Saving...</span>
              <span v-else>Save changes</span>
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
