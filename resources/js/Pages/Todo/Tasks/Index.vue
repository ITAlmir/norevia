<script setup>
import TodoLayout from '@/Layouts/TodoLayout.vue'
import { useForm, router } from '@inertiajs/vue3'
import { useUiFeedback } from '@/Composables/useUiFeedback'

const props = defineProps({
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

const { openConfirm, showToast } = useUiFeedback()

const submit = () => {
  form.post('/todo/tasks', {
    preserveScroll: true,
    onSuccess: () => form.reset(),
  })
}

const isTaskCleared = (task) => !!task?.cleared_at

const markDone = (task) => {
  if (isTaskCleared(task)) return

  router.patch(`/todo/tasks/${task.id}`, {
    status: task.status === 'done' ? 'pending' : 'done',
  }, {
    preserveScroll: true,
  })
}

const removeTask = (task) => {
  if (isTaskCleared(task)) return

  openConfirm({
    title: 'Delete Task',
    message: `Delete "${task.title}"? This action cannot be undone.`,
    confirmLabel: 'Delete Task',
    danger: true,
    action: () => router.delete(`/todo/tasks/${task.id}`, {
      preserveScroll: true,
    }),
  })
}

const clearActiveTasks = () => {
  openConfirm({
    title: 'Clear Active Tasks',
    message: 'Delete all pending and late tasks? Completed tasks will stay in archive.',
    confirmLabel: 'Clear Tasks',
    danger: true,
    action: () => router.post('/todo/tasks/clear-active', {}, {
      preserveScroll: true,
    }),
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

const copy = async (text) => {
  if (!text) return

  await navigator.clipboard.writeText(text)
  showToast('Copied to clipboard.', 'success')
}

const copyPack = async (task) => {
  let text = ''

  if (task.caption) text += task.caption + '\n\n'
  if (task.description) text += task.description + '\n\n'
  if (task.hashtags) text += task.hashtags

  if (!text.trim()) return

  await navigator.clipboard.writeText(text)
  showToast('Full post copied.', 'success')
}

const formatTaskDate = (date) => {
  if (!date) return ''

  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
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
      <section class="rounded-[28px] border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
          <div>
            <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">
              Task List
            </h2>

            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
              Active and completed tasks.
            </p>
          </div>

          <div class="flex items-center gap-3">
            <div class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
              {{ tasks.length }} total
            </div>

            <button
              type="button"
              @click="clearActiveTasks"
              class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-rose-700"
            >
              Clear Active Tasks
            </button>
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
                  <span
                    v-if="task.platform"
                    class="rounded-full bg-slate-200 px-2.5 py-1 font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300"
                  >
                    {{ task.platform }}
                  </span>

                  <span
                    v-if="task.series"
                    class="rounded-full bg-violet-100 px-2.5 py-1 font-semibold text-violet-700 dark:bg-violet-900/30 dark:text-violet-300"
                  >
                    {{ task.series }}
                  </span>

                  <span
                    v-if="task.voice_tool"
                    class="rounded-full bg-cyan-100 px-2.5 py-1 font-semibold text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300"
                  >
                    {{ task.voice_tool }}
                  </span>

                  <span
                    v-if="task.scheduled_for"
                    class="rounded-full bg-slate-100 px-2.5 py-1 font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300"
                  >
                    {{ formatTaskDate(task.scheduled_for) }}
                  </span>

                  <span
                    v-if="task.publish_time"
                    class="rounded-full bg-amber-100 px-2.5 py-1 font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-300"
                  >
                    {{ formatTime(task.publish_time) }}
                  </span>

                  <span
                    v-if="task.cleared_at"
                    class="rounded-full bg-slate-300 px-2 py-1 text-xs font-semibold text-slate-700"
                  >
                    cleared
                  </span>
                </div>

                <div v-if="task.notes" class="mt-3 text-sm text-slate-600 dark:text-slate-400">
                  {{ task.notes }}
                </div>

                <div class="mt-3 flex flex-wrap gap-2">
                  <button
                    v-if="task.caption"
                    @click="copy(task.caption)"
                    class="rounded-lg border border-slate-300 px-3 py-1 text-xs font-semibold hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800"
                  >
                    Copy Caption
                  </button>

                  <button
                    v-if="task.description"
                    @click="copy(task.description)"
                    class="rounded-lg border border-slate-300 px-3 py-1 text-xs font-semibold hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800"
                  >
                    Copy Description
                  </button>

                  <button
                    v-if="task.hashtags"
                    @click="copy(task.hashtags)"
                    class="rounded-lg border border-slate-300 px-3 py-1 text-xs font-semibold hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800"
                  >
                    Copy Hashtags
                  </button>

                  <button
                    @click="copyPack(task)"
                    class="rounded-xl bg-cyan-600 px-4 py-1 text-xs font-bold text-white hover:bg-cyan-700"
                  >
                    Copy Full Post
                  </button>
                </div>
              </div>

              <div class="flex flex-wrap items-center gap-2">
                <span
                  class="rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide"
                  :class="badgeClass(task.status)"
                >
                  {{ task.status }}
                </span>

                <button
                  class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                  :disabled="isTaskCleared(task)"
                  @click="markDone(task)"
                >
                  {{ task.status === 'done' ? 'Mark Pending' : 'Mark Done' }}
                </button>

                <button
                  class="rounded-xl bg-rose-600 px-3 py-2 text-sm font-semibold text-white hover:bg-rose-700 disabled:cursor-not-allowed disabled:opacity-50"
                  :disabled="isTaskCleared(task)"
                  @click="removeTask(task)"
                >
                  Delete
                </button>
              </div>
            </div>
          </div>
        </div>

        <div
          v-else
          class="mt-6 rounded-2xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400"
        >
          No tasks yet.
        </div>
      </section>

      <section class="rounded-[28px] border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
        <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">
          Create Task
        </h2>

        <form class="mt-6 space-y-4" @submit.prevent="submit">
          <input
            v-model="form.title"
            type="text"
            placeholder="Task title"
            class="w-full rounded-2xl border border-slate-300 px-4 py-3 dark:border-slate-700 dark:bg-slate-950"
          />

          <input
            v-model="form.platform"
            type="text"
            placeholder="Platform"
            class="w-full rounded-2xl border border-slate-300 px-4 py-3 dark:border-slate-700 dark:bg-slate-950"
          />

          <input
            v-model="form.series"
            type="text"
            placeholder="Series"
            class="w-full rounded-2xl border border-slate-300 px-4 py-3 dark:border-slate-700 dark:bg-slate-950"
          />

          <input
            v-model="form.voice_tool"
            type="text"
            placeholder="Voice tool"
            class="w-full rounded-2xl border border-slate-300 px-4 py-3 dark:border-slate-700 dark:bg-slate-950"
          />

          <input
            v-model="form.scheduled_for"
            type="date"
            class="w-full rounded-2xl border border-slate-300 px-4 py-3 dark:border-slate-700 dark:bg-slate-950"
          />

          <input
            v-model="form.publish_time"
            type="time"
            class="w-full rounded-2xl border border-slate-300 px-4 py-3 dark:border-slate-700 dark:bg-slate-950"
          />

          <textarea
            v-model="form.notes"
            rows="4"
            placeholder="Notes"
            class="w-full rounded-2xl border border-slate-300 px-4 py-3 dark:border-slate-700 dark:bg-slate-950"
          ></textarea>

          <button
            type="submit"
            class="rounded-2xl bg-cyan-600 px-5 py-3 text-sm font-bold text-white hover:bg-cyan-700"
          >
            Create Task
          </button>
        </form>
      </section>
    </div>
  </TodoLayout>
</template>