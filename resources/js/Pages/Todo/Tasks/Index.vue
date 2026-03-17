<script setup>
import TodoLayout from '@/Layouts/TodoLayout.vue'
import { useForm, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { useUiFeedback } from '@/Composables/useUiFeedback'

const props = defineProps({
  tasks: {
    type: Array,
    default: () => [],
  },
})

const hiddenTaskIds = ref([])

const visibleTasks = computed(() =>
  props.tasks.filter(task => !task.cleared_at)
)

const incomingTasks = computed(() =>
  props.tasks.filter(task =>
    isTaskVisible(task) &&
    task.plan_item_status !== 'skipped' &&
    task.status !== 'done'
  )
)

const doneTasks = computed(() =>
  props.tasks.filter(task =>
    isTaskVisible(task) &&
    task.status === 'done'
  )
)

const skippedTasks = computed(() =>
  props.tasks.filter(task =>
    isTaskVisible(task) &&
    task.plan_item_status === 'skipped'
  )
)

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

const isTaskVisible = (task) => {
  return !task?.cleared_at && !hiddenTaskIds.value.includes(task.id)
}

const markDone = (task) => {
  if (isTaskCleared(task)) return

  router.patch(`/todo/tasks/${task.id}`, {
    status: task.status === 'done' ? 'pending' : 'done',
  }, {
    preserveScroll: true,
  })
}

const markUsed = (task) => {
  if (isTaskCleared(task)) return

  openConfirm({
    title: 'Mark Used',
    message: `Mark "${task.title}" as published and move it to archive?`,
    confirmLabel: 'Mark Used',
    danger: false,
    action: () => router.patch(`/todo/tasks/${task.id}`, {
      status: 'used',
    }, {
      preserveScroll: true,
      onSuccess: () => {
        if (!hiddenTaskIds.value.includes(task.id)) {
          hiddenTaskIds.value.push(task.id)
        }
        showToast('Task moved to archive.', 'success')
      },
    }),
  })
}

const markSkipped = (task) => {
  if (isTaskCleared(task)) return

  router.patch(`/todo/tasks/${task.id}`, {
    status: task.plan_item_status === 'skipped' ? 'pending' : 'skipped',
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

const badgeClass = (status, planItemStatus = null) => {
  if (planItemStatus === 'skipped') {
    return 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'
  }

  if (status === 'done') {
    return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'
  }

  if (status === 'late') {
    return 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300'
  }

  return 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300'
}

const copy = async (text) => {
  const value = String(text || '').trim()

  if (!value) {
    showToast('Nothing to copy.', 'error')
    return
  }

  try {
    await navigator.clipboard.writeText(value)
    showToast('Copied to clipboard.', 'success')
  } catch (error) {
    const textarea = document.createElement('textarea')
    textarea.value = value
    textarea.style.position = 'fixed'
    textarea.style.opacity = '0'
    document.body.appendChild(textarea)
    textarea.focus()
    textarea.select()

    try {
      document.execCommand('copy')
      showToast('Copied to clipboard.', 'success')
    } catch {
      showToast('Copy failed.', 'error')
    }

    document.body.removeChild(textarea)
  }
}

const copyPack = async (task) => {
  let text = ''

  if (task.caption) text += task.caption + '\n\n'
  if (task.description) text += task.description + '\n\n'
  if (task.hashtags) text += task.hashtags

  if (!text.trim()) {
    showToast('Nothing to copy.', 'error')
    return
  }

  try {
    await navigator.clipboard.writeText(text)
    showToast('Full post copied.', 'success')
  } catch (error) {
    const textarea = document.createElement('textarea')
    textarea.value = text
    textarea.style.position = 'fixed'
    textarea.style.opacity = '0'
    document.body.appendChild(textarea)
    textarea.focus()
    textarea.select()

    try {
      document.execCommand('copy')
      showToast('Full post copied.', 'success')
    } catch {
      showToast('Copy failed.', 'error')
    }

    document.body.removeChild(textarea)
  }
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
              Active, prepared, and skipped tasks.
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

        <div class="mt-6">
          <h3 class="mb-3 text-sm font-black uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">
            Incoming Tasks
          </h3>

          <div v-if="incomingTasks.length" class="space-y-1 text-sm">

  <div
    v-for="task in incomingTasks"
    :key="task.id"
    class="flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 dark:border-slate-800 dark:bg-slate-950/60"
  >

    <div class="truncate text-slate-900 dark:text-white">
      {{ task.title }}
    </div>

    <div class="flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400">

      <span v-if="task.platform">
        {{ task.platform }}
      </span>

      <span v-if="task.publish_time">
        {{ formatTime(task.publish_time) }}
      </span>

      <button
        class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-bold hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800"
        @click="markDone(task)"
      >
        {{ task.status === 'done' ? 'Undo' : 'Done' }}
      </button>

      <button
        class="rounded-lg border border-amber-300 px-2 py-1 text-xs font-bold text-amber-700 hover:bg-amber-100 dark:border-amber-700 dark:text-amber-300 dark:hover:bg-amber-900/20"
        @click="markSkipped(task)"
      >
        Skip
      </button>

      <button
        class="rounded-lg bg-rose-600 px-2 py-1 text-xs font-bold text-white hover:bg-rose-700"
        @click="removeTask(task)"
      >
        Delete
      </button>

    </div>

  </div>

</div>

          <div
            v-else
            class="rounded-2xl border border-dashed border-slate-300 p-5 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400"
          >
            No incoming tasks.
          </div>
        </div>

        <div class="mt-8">
  <h3 class="mb-3 text-sm font-black uppercase tracking-[0.14em] text-emerald-600 dark:text-emerald-400">
    Done / Ready To Publish
  </h3>

  <div v-if="doneTasks.length" class="space-y-1 text-sm">

    <div
      v-for="task in doneTasks"
      :key="task.id"
      class="flex items-center justify-between rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 dark:border-emerald-900/40 dark:bg-emerald-950/20"
    >

      <div class="truncate text-slate-900 dark:text-white">
        {{ task.title }}
      </div>

      <div class="flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400">

        <span v-if="task.platform">
          {{ task.platform }}
        </span>

        <span v-if="task.publish_time">
          {{ formatTime(task.publish_time) }}
        </span>

        <button
          class="rounded-lg bg-emerald-600 px-2 py-1 text-xs font-bold text-white hover:bg-emerald-700"
          @click="markUsed(task)"
        >
          Used
        </button>
        <span class="text-slate-400">|</span>
        <button
          @click="copyPack(task)"
          class="text-xs text-cyan-600 hover:underline dark:text-cyan-400"
        >
          copy desc
        </button>
      </div>

    </div>

  </div>

  <div
    v-else
    class="rounded-2xl border border-dashed border-slate-300 p-5 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400"
  >
    No done tasks yet.
  </div>
</div>

        <div class="mt-8">
  <h3 class="mb-3 text-sm font-black uppercase tracking-[0.14em] text-amber-600 dark:text-amber-400">
    Skipped Tasks
  </h3>

  <div v-if="skippedTasks.length" class="space-y-1 text-sm">

    <div
      v-for="task in skippedTasks"
      :key="task.id"
      class="flex items-center justify-between rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 dark:border-amber-900/40 dark:bg-amber-950/20"
    >

      <div class="truncate text-slate-900 dark:text-white">
        {{ task.title }}
      </div>

      <div class="flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400">

        <span v-if="task.platform">
          {{ task.platform }}
        </span>

        <span class="font-semibold text-amber-600">
          skipped
        </span>

        <button
          class="rounded-lg border border-amber-300 px-2 py-1 text-xs font-bold text-amber-700 hover:bg-amber-100 dark:border-amber-700 dark:text-amber-300 dark:hover:bg-amber-900/20"
          @click="markSkipped(task)"
        >
          Unskip
        </button>

      </div>

    </div>

  </div>

  <div
    v-else
    class="rounded-2xl border border-dashed border-slate-300 p-5 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400"
  >
    No skipped tasks.
  </div>
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