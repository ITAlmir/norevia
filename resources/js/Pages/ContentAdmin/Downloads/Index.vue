<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
import { router, Link  } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({ items: Object })

const showConfirm = ref(false)
const pendingId = ref(null)

function askDelete(id) {
  pendingId.value = id
  showConfirm.value = true
}

function doDelete() {
  if (!pendingId.value) return
  router.delete(route('contentAdmin.downloads.destroy', pendingId.value), {
    onFinish: () => {
      showConfirm.value = false
      pendingId.value = null
    }
  })
}



</script>

<template>
  <MainLayout>
    <div class="rounded-2xl border overflow-hidden
            bg-white border-slate-200
            dark:bg-slate-900/40 dark:border-slate-800
            shadow-[0_15px_45px_-25px_rgba(0,0,0,0.15)]">
      <div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">
      Downloads
    </h1>
    <p class="text-slate-600 dark:text-slate-400">
      Manage uploaded items
    </p>
  </div>

  <div class="flex gap-3">
    <Link
      :href="route('contentAdmin.downloads.create')"
      class="px-4 py-2 rounded-xl font-medium text-white
             bg-emerald-600 hover:bg-emerald-700
             shadow-sm"
    >
      + New
    </Link>

    <Link
      href="/admin/categories"
      class="px-4 py-2 rounded-xl font-medium
             bg-white border border-slate-300 text-slate-800
             hover:bg-slate-50
             dark:bg-slate-800 dark:border-slate-700 dark:text-white dark:hover:bg-slate-700"
    >
      ← Categories
    </Link>
  </div>
</div>

      <div class="bg-slate-800/50 border border-slate-700 rounded-xl overflow-hidden">
        <div class="grid grid-cols-12 gap-2 px-4 py-3 text-xs
            text-black-500 dark:text-slate-400
            border-b border-slate-200 dark:border-slate-800">
          <div class="col-span-6">Title</div>
          <div class="col-span-2">Category</div>
          <div class="col-span-2">Downloads</div>
          <div class="col-span-2 text-right">Actions</div>
        </div>

        <div
  v-for="it in items.data"
  :key="it.id"
  class="grid grid-cols-12 gap-2 px-4 py-4 
         border-b border-slate-100 dark:border-slate-800
         hover:bg-slate-400 dark:hover:bg-slate-800/40
         transition"
>
          <div class="col-span-6">
            <div class="font-medium">{{ it.title }}</div>
            <div class=" text-xs">{{ it.slug }}</div>
          </div>

          <div class="col-span-2 text-gray-300 capitalize">{{ it.category }}</div>
          <div class="col-span-2 text-gray-300">{{ it.download_count }}</div>

          <div class="col-span-2 text-right">
            <Link
  v-if="it.can_edit"
  :href="route('contentAdmin.downloads.edit', it.id)"
  class="text-blue-400 hover:text-blue-300 text-sm"
>
  Edit
</Link>

<span
  v-else
  class="text-gray-500 text-sm cursor-not-allowed"
  title="You can only edit your own downloads."
>
  Edit
</span>
            <button
  type="button"
  :disabled="!it.can_edit"
  @click="it.can_edit && askDelete(it.id)"
  class="px-3 py-1.5 rounded-lg text-white text-sm font-medium ml-2"
  :class="it.can_edit
    ? 'bg-red-500/90 hover:bg-red-700'
    : 'bg-slate-600/60 opacity-60 cursor-not-allowed'"
  title="You can only delete your own downloads."
>
  Delete
</button>
          </div>
        </div>

        <div v-if="!items.data.length" class="p-6 text-gray-400">
          No items yet.
        </div>
      </div>
      <!-- Confirm Modal -->
      <div v-if="showConfirm" class="fixed inset-0 z-50">
  <div class="absolute inset-0 bg-black/70" @click="showConfirm=false"></div>

  <div class="relative min-h-full flex items-center justify-center p-4">
    <div class="w-full max-w-md rounded-2xl border border-slate-700 bg-slate-900 shadow-xl">
      <div class="p-6">
        <div class="text-lg font-semibold text-white">Delete download?</div>
        <div class="text-sm text-gray-400 mt-2">
          This will permanently delete the database record and stored files.
        </div>
      </div>

      <div class="px-6 pb-6 flex justify-end gap-3">
        <button
          type="button"
          @click="showConfirm=false"
          class="px-4 py-2 rounded-lg border border-slate-200 text-gray-200 hover:bg-slate-800"
        >
          Cancel
        </button>

        <button
          type="button"
          @click="doDelete"
          class="px-4 py-2 rounded-lg bg-red-500 hover:bg-red-700 text-white"
        >
          Delete
        </button>
      </div>
    </div>
  </div>
</div>

    </div>
  </MainLayout>
</template>
