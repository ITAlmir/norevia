<script setup>
import TodoLayout from '@/Layouts/TodoLayout.vue'
import { useForm, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps({
  topics: {
    type: Array,
    default: () => [],
  },
})

const editingId = ref(null)

const bulkForm = useForm({
  bulk_input: '',
})

const editForm = useForm({
  title: '',
  platform: '',
  series: '',
  voice_tool: '',
  caption: '',
  description: '',
  hashtags: '',
  script_notes: '',
  status: 'available',
})

const submitBulk = () => {
  bulkForm.post('/todo/library', {
    preserveScroll: true,
    onSuccess: () => bulkForm.reset(),
  })
}

const startEdit = (topic) => {
  editingId.value = topic.id
  editForm.title = topic.title ?? ''
  editForm.platform = topic.platform ?? ''
  editForm.series = topic.series ?? ''
  editForm.voice_tool = topic.voice_tool ?? ''
  editForm.caption = topic.caption ?? ''
  editForm.description = topic.description ?? ''
  editForm.hashtags = topic.hashtags ?? ''
  editForm.script_notes = topic.script_notes ?? ''
  editForm.status = topic.status ?? 'available'
}

const submitEdit = () => {
  if (!editingId.value) return

  editForm.patch(`/todo/library/${editingId.value}`, {
    preserveScroll: true,
    onSuccess: () => cancelEdit(),
  })
}

const cancelEdit = () => {
  editingId.value = null
  editForm.reset()
  editForm.clearErrors()
  editForm.status = 'available'
}

const updateStatus = (topic, status) => {
  router.patch(`/todo/library/${topic.id}`, { status }, {
    preserveScroll: true,
  })
}

const removeTopic = (topic) => {
  if (!confirm(`Delete topic "${topic.title}"?`)) return

  if (editingId.value === topic.id) {
    cancelEdit()
  }

  router.delete(`/todo/library/${topic.id}`, {
    preserveScroll: true,
  })
}

const copy = (text) => {
  if (!text) return
  navigator.clipboard.writeText(text)
}

const copyPack = (topic) => {
  let text = ''

  if (topic.caption) text += topic.caption + '\n\n'
  if (topic.description) text += topic.description + '\n\n'
  if (topic.hashtags) text += topic.hashtags

  navigator.clipboard.writeText(text)
}

const statusClass = (status) => {
  if (status === 'used') {
    return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'
  }

  if (status === 'archived') {
    return 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300'
  }

  return 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300'
}

const stats = computed(() => {
  return {
    total: props.topics.length,
    available: props.topics.filter(t => t.status === 'available').length,
    used: props.topics.filter(t => t.status === 'used').length,
    archived: props.topics.filter(t => t.status === 'archived').length,
  }
})
</script>

<template>
  <TodoLayout>
    <div class="grid gap-6 xl:grid-cols-[0.95fr,1.05fr]">
      <section class="rounded-[28px] border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
        <div>
          <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">
            Bulk Import Topics
          </h2>
          <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
            Zalijepi više tema odjednom. Jedna linija = jedna tema.
          </p>
        </div>

        <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600 dark:border-slate-800 dark:bg-slate-950/60 dark:text-slate-300">
          <div class="font-semibold text-slate-900 dark:text-white">Format:</div>
          <div class="mt-2 font-mono text-xs leading-6">
            platform | series | voice_tool | title | caption | description | hashtags
          </div>

          <div class="mt-4 font-semibold text-slate-900 dark:text-white">Example:</div>
          <div class="mt-2 font-mono text-xs leading-6">
            tiktok|cs2fps|TTSMaker|Best NVIDIA setting|This NVIDIA setting can stabilize FPS.|Check this setting if you use NVIDIA GPU.|#cs2 #fps #gamingtips
          </div>
        </div>

        <form class="mt-6 space-y-4" @submit.prevent="submitBulk">
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
              Bulk Input
            </label>

            <textarea
              v-model="bulkForm.bulk_input"
              rows="14"
              class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-4 text-sm text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
              placeholder="tiktok|cs2fps|TTSMaker|CS2 launch options"
            />

            <div v-if="bulkForm.errors.bulk_input" class="mt-2 text-sm text-rose-500">
              {{ bulkForm.errors.bulk_input }}
            </div>
          </div>

          <button
            type="submit"
            :disabled="bulkForm.processing"
            class="inline-flex items-center rounded-2xl bg-cyan-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-cyan-700 disabled:opacity-60"
          >
            {{ bulkForm.processing ? 'Importing...' : 'Import Topics' }}
          </button>
        </form>
      </section>

      <section class="space-y-6">
        <div
          v-if="editingId"
          class="rounded-[24px] border border-cyan-200 bg-cyan-50/40 p-5 dark:border-cyan-900/40 dark:bg-cyan-950/10"
        >
          <h2 class="text-xl font-black text-slate-900 dark:text-white">
            Edit Topic
          </h2>
          <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
            Ažuriraj pojedinačnu temu bez brisanja.
          </p>

          <form class="mt-5 space-y-4" @submit.prevent="submitEdit">
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                Title
              </label>
              <input
                v-model="editForm.title"
                type="text"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
              />
              <div v-if="editForm.errors.title" class="mt-2 text-sm text-rose-500">
                {{ editForm.errors.title }}
              </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
              <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                  Platform
                </label>
                <input
                  v-model="editForm.platform"
                  type="text"
                  class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                />
              </div>

              <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                  Series
                </label>
                <input
                  v-model="editForm.series"
                  type="text"
                  class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                />
              </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
              <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                  Voice Tool
                </label>
                <input
                  v-model="editForm.voice_tool"
                  type="text"
                  class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                />
              </div>

              <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                  Status
                </label>
                <select
                  v-model="editForm.status"
                  class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                >
                  <option value="available">available</option>
                  <option value="used">used</option>
                  <option value="archived">archived</option>
                </select>
              </div>
            </div>

            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                Caption
              </label>
              <textarea
                v-model="editForm.caption"
                rows="3"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
              />
            </div>

            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                Description
              </label>
              <textarea
                v-model="editForm.description"
                rows="4"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
              />
            </div>

            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                Hashtags
              </label>
              <textarea
                v-model="editForm.hashtags"
                rows="2"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                placeholder="#cs2 #fps #gamingtips"
              />
            </div>

            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                Script Notes
              </label>
              <textarea
                v-model="editForm.script_notes"
                rows="3"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                placeholder="Hook, pacing, voice instructions..."
              />
            </div>

            <div class="flex gap-3">
              <button
                type="submit"
                :disabled="editForm.processing"
                class="rounded-xl bg-cyan-600 px-4 py-2 text-white font-bold hover:bg-cyan-700"
              >
                {{ editForm.processing ? 'Updating...' : 'Update Topic' }}
              </button>

              <button
                type="button"
                @click="cancelEdit"
                class="rounded-xl border px-4 py-2 font-bold"
              >
                Cancel
              </button>
            </div>
          </form>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 2xl:grid-cols-4">
          <div class="rounded-[24px] border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
            <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">
              Total
            </div>
            <div class="mt-3 text-3xl font-black text-slate-900 dark:text-white">
              {{ stats.total }}
            </div>
          </div>

          <div class="rounded-[24px] border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
            <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">
              Available
            </div>
            <div class="mt-3 text-3xl font-black text-cyan-600 dark:text-cyan-400">
              {{ stats.available }}
            </div>
          </div>

          <div class="rounded-[24px] border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
            <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">
              Used
            </div>
            <div class="mt-3 text-3xl font-black text-emerald-600 dark:text-emerald-400">
              {{ stats.used }}
            </div>
          </div>

          <div class="rounded-[24px] border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
            <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">
              Archived
            </div>
            <div class="mt-3 text-3xl font-black text-slate-900 dark:text-white">
              {{ stats.archived }}
            </div>
          </div>
        </div>

        <div class="rounded-[28px] border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
          <div class="flex items-center justify-between gap-4">
            <div>
              <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                Content Library
              </h2>
              <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                Ovdje su sve teme koje si ubacio.
              </p>
            </div>

            <div class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
              {{ topics.length }} topics
            </div>
          </div>

          <div v-if="topics.length" class="mt-6 space-y-3">
            <div
              v-for="topic in topics"
              :key="topic.id"
              :class="editingId === topic.id
                ? 'rounded-2xl border border-cyan-400 bg-cyan-50/40 p-4 dark:border-cyan-700 dark:bg-cyan-950/10'
                : 'rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60'"
            >
              <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                <div class="flex-1">
                  <div class="text-base font-black text-slate-900 dark:text-white">
                    {{ topic.title }}
                  </div>

                  <div class="mt-2 flex flex-wrap gap-2 text-xs">
                    <span v-if="topic.platform" class="rounded-full bg-slate-200 px-2.5 py-1 font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                      {{ topic.platform }}
                    </span>

                    <span v-if="topic.series" class="rounded-full bg-violet-100 px-2.5 py-1 font-semibold text-violet-700 dark:bg-violet-900/30 dark:text-violet-300">
                      {{ topic.series }}
                    </span>

                    <span v-if="topic.voice_tool" class="rounded-full bg-cyan-100 px-2.5 py-1 font-semibold text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300">
                      {{ topic.voice_tool }}
                    </span>

                    <span v-if="topic.category" class="rounded-full bg-amber-100 px-2.5 py-1 font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
                      {{ topic.category }}
                    </span>

                    <span v-if="topic.topic_code" class="rounded-full bg-slate-100 px-2.5 py-1 font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                      {{ topic.topic_code }}
                    </span>
                  </div>

                  <div v-if="topic.caption" class="mt-3 text-sm text-slate-700 dark:text-slate-300">
                    {{ topic.caption }}
                  </div>

                  <div v-if="topic.description" class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                    {{ topic.description }}
                  </div>

                  <div v-if="topic.hashtags" class="mt-2 text-xs text-cyan-600 dark:text-cyan-400">
                    {{ topic.hashtags }}
                  </div>

                  <div v-if="topic.script_notes" class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                    {{ topic.script_notes }}
                  </div>

                  <div class="mt-3 flex flex-wrap gap-2">
                    <button
                      v-if="topic.caption"
                      type="button"
                      @click="copy(topic.caption)"
                      class="rounded-lg border border-slate-300 px-3 py-1 text-xs font-semibold hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800"
                    >
                      Copy Caption
                    </button>

                    <button
                      v-if="topic.description"
                      type="button"
                      @click="copy(topic.description)"
                      class="rounded-lg border border-slate-300 px-3 py-1 text-xs font-semibold hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800"
                    >
                      Copy Description
                    </button>

                    <button
                      v-if="topic.hashtags"
                      type="button"
                      @click="copy(topic.hashtags)"
                      class="rounded-lg border border-slate-300 px-3 py-1 text-xs font-semibold hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800"
                    >
                      Copy Hashtags
                    </button>

                    <button
                      type="button"
                      @click="copyPack(topic)"
                      class="rounded-xl bg-cyan-600 px-4 py-1 text-xs font-bold text-white hover:bg-cyan-700"
                    >
                      Copy Full Post
                    </button>
                  </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 xl:max-w-[260px] xl:justify-end">
                  <span
                    class="rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide"
                    :class="statusClass(topic.status)"
                  >
                    {{ topic.status }}
                  </span>

                  <button
                    type="button"
                    class="rounded-xl border border-cyan-300 px-3 py-2 text-sm font-semibold text-cyan-700 transition hover:bg-cyan-50 dark:border-cyan-800 dark:text-cyan-300 dark:hover:bg-cyan-950/30"
                    @click="startEdit(topic)"
                  >
                    Edit
                  </button>

                  <button
                    type="button"
                    class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                    @click="updateStatus(topic, 'available')"
                  >
                    Available
                  </button>

                  <button
                    type="button"
                    class="rounded-xl border border-emerald-300 px-3 py-2 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50 dark:border-emerald-800 dark:text-emerald-300 dark:hover:bg-emerald-950/30"
                    @click="updateStatus(topic, 'used')"
                  >
                    Used
                  </button>

                  <button
                    type="button"
                    class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                    @click="updateStatus(topic, 'archived')"
                  >
                    Archive
                  </button>

                  <button
                    type="button"
                    class="rounded-xl bg-rose-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-rose-700"
                    @click="removeTopic(topic)"
                  >
                    Delete
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div v-else class="mt-6 rounded-2xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
            Nema unesenih tema još.
          </div>
        </div>
      </section>
    </div>
  </TodoLayout>
</template>