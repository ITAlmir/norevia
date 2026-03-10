<script setup>
import TodoLayout from '@/Layouts/TodoLayout.vue'
import { useForm, router } from '@inertiajs/vue3'

defineProps({
  tasks: {
    type: Array,
    default: () => [],
  },
})

const form = useForm({
  title: '',
  platform: '',
  series: '',
  voice_tool: '',
  scheduled_for: '',
  publish_time: '',
  notes: '',
})

const submit = () => {
  form.post('/todo/tasks', {
    preserveScroll: true,
    onSuccess: () => form.reset(),
  })
}

const markDone = (task) => {
  router.patch(
    `/todo/tasks/${task.id}`,
    { status: task.status === 'done' ? 'pending' : 'done' },
    { preserveScroll: true }
  )
}

const removeTask = (task) => {
  if (!confirm(`Delete task "${task.title}"?`)) return

  router.delete(`/todo/tasks/${task.id}`, {
    preserveScroll: true,
  })
}

const badgeClass = (status) => {
  if (status === 'done') {
    return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'
  }

  if (status === 'late') {
    return 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300'
  }

  return 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'
}

const copy = (text) => {
  if (!text) return
  navigator.clipboard.writeText(text)
}

const copyPack = (task) => {
  let text = ''

  if (task.caption) text += task.caption + '\n\n'
  if (task.description) text += task.description + '\n\n'
  if (task.hashtags) text += task.hashtags

  if (!text.trim()) return

  navigator.clipboard.writeText(text)
}

const formatDate = (date) => {
  if (!date) return ''

  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: '2-digit',
    year: 'numeric',
  })
}

const formatTime = (time) => {
  if (!time) return ''

  const [hours, minutes] = String(time).split(':')
  if (hours === undefined || minutes === undefined) return time

  const h = Number(hours)
  const suffix = h >= 12 ? 'PM' : 'AM'
  const displayHour = h % 12 || 12

  return `${displayHour}:${minutes} ${suffix}`
}
</script>

<template>
  <TodoLayout>
    <div class="grid gap-6 xl:grid-cols-[1.05fr,0.95fr]">
      <!-- LEFT: TASK LIST -->
      <section class="rounded-[28px] border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
        <div class="flex items-center justify-between gap-4">
          <div>
            <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">
              Task List
            </h2>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
              Privatni taskovi trenutnog korisnika.
            </p>
          </div>

          <div class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
            {{ tasks.length }} total
          </div>
        </div>

        <div v-if="tasks.length" class="mt-6 space-y-3">
          <div
            v-for="task in tasks"
            :key="task.id"
            :class="task.status === 'done'
              ? 'rounded-2xl border border-emerald-300 bg-emerald-50/60 p-4 dark:border-emerald-900/40 dark:bg-emerald-950/20'
              : 'rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60'"
          >
            <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
              <div class="flex-1">
                <div class="text-base font-black text-slate-900 dark:text-white">
                  {{ task.title }}
                </div>

                <div class="mt-2 flex flex-wrap gap-2 text-xs">
                  <span v-if="task.platform" class="rounded-full bg-slate-200 px-2.5 py-1 font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                    {{ task.platform }}
                  </span>

                  <span v-if="task.series" class="rounded-full bg-violet-100 px-2.5 py-1 font-semibold text-violet-700 dark:bg-violet-900/30 dark:text-violet-300">
                    {{ task.series }}
                  </span>

                  <span v-if="task.voice_tool" class="rounded-full bg-cyan-100 px-2.5 py-1 font-semibold text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300">
                    {{ task.voice_tool }}
                  </span>

                  <span v-if="task.scheduled_for" class="rounded-full bg-slate-100 px-2.5 py-1 font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                    {{ formatDate(task.scheduled_for) }}
                  </span>

                  <span v-if="task.publish_time" class="rounded-full bg-slate-100 px-2.5 py-1 font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                    {{ formatTime(task.publish_time) }}
                  </span>
                </div>

                <div v-if="task.notes" class="mt-3 text-sm text-slate-600 dark:text-slate-400">
                  {{ task.notes }}
                </div>

                <div v-if="task.caption" class="mt-3 text-sm text-slate-700 dark:text-slate-300">
                  {{ task.caption }}
                </div>

                <div v-if="task.description" class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                  {{ task.description }}
                </div>

                <div v-if="task.hashtags" class="mt-2 text-xs text-cyan-600 dark:text-cyan-400">
                  {{ task.hashtags }}
                </div>

                <div class="mt-3 flex flex-wrap gap-2">
                  <button
                    v-if="task.caption"
                    type="button"
                    @click="copy(task.caption)"
                    class="rounded-lg border border-slate-300 px-3 py-1 text-xs font-semibold hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800"
                  >
                    Copy Caption
                  </button>

                  <button
                    v-if="task.description"
                    type="button"
                    @click="copy(task.description)"
                    class="rounded-lg border border-slate-300 px-3 py-1 text-xs font-semibold hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800"
                  >
                    Copy Description
                  </button>

                  <button
                    v-if="task.hashtags"
                    type="button"
                    @click="copy(task.hashtags)"
                    class="rounded-lg border border-slate-300 px-3 py-1 text-xs font-semibold hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800"
                  >
                    Copy Hashtags
                  </button>

                  <button
                    type="button"
                    @click="copyPack(task)"
                    class="rounded-xl bg-cyan-600 px-4 py-1 text-xs font-bold text-white hover:bg-cyan-700"
                  >
                    Copy Full Post
                  </button>
                </div>

                <div
                  v-if="task.status === 'done'"
                  class="mt-3 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700 dark:border-emerald-900/40 dark:bg-emerald-950/20 dark:text-emerald-300"
                >
                  Completed — linked plan item and topic updated.
                </div>
              </div>

              <div class="flex flex-wrap items-center gap-2 xl:max-w-[220px] xl:justify-end">
                <span
                  class="rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide"
                  :class="badgeClass(task.status)"
                >
                  {{ task.status }}
                </span>

                <button
                  type="button"
                  class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                  @click="markDone(task)"
                >
                  {{ task.status === 'done' ? 'Mark Pending' : 'Mark Done' }}
                </button>

                <button
                  type="button"
                  class="rounded-xl bg-rose-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-rose-700"
                  @click="removeTask(task)"
                >
                  Delete
                </button>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="mt-6 rounded-2xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
          Nema taskova još.
        </div>
      </section>

      <!-- RIGHT: CREATE TASK -->
      <section class="rounded-[28px] border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
        <div>
          <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">
            Create Task
          </h2>
          <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
            Dodaj novi zadatak bez komplikacije.
          </p>
        </div>

        <form class="mt-6 space-y-4" @submit.prevent="submit">
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">Title</label>
            <input
              v-model="form.title"
              type="text"
              class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
              placeholder="tiktok#cs2fps#21"
            />
            <div v-if="form.errors.title" class="mt-2 text-sm text-rose-500">{{ form.errors.title }}</div>
          </div>

          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">Platform</label>
              <input
                v-model="form.platform"
                type="text"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                placeholder="tiktok"
              />
            </div>

            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">Series</label>
              <input
                v-model="form.series"
                type="text"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                placeholder="cs2fps"
              />
            </div>
          </div>

          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">Voice Tool</label>
              <input
                v-model="form.voice_tool"
                type="text"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                placeholder="TTSMaker"
              />
            </div>

            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">Date</label>
              <input
                v-model="form.scheduled_for"
                type="date"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
              />
            </div>
          </div>

          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">Publish Time</label>
            <input
              v-model="form.publish_time"
              type="time"
              class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
            />
          </div>

          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">Notes</label>
            <textarea
              v-model="form.notes"
              rows="4"
              class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
              placeholder="Short production note..."
            />
          </div>

          <button
            type="submit"
            :disabled="form.processing"
            class="inline-flex items-center rounded-2xl bg-cyan-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-cyan-700 disabled:opacity-60"
          >
            {{ form.processing ? 'Saving...' : 'Create Task' }}
          </button>
        </form>
      </section>
    </div>
  </TodoLayout>
</template>