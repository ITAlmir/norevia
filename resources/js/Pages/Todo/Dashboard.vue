<script setup>
import TodoLayout from '@/Layouts/TodoLayout.vue'
import { router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { useUiFeedback } from '@/Composables/useUiFeedback'

const props = defineProps({
  monthProgress: { type: Number, default: 0 },
  todayPlan: { type: Array, default: () => [] },
  upcomingPlan: { type: Array, default: () => [] },
  lateTasks: { type: Array, default: () => [] },
  activeProfiles: { type: Array, default: () => [] },

  dailyCapacity: { type: Number, default: 0 },
  plannedRemaining: { type: Number, default: 0 },
  coverageDays: { type: Number, default: 0 },
  missingTopics: { type: Number, default: 0 },
  daysRemainingInMonth: { type: Number, default: 0 },

  bucketCoverage: { type: Array, default: () => [] },

  libraryOverview: {
    type: Object,
    default: () => ({
      available: 0,
      planned: 0,
      completed: 0,
      archived: 0,
    }),
  },
})

const { showToast, openConfirm } = useUiFeedback()

const badgeClass = (status) => {
  if (status === 'done') {
    return 'border border-emerald-500 text-emerald-600 dark:text-emerald-300'
  }
  if (status === 'used') {
    return 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300'
  }
  if (status === 'late') {
    return 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300'
  }
  if (status === 'planned') {
    return 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300'
  }
  if (status === 'skipped') {
    return 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'
  }
  return 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300'
}

const isTaskCleared = (item) => !!item?.cleared_at

const setPlanItemStatus = (item, nextStatus, successMessage) => {
  if (!item?.id) return
  if (item.cleared_at) return

  router.patch(`/todo/monthly-plan/items/${item.id}/toggle`, {
    status: nextStatus,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      showToast(successMessage, 'success')
    },
  })
}

const toggleTodayTask = (item) => {
  const currentStatus = item.task_status ?? item.status
  const nextStatus = currentStatus === 'done' ? 'planned' : 'done'

  setPlanItemStatus(
    item,
    nextStatus,
    nextStatus === 'done'
      ? 'Today item marked done.'
      : 'Today item returned to planned state.'
  )
}

const markTodayUsed = (item) => {
  if (!item?.id || item.cleared_at) return

  openConfirm({
    title: 'Mark Used',
    message: `Mark "${item.task_title}" as published and move it to archive?`,
    confirmLabel: 'Mark Used',
    danger: false,
    action: () => setPlanItemStatus(item, 'used', 'Today item marked used and archived.'),
  })
}

const toggleTodaySkipped = (item) => {
  const currentStatus = item.task_status ?? item.status
  const nextStatus = currentStatus === 'skipped' ? 'planned' : 'skipped'

  setPlanItemStatus(
    item,
    nextStatus,
    nextStatus === 'skipped'
      ? 'Today item marked skipped.'
      : 'Today item returned to planned state.'
  )
}

const toggleLateTask = (task) => {
  if (!task?.id || task.cleared_at) return

  const nextStatus = task.status === 'done' ? 'pending' : 'done'

  router.patch(`/todo/tasks/${task.id}`, {
    status: nextStatus,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      showToast(
        nextStatus === 'done'
          ? 'Late task marked done.'
          : 'Late task returned to pending state.',
        'success'
      )
    },
  })
}

const markLateUsed = (task) => {
  if (!task?.id || task.cleared_at) return

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
        showToast('Late task marked used and archived.', 'success')
      },
    }),
  })
}

const toggleLateSkipped = (task) => {
  if (!task?.id || task.cleared_at) return

  const nextStatus = task.plan_item_status === 'skipped' ? 'pending' : 'skipped'

  router.patch(`/todo/tasks/${task.id}`, {
    status: nextStatus,
  }, {
    preserveScroll: true,
    onSuccess: () => {
      showToast(
        nextStatus === 'skipped'
          ? 'Late task marked skipped.'
          : 'Late task returned to pending state.',
        'success'
      )
    },
  })
}

const toggleUpcomingTask = (item) => {
  const currentStatus = item.task_status ?? item.status
  const nextStatus = currentStatus === 'done' ? 'planned' : 'done'

  setPlanItemStatus(
    item,
    nextStatus,
    nextStatus === 'done'
      ? 'Upcoming item marked done.'
      : 'Upcoming item returned to planned state.'
  )
}

const markUpcomingUsed = (item) => {
  if (!item?.id || item.cleared_at) return

  openConfirm({
    title: 'Mark Used',
    message: `Mark "${item.task_title}" as published and move it to archive?`,
    confirmLabel: 'Mark Used',
    danger: false,
    action: () => setPlanItemStatus(item, 'used', 'Upcoming item marked used and archived.'),
  })
}

const toggleUpcomingSkipped = (item) => {
  const currentStatus = item.task_status ?? item.status
  const nextStatus = currentStatus === 'skipped' ? 'planned' : 'skipped'

  setPlanItemStatus(
    item,
    nextStatus,
    nextStatus === 'skipped'
      ? 'Upcoming item marked skipped.'
      : 'Upcoming item returned to planned state.'
  )
}

const toDateKey = (value) => {
  if (!value) return ''
  return String(value).slice(0, 10)
}

const groupedUpcomingPlan = computed(() => {
  const groups = {}

  for (const item of (props.upcomingPlan ?? [])) {
    const key = toDateKey(item.plan_date)
    if (!key) continue

    if (!groups[key]) {
      groups[key] = []
    }

    groups[key].push(item)
  }

  Object.keys(groups).forEach((key) => {
    groups[key].sort((a, b) => {
      const at = a.publish_time || ''
      const bt = b.publish_time || ''
      return at.localeCompare(bt)
    })
  })

  return groups
})

const availableUpcomingDates = computed(() => {
  return Object.keys(groupedUpcomingPlan.value).sort((a, b) => a.localeCompare(b))
})

const selectedUpcomingDate = ref('')

const ensureUpcomingDateSelected = () => {
  if (!availableUpcomingDates.value.length) {
    selectedUpcomingDate.value = ''
    return
  }

  if (!selectedUpcomingDate.value || !availableUpcomingDates.value.includes(selectedUpcomingDate.value)) {
    selectedUpcomingDate.value = availableUpcomingDates.value[0]
  }
}

const selectedUpcomingItems = computed(() => {
  ensureUpcomingDateSelected()

  if (!selectedUpcomingDate.value) return []

  return groupedUpcomingPlan.value[selectedUpcomingDate.value] ?? []
})

const goToPreviousUpcomingDay = () => {
  ensureUpcomingDateSelected()

  const index = availableUpcomingDates.value.indexOf(selectedUpcomingDate.value)
  if (index > 0) {
    selectedUpcomingDate.value = availableUpcomingDates.value[index - 1]
  }
}

const goToNextUpcomingDay = () => {
  ensureUpcomingDateSelected()

  const index = availableUpcomingDates.value.indexOf(selectedUpcomingDate.value)
  if (index !== -1 && index < availableUpcomingDates.value.length - 1) {
    selectedUpcomingDate.value = availableUpcomingDates.value[index + 1]
  }
}

const formatDashboardDate = (date) => {
  if (!date) return ''

  return new Date(date).toLocaleDateString('en-US', {
    weekday: 'short',
    month: 'short',
    day: '2-digit',
    year: 'numeric',
  })
}

const copyText = async (value, successMessage = 'Copied.') => {
  const text = String(value || '').trim()

  if (!text) {
    showToast('Nothing to copy.', 'error')
    return
  }

  try {
    await navigator.clipboard.writeText(text)
    showToast(successMessage, 'success')
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
      showToast(successMessage, 'success')
    } catch {
      showToast('Copy failed.', 'error')
    }

    document.body.removeChild(textarea)
  }
}

const copyFullPost = async (item) => {
  const parts = []

  if (item.caption) parts.push(item.caption)
  if (item.description) parts.push(item.description)
  if (item.hashtags) parts.push(item.hashtags)

  await copyText(parts.join('\n\n'), 'Full post copied.')
}
</script>

<template>
  <TodoLayout>
    <div class="grid gap-6 xl:grid-cols-[1.2fr,0.8fr]">
      <section
        class="overflow-hidden rounded-[28px] border border-slate-200 bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800 p-6 text-white shadow-[0_30px_80px_-35px_rgba(15,23,42,0.9)] dark:border-slate-700 md:p-8"
      >
        <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
          <div>
            <div class="text-xs font-semibold uppercase tracking-[0.2em] text-cyan-300/90">
              Command Status
            </div>
            <h2 class="mt-3 text-3xl font-black tracking-tight md:text-5xl">
              {{ monthProgress }}%
            </h2>
            <p class="mt-3 max-w-xl text-sm leading-6 text-slate-300 md:text-base">
              Calendar progress for the current month.
            </p>
          </div>

          <div class="w-full max-w-xs">
            <div class="mb-2 flex items-center justify-between text-sm text-slate-300">
              <span>Month progress</span>
              <span>{{ monthProgress }}%</span>
            </div>
            <div class="h-4 overflow-hidden rounded-full bg-white/10">
              <div
                class="h-full rounded-full bg-gradient-to-r from-cyan-400 via-violet-400 to-fuchsia-400 transition-all"
                :style="{ width: `${monthProgress}%` }"
              />
            </div>
          </div>
        </div>
      </section>

      <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-1">
        <div class="rounded-[24px] border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
          <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">
            Daily Capacity
          </div>
          <div class="mt-3 text-3xl font-black text-cyan-600 dark:text-cyan-400">
            {{ dailyCapacity }}
          </div>
        </div>

        <div class="rounded-[24px] border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
          <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">
            Planned Remaining
          </div>
          <div class="mt-3 text-3xl font-black text-slate-900 dark:text-white">
            {{ plannedRemaining }}
          </div>
        </div>

        <div class="rounded-[24px] border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
          <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">
            Coverage Days
          </div>
          <div class="mt-3 text-3xl font-black text-emerald-600 dark:text-emerald-400">
            {{ coverageDays }}
          </div>
        </div>

        <div class="rounded-[24px] border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
          <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">
            Missing Slots
          </div>
          <div class="mt-3 text-3xl font-black text-rose-600 dark:text-rose-400">
            {{ missingTopics }}
          </div>
        </div>
      </section>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
      <section class="xl:col-span-2 rounded-[28px] border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
        <div class="flex items-center justify-between gap-4">
          <div>
            <h3 class="text-xl font-black tracking-tight text-slate-900 dark:text-white">
              Today Plan
            </h3>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
              Planned content scheduled for today.
            </p>
          </div>

          <div class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
            {{ todayPlan.length }} items
          </div>
        </div>

        <div v-if="todayPlan.length" class="mt-6 space-y-3">
          <div
  v-for="item in todayPlan"
  :key="item.id"
  :class="[
    'rounded-2xl border p-4 transition-all duration-200',

    // DONE (zeleno - nagrađujuće)
    (item.task_status ?? item.status) === 'done'
      ? 'hover:scale-[1.01] hover:shadow-[0_6px_25px_rgba(16,185,129,0.25)] ring-1 ring-emerald-300/50 dark:ring-emerald-700/40 bg-gradient-to-br from-emerald-100 to-emerald-50 border-emerald-400 shadow-[0_4px_20px_rgba(16,185,129,0.15)] dark:from-emerald-900/40 dark:to-emerald-900/10 dark:border-emerald-700'

    // SKIPPED (amber - upozorenje)
    : (item.task_status ?? item.status) === 'skipped'
      ? 'bg-gradient-to-br from-amber-100 to-amber-50 border-amber-400 shadow-[0_4px_20px_rgba(245,158,11,0.15)] dark:from-amber-900/40 dark:to-amber-900/10 dark:border-amber-700'

    // DEFAULT
    : 'bg-gradient-to-br from-slate-100 to-slate-50 border-slate-300 shadow-sm dark:from-slate-900/60 dark:to-slate-900/30 dark:border-slate-700'
  ]"
>
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
              <div>
                <div class="flex items-center justify-between gap-2">
  <h3 class="text-sm font-medium text-slate-900 dark:text-white truncate">
    {{ item.task_title }}
  </h3>

  <span
    class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase"
    :class="badgeClass(item.task_status ?? item.status)"
  >
    {{ item.task_status ?? item.status }}
  </span>
</div><hr class="border-slate-400 dark:border-slate-600">

                <div class="mt-2 flex flex-wrap gap-2 text-xs">
                  <span
                    v-if="item.profile_name"
                    class="rounded-full bg-slate-900 px-2.5 py-1 font-semibold text-white dark:bg-white dark:text-slate-900"
                  >
                    {{ item.profile_name }}
                  </span>

                  <span
                    v-if="item.platform"
                    class="rounded-full bg-slate-200 px-2.5 py-1 font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300"
                  >
                    {{ item.platform }}
                  </span>

                  <span
                    v-if="item.series"
                    class="rounded-full bg-violet-100 px-2.5 py-1 font-semibold text-violet-700 dark:bg-violet-900/30 dark:text-violet-300"
                  >
                    {{ item.series }}
                  </span>

                  <span
                    v-if="item.voice_tool"
                    class="rounded-full bg-cyan-100 px-2.5 py-1 font-semibold text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300"
                  >
                    {{ item.voice_tool }}
                  </span>

                  <span
                    v-if="item.publish_time"
                    class="rounded-full bg-amber-100 px-2.5 py-1 font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-300"
                  >
                    {{ item.publish_time }}
                  </span>

                  <span
                    v-if="item.cleared_at"
                    class="rounded-full bg-slate-300 px-2.5 py-1 font-semibold text-slate-700 dark:bg-slate-700 dark:text-slate-200"
                  >
                    cleared
                  </span>
                </div>
              </div>

              <div class="flex flex-wrap items-center gap-2">
                
                <button
                  type="button"
                  class="rounded-xl px-3 py-2 text-xs font-bold transition disabled:cursor-not-allowed disabled:opacity-50"
                  :disabled="isTaskCleared(item)"
                  :class="(item.task_status ?? item.status) === 'done'
                    ? 'border border-slate-300 text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800'
                    : 'bg-emerald-600 text-white hover:bg-emerald-700'"
                  @click="toggleTodayTask(item)"
                >
                  {{ isTaskCleared(item)
                    ? 'Cleared'
                    : ((item.task_status ?? item.status) === 'done' ? 'Mark Planned' : 'Mark Done') }}
                </button>

                <button
                  type="button"
                  class="rounded-xl bg-cyan-600 px-3 py-2 text-xs font-bold text-white transition hover:bg-cyan-700 disabled:cursor-not-allowed disabled:opacity-50"
                  :disabled="isTaskCleared(item)"
                  @click="markTodayUsed(item)"
                >
                  Mark Used
                </button>

                <button
                  type="button"
                  class="rounded-xl border border-amber-300 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-amber-700 dark:text-amber-300 dark:hover:bg-amber-900/20"
                  :disabled="isTaskCleared(item)"
                  @click="toggleTodaySkipped(item)"
                >
                  {{ (item.task_status ?? item.status) === 'skipped' ? 'Unskip' : 'Skip' }}
                </button>

                <div class="mt-3 flex flex-wrap gap-2">
  <button
    v-if="item.caption"
    type="button"
    @click="copyText(item.caption, 'Caption copied.')"
    class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800"
  >
    Copy Caption
  </button>

  <button
    v-if="item.description"
    type="button"
    @click="copyText(item.description, 'Description copied.')"
    class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800"
  >
    Copy Description
  </button>

  <button
    v-if="item.hashtags"
    type="button"
    @click="copyText(item.hashtags, 'Hashtags copied.')"
    class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800"
  >
    Copy Hashtags
  </button>

  <button
    type="button"
    @click="copyFullPost(item)"
    class="rounded-lg bg-cyan-600 px-2 py-1 text-xs font-bold text-white hover:bg-cyan-700"
  >
    Copy Full Post
  </button>
</div>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="mt-6 rounded-2xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
          No plan generated for today.
        </div>
      </section>

      <section class="rounded-[28px] border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
        <h3 class="text-xl font-black tracking-tight text-slate-900 dark:text-white">
          Active Profiles
        </h3>
        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
          Active channels and their daily publishing targets.
        </p>

        <div v-if="activeProfiles.length" class="mt-6 space-y-3">
          <div
            v-for="profile in activeProfiles"
            :key="profile.id"
            class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60"
          >
            <div class="font-bold text-slate-900 dark:text-white">
              {{ profile.name }}
            </div>

            <div class="mt-2 flex flex-wrap gap-2 text-xs">
              <span
                v-if="profile.profile_name"
                class="rounded-full bg-slate-900 px-2.5 py-1 font-semibold text-white dark:bg-white dark:text-slate-900"
              >
                {{ profile.profile_name }}
              </span>

              <span class="rounded-full bg-slate-200 px-2.5 py-1 font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                {{ profile.platform }}
              </span>

              <span class="rounded-full bg-cyan-100 px-2.5 py-1 font-semibold text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300">
                {{
                  profile.schedule_type === 'weekly'
                    ? `${profile.daily_target} / week`
                    : profile.schedule_type === 'monthly'
                      ? `${profile.daily_target} / month`
                      : `${profile.daily_target} / day`
                }}
              </span>

              <span
                v-if="profile.default_voice_tool"
                class="rounded-full bg-violet-100 px-2.5 py-1 font-semibold text-violet-700 dark:bg-violet-900/30 dark:text-violet-300"
              >
                {{ profile.default_voice_tool }}
              </span>

              <span
                v-if="profile.publish_times?.length || profile.default_publish_time"
                class="rounded-full bg-amber-100 px-2.5 py-1 font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-300"
              >
                {{ profile.publish_times?.length ? profile.publish_times.join(' • ') : profile.default_publish_time }}
              </span>
            </div>
          </div>
        </div>

        <div v-else class="mt-6 rounded-2xl border border-dashed border-slate-300 p-6 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
          No active publishing profiles.
        </div>
      </section>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
      <section class="rounded-[28px] border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
        <div class="flex items-center justify-between gap-4">
          <div>
            <h3 class="text-xl font-black tracking-tight text-slate-900 dark:text-white">
              Coverage Insight
            </h3>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
              Remaining monthly capacity compared to scheduled content.
            </p>
          </div>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2">
          <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60">
            <div class="text-xs uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">Days Remaining</div>
            <div class="mt-2 text-3xl font-black text-slate-900 dark:text-white">{{ daysRemainingInMonth }}</div>
          </div>

          <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60">
            <div class="text-xs uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">Planned Remaining</div>
            <div class="mt-2 text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ plannedRemaining }}</div>
          </div>
        </div>

        <div
          class="mt-5 rounded-2xl border p-4 text-sm"
          :class="missingTopics > 0
            ? 'border-rose-200 bg-rose-50 text-rose-700 dark:border-rose-900/40 dark:bg-rose-950/20 dark:text-rose-300'
            : 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900/40 dark:bg-emerald-950/20 dark:text-emerald-300'"
        >
          <span v-if="missingTopics > 0">
            You still need <strong>{{ missingTopics }}</strong> more scheduled slots to fully cover the current month.
          </span>
          <span v-else>
            Your current schedule already covers the remaining monthly target.
          </span>
        </div>
      </section>

      <section class="rounded-[28px] border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
        <div class="flex items-center justify-between gap-4">
          <div>
            <h3 class="text-xl font-black tracking-tight text-slate-900 dark:text-white">
              Content Coverage
            </h3>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
              Coverage by shared content bucket.
            </p>
          </div>
        </div>

        <div v-if="bucketCoverage.length" class="mt-6 space-y-3">
          <div
            v-for="item in bucketCoverage"
            :key="item.content_bucket"
            class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60"
          >
            <div class="min-w-0">
              <div class="flex flex-col gap-3">
                <div class="min-w-0">
                  <div class="text-base font-black text-slate-900 dark:text-white">
                    {{ item.label }}
                  </div>

                  <div class="mt-2 flex flex-wrap items-start gap-2 text-xs">
                    <span
                      class="inline-flex max-w-full break-all rounded-lg bg-slate-200 px-2.5 py-1 font-semibold leading-5 text-slate-700 dark:bg-slate-800 dark:text-slate-300"
                    >
                      {{ item.content_bucket }}
                    </span>

                    <span
                      class="inline-flex rounded-lg bg-cyan-100 px-2.5 py-1 font-semibold leading-5 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300"
                    >
                      target {{ item.required }}
                    </span>

                    <span
                      class="inline-flex rounded-lg bg-emerald-100 px-2.5 py-1 font-semibold leading-5 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300"
                    >
                      planned {{ item.planned }}
                    </span>
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
                  <div class="rounded-xl border border-slate-200 bg-white px-3 py-3 dark:border-slate-700 dark:bg-slate-900">
                    <div class="text-[11px] uppercase tracking-[0.12em] text-slate-500 dark:text-slate-400">
                      Required
                    </div>
                    <div class="mt-1 text-2xl font-black text-slate-900 dark:text-white">
                      {{ item.required }}
                    </div>
                  </div>

                  <div class="rounded-xl border border-slate-200 bg-white px-3 py-3 dark:border-slate-700 dark:bg-slate-900">
                    <div class="text-[11px] uppercase tracking-[0.12em] text-slate-500 dark:text-slate-400">
                      Planned
                    </div>
                    <div class="mt-1 text-2xl font-black text-slate-900 dark:text-white">
                      {{ item.planned }}
                    </div>
                  </div>

                  <div class="rounded-xl border border-slate-200 bg-white px-3 py-3 dark:border-slate-700 dark:bg-slate-900">
                    <div class="text-[11px] uppercase tracking-[0.12em] text-slate-500 dark:text-slate-400">
                      Available
                    </div>
                    <div class="mt-1 text-2xl font-black text-slate-900 dark:text-white">
                      {{ item.available }}
                    </div>
                  </div>

                  <div class="rounded-xl border border-slate-200 bg-white px-3 py-3 dark:border-slate-700 dark:bg-slate-900">
                    <div class="text-[11px] uppercase tracking-[0.12em] text-slate-500 dark:text-slate-400">
                      Missing
                    </div>
                    <div
                      class="mt-1 text-2xl font-black"
                      :class="item.missing > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400'"
                    >
                      {{ item.missing }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="mt-6 rounded-2xl border border-dashed border-slate-300 p-6 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
          No content bucket coverage available.
        </div>
      </section>

      <section class="xl:col-span-2 rounded-[28px] border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
          <div>
            <h3 class="text-xl font-black tracking-tight text-slate-900 dark:text-white">
              Upcoming Schedule
            </h3>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
              View scheduled items by day.
            </p>
          </div>

          <div v-if="availableUpcomingDates.length" class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <button
              type="button"
              @click="goToPreviousUpcomingDay"
              class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
            >
              Previous Day
            </button>

            <input
              v-model="selectedUpcomingDate"
              type="date"
              :min="availableUpcomingDates[0] || undefined"
              :max="availableUpcomingDates[availableUpcomingDates.length - 1] || undefined"
              class="date-picker-contrast rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm text-slate-900 dark:border-slate-500 dark:bg-white dark:text-slate-900"
            />

            <button
              type="button"
              @click="goToNextUpcomingDay"
              class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
            >
              Next Day
            </button>
          </div>
        </div>

        <div v-if="availableUpcomingDates.length" class="mt-6">
          <div class="mb-4 flex items-center justify-between gap-3">
            <div class="text-sm font-bold uppercase tracking-[0.14em] text-slate-500 dark:text-slate-400">
              Selected Date
            </div>

            <div class="rounded-full bg-cyan-100 px-3 py-1 text-sm font-bold text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300">
              {{ formatDashboardDate(selectedUpcomingDate) }}
            </div>
          </div>

          <div v-if="selectedUpcomingItems.length" class="space-y-3">
            <div
              v-for="item in selectedUpcomingItems"
              :class="[
    'rounded-2xl border p-4 transition-all duration-200',

    // DONE (zeleno - nagrađujuće)
    (item.task_status ?? item.status) === 'done'
      ? 'hover:scale-[1.01] hover:shadow-[0_6px_25px_rgba(16,185,129,0.25)] ring-1 ring-emerald-300/50 dark:ring-emerald-700/40 bg-gradient-to-br from-emerald-100 to-emerald-50 border-emerald-400 shadow-[0_4px_20px_rgba(16,185,129,0.15)] dark:from-emerald-900/40 dark:to-emerald-900/10 dark:border-emerald-700'

    // SKIPPED (amber - upozorenje)
    : (item.task_status ?? item.status) === 'skipped'
      ? 'bg-gradient-to-br from-amber-100 to-amber-50 border-amber-400 shadow-[0_4px_20px_rgba(245,158,11,0.15)] dark:from-amber-900/40 dark:to-amber-900/10 dark:border-amber-700'

    // DEFAULT
    : 'bg-gradient-to-br from-slate-100 to-slate-50 border-slate-300 shadow-sm dark:from-slate-900/60 dark:to-slate-900/30 dark:border-slate-700'
  ]"
>
              <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div>
                  <div class="flex items-center justify-between gap-2">
  <h3 class="text-sm font-medium text-slate-900 dark:text-white truncate">
    {{ item.task_title }}
  </h3>

  <span
    class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase"
    :class="badgeClass(item.task_status ?? item.status)"
  >
    {{ item.task_status ?? item.status }}
  </span>
</div><hr class="border-slate-300 dark:border-slate-700">

                  <div class="mt-2 flex flex-wrap items-start gap-2 text-xs">
                    <span
                      v-if="item.profile_name"
                      class="rounded-full bg-slate-900 px-2.5 py-1 font-semibold text-white dark:bg-white dark:text-slate-900"
                    >
                      {{ item.profile_name }}
                    </span>

                    <span
                      v-if="item.platform"
                      class="rounded-full bg-slate-200 px-2.5 py-1 font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300"
                    >
                      {{ item.platform }}
                    </span>

                    <span
                      v-if="item.series"
                      class="rounded-full bg-violet-100 px-2.5 py-1 font-semibold text-violet-700 dark:bg-violet-900/30 dark:text-violet-300"
                    >
                      {{ item.series }}
                    </span>

                    <span
                      v-if="item.voice_tool"
                      class="rounded-full bg-cyan-100 px-2.5 py-1 font-semibold text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300"
                    >
                      {{ item.voice_tool }}
                    </span>

                    <span
                      v-if="item.publish_time"
                      class="rounded-full bg-amber-100 px-2.5 py-1 font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-300"
                    >
                      {{ item.publish_time }}
                    </span>

                    <span
                      v-if="item.cleared_at"
                      class="rounded-full bg-slate-300 px-2.5 py-1 font-semibold text-slate-700 dark:bg-slate-700 dark:text-slate-200"
                    >
                      cleared
                    </span>
                  </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">                  

                  <button
                    type="button"
                    class="rounded-xl px-3 py-2 text-xs font-bold transition disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="isTaskCleared(item)"
                    :class="(item.task_status ?? item.status) === 'done'
                      ? 'border border-slate-300 text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800'
                      : 'bg-emerald-600 text-white hover:bg-emerald-700'"
                    @click="toggleUpcomingTask(item)"
                  >
                    {{ isTaskCleared(item)
                      ? 'Cleared'
                      : ((item.task_status ?? item.status) === 'done' ? 'Mark Planned' : 'Mark Done') }}
                  </button>

                  <button
                    type="button"
                    class="rounded-xl bg-cyan-600 px-3 py-2 text-xs font-bold text-white transition hover:bg-cyan-700 disabled:cursor-not-allowed disabled:opacity-50"
                    :disabled="isTaskCleared(item)"
                    @click="markUpcomingUsed(item)"
                  >
                    Mark Used
                  </button>

                  <button
                    type="button"
                    class="rounded-xl border border-amber-300 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-amber-700 dark:text-amber-300 dark:hover:bg-amber-900/20"
                    :disabled="isTaskCleared(item)"
                    @click="toggleUpcomingSkipped(item)"
                  >
                    {{ (item.task_status ?? item.status) === 'skipped' ? 'Unskip' : 'Skip' }}
                  </button>

                  <div class="mt-3 flex flex-wrap gap-2">
  <button
    v-if="item.caption"
    type="button"
    @click="copyText(item.caption, 'Caption copied.')"
    class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800"
  >
    Copy Caption
  </button>

  <button
    v-if="item.description"
    type="button"
    @click="copyText(item.description, 'Description copied.')"
    class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800"
  >
    Copy Description
  </button>

  <button
    v-if="item.hashtags"
    type="button"
    @click="copyText(item.hashtags, 'Hashtags copied.')"
    class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-semibold hover:bg-slate-100 dark:border-slate-700 dark:hover:bg-slate-800"
  >
    Copy Hashtags
  </button>

  <button
    type="button"
    @click="copyFullPost(item)"
    class="rounded-lg bg-cyan-600 px-2 py-1 text-xs font-bold text-white hover:bg-cyan-700"
  >
    Copy Full Post
  </button>
</div>
                </div>
              </div>
            </div>
          </div>

          <div
            v-else
            class="rounded-2xl border border-dashed border-slate-300 p-6 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400"
          >
            No scheduled items for this date.
          </div>
        </div>

        <div
          v-else
          class="mt-6 rounded-2xl border border-dashed border-slate-300 p-6 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400"
        >
          No upcoming plan items.
        </div>
      </section>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
      <section class="rounded-[28px] border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
        <h3 class="text-xl font-black tracking-tight text-slate-900 dark:text-white">
          Late Tasks
        </h3>
        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
          Overdue tasks that still need attention.
        </p>

        <div v-if="lateTasks.length" class="mt-6 space-y-3">
          <div
            v-for="task in lateTasks"
            :key="task.id"
            :class="task.plan_item_status === 'skipped'
              ? 'rounded-2xl border border-amber-300 bg-amber-50/70 p-4 dark:border-amber-900/40 dark:bg-amber-950/20'
              : 'rounded-2xl border border-rose-200 bg-rose-50 p-4 dark:border-rose-900/40 dark:bg-rose-950/20'"
          >
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
              <div>
                <div class="text-base font-bold text-slate-900 dark:text-white">
                  {{ task.title }}
                </div>

                <span
                  v-if="task.profile_name"
                  class="rounded-full bg-slate-900 px-2.5 py-1 font-semibold text-white dark:bg-white dark:text-slate-900"
                >
                  {{ task.profile_name }}
                </span>

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
                    {{ formatDashboardDate(task.scheduled_for) }}
                  </span>

                  <span
                    v-if="task.publish_time"
                    class="rounded-full bg-amber-100 px-2.5 py-1 font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-300"
                  >
                    {{ task.publish_time }}
                  </span>

                  <span
                    v-if="task.cleared_at"
                    class="rounded-full bg-slate-300 px-2.5 py-1 font-semibold text-slate-700 dark:bg-slate-700 dark:text-slate-200"
                  >
                    cleared
                  </span>
                </div>
              </div>

              <div class="flex flex-wrap items-center gap-2">
                <span
                  class="rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide"
                  :class="badgeClass(task.plan_item_status === 'skipped' ? 'skipped' : task.status)"
                >
                  {{ task.plan_item_status === 'skipped' ? 'skipped' : task.status }}
                </span>

                <button
                  type="button"
                  class="rounded-xl px-3 py-2 text-xs font-bold transition disabled:cursor-not-allowed disabled:opacity-50"
                  :disabled="!!task.cleared_at"
                  :class="task.status === 'done'
                    ? 'border border-slate-300 text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800'
                    : 'bg-emerald-600 text-white hover:bg-emerald-700'"
                  @click="toggleLateTask(task)"
                >
                  {{ task.cleared_at
                    ? 'Cleared'
                    : (task.status === 'done' ? 'Mark Pending' : 'Mark Done') }}
                </button>

                <button
                  type="button"
                  class="rounded-xl bg-cyan-600 px-3 py-2 text-xs font-bold text-white transition hover:bg-cyan-700 disabled:cursor-not-allowed disabled:opacity-50"
                  :disabled="!!task.cleared_at"
                  @click="markLateUsed(task)"
                >
                  Mark Used
                </button>

                <button
                  type="button"
                  class="rounded-xl border border-amber-300 px-3 py-2 text-xs font-bold text-amber-700 transition hover:bg-amber-50 disabled:cursor-not-allowed disabled:opacity-50 dark:border-amber-700 dark:text-amber-300 dark:hover:bg-amber-900/20"
                  :disabled="!!task.cleared_at"
                  @click="toggleLateSkipped(task)"
                >
                  {{ task.plan_item_status === 'skipped' ? 'Unskip' : 'Skip' }}
                </button>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="mt-6 rounded-2xl border border-dashed border-slate-300 p-6 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
          No late tasks.
        </div>
      </section>

      <section class="rounded-[28px] border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
        <h3 class="text-xl font-black tracking-tight text-slate-900 dark:text-white">
          Library Overview
        </h3>
        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
          Current content library status across all buckets.
        </p>

        <div class="mt-6 grid gap-4 sm:grid-cols-4">
          <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60">
            <div class="text-xs uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">Available</div>
            <div class="mt-2 text-3xl font-black text-slate-900 dark:text-white">{{ libraryOverview.available }}</div>
          </div>

          <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60">
            <div class="text-xs uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">Planned</div>
            <div class="mt-2 text-3xl font-black text-cyan-600 dark:text-cyan-400">{{ libraryOverview.planned }}</div>
          </div>

          <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60">
            <div class="text-xs uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">Completed</div>
            <div class="mt-2 text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ libraryOverview.completed }}</div>
          </div>

          <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60">
            <div class="text-xs uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">Archived</div>
            <div class="mt-2 text-3xl font-black text-slate-900 dark:text-white">{{ libraryOverview.archived }}</div>
          </div>
        </div>
      </section>
    </div>
  </TodoLayout>
</template>

<style scoped>
.date-picker-contrast {
  color-scheme: light;
}

.date-picker-contrast::-webkit-calendar-picker-indicator {
  opacity: 1;
  cursor: pointer;
}

.dark .date-picker-contrast::-webkit-calendar-picker-indicator {
  filter: none;
}
</style>