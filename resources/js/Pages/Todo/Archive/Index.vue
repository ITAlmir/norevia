<script setup>
import TodoLayout from '@/Layouts/TodoLayout.vue'
import { useUiFeedback } from '@/Composables/useUiFeedback'
import { computed, ref } from 'vue'

const props = defineProps({
  tasks: {
    type: Array,
    default: () => [],
  },
  topics: {
    type: Array,
    default: () => [],
  },
})

const { showToast } = useUiFeedback()

const expandedYears = ref({})
const expandedMonths = ref({})
const expandedDays = ref({})

const showTopics = ref(false)

const toggleTopics = () => {
  showTopics.value = !showTopics.value
}

const copyTopicLines = async () => {
  const text = props.topics
    .map(t => `${t.platform || ''} | ${t.series || ''} | ${t.title || ''}`)
    .join('\n')

  if (!text.trim()) return

  await copyText(text, 'Topic lines copied.')
}

const copyTopicTitles = async () => {
  const text = props.topics
    .map(t => t.title || '')
    .filter(Boolean)
    .join('\n')

  if (!text.trim()) return

  await copyText(text, 'Topic titles copied.')
}

const toDateKey = (value) => {
  if (!value) return 'No Date'
  return String(value).slice(0, 10)
}

const getDateParts = (value) => {
  if (!value) {
    return {
      year: 'No Year',
      month: 'No Month',
      day: 'No Date',
      monthKey: 'No Year-No Month',
    }
  }

  const date = new Date(value)
  const year = String(date.getFullYear())
  const month = date.toLocaleDateString('en-US', { month: 'long' })
  const day = String(value).slice(0, 10)

  return {
    year,
    month,
    day,
    monthKey: `${year}-${month}`,
  }
}

const formatDayLabel = (date) => {
  if (!date || date === 'No Date') return 'No Date'

  return new Date(date).toLocaleDateString('en-US', {
    weekday: 'short',
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  })
}

const buildTitleLine = (task) => {
  const parts = []

  if (task.platform) parts.push(task.platform)
  if (task.series) parts.push(task.series)
  if (task.title) parts.push(task.title)

  return parts.join(' | ')
}

const buildTaskLine = (task) => {
  const parts = []

  if (task.publish_time) parts.push(task.publish_time)
  if (task.platform) parts.push(task.platform)
  if (task.series) parts.push(task.series)
  if (task.voice_tool) parts.push(task.voice_tool)
  if (task.title) parts.push(task.title)

  return parts.join(' | ')
}

const buildTaskBlock = (task) => {
  const lines = [buildTaskLine(task)]

  if (task.caption) lines.push(`Caption: ${task.caption}`)
  if (task.description) lines.push(`Description: ${task.description}`)
  if (task.hashtags) lines.push(`Hashtags: ${task.hashtags}`)
  if (task.notes) lines.push(`Notes: ${task.notes}`)

  return lines.join('\n')
}

const archiveTree = computed(() => {
  const yearsMap = {}

  for (const task of props.tasks) {
    const parts = getDateParts(task.scheduled_for)

    if (!yearsMap[parts.year]) {
      yearsMap[parts.year] = {
        year: parts.year,
        months: {},
      }
    }

    if (!yearsMap[parts.year].months[parts.monthKey]) {
      yearsMap[parts.year].months[parts.monthKey] = {
        key: parts.monthKey,
        month: parts.month,
        year: parts.year,
        days: {},
      }
    }

    if (!yearsMap[parts.year].months[parts.monthKey].days[parts.day]) {
      yearsMap[parts.year].months[parts.monthKey].days[parts.day] = {
        day: parts.day,
        items: [],
      }
    }

    yearsMap[parts.year].months[parts.monthKey].days[parts.day].items.push(task)
  }

  return Object.values(yearsMap)
    .sort((a, b) => b.year.localeCompare(a.year))
    .map((yearGroup) => ({
      ...yearGroup,
      months: Object.values(yearGroup.months)
        .sort((a, b) => b.key.localeCompare(a.key))
        .map((monthGroup) => ({
          ...monthGroup,
          days: Object.values(monthGroup.days)
            .sort((a, b) => b.day.localeCompare(a.day))
            .map((dayGroup) => ({
              ...dayGroup,
              items: [...dayGroup.items].sort((a, b) => {
                const at = a.publish_time || ''
                const bt = b.publish_time || ''
                return at.localeCompare(bt)
              }),
            })),
        })),
    }))
})

const allTasksFlat = computed(() => {
  return archiveTree.value.flatMap(year =>
    year.months.flatMap(month =>
      month.days.flatMap(day => day.items)
    )
  )
})

const buildDayBlock = (dayGroup) => {
  return [
    formatDayLabel(dayGroup.day),
    '',
    ...dayGroup.items.map((task) => buildTaskBlock(task)),
  ].join('\n\n')
}

const buildDayTitlesBlock = (dayGroup) => {
  return [
    formatDayLabel(dayGroup.day),
    '',
    ...dayGroup.items.map((task) => buildTitleLine(task)),
  ].join('\n')
}

const buildMonthBlock = (monthGroup) => {
  return monthGroup.days
    .map((dayGroup) => buildDayBlock(dayGroup))
    .join('\n\n====================\n\n')
}

const buildMonthTitlesBlock = (monthGroup) => {
  return monthGroup.days
    .map((dayGroup) => buildDayTitlesBlock(dayGroup))
    .join('\n\n====================\n\n')
}

const allArchiveText = computed(() => {
  return archiveTree.value
    .flatMap((yearGroup) => yearGroup.months.map((monthGroup) => buildMonthBlock(monthGroup)))
    .join('\n\n####################\n\n')
})

const allArchiveTitlesText = computed(() => {
  return archiveTree.value
    .flatMap((yearGroup) => yearGroup.months.map((monthGroup) => buildMonthTitlesBlock(monthGroup)))
    .join('\n\n####################\n\n')
})

const copyText = async (text, successMessage = 'Copied to clipboard.') => {
  try {
    await navigator.clipboard.writeText(text)
    showToast(successMessage, 'success')
  } catch (error) {
    console.error(error)
    showToast('Copy failed.', 'error')
  }
}

const copyDay = (dayGroup) => {
  copyText(buildDayBlock(dayGroup), 'Day archive copied.')
}

const copyDayTitles = (dayGroup) => {
  copyText(buildDayTitlesBlock(dayGroup), 'Day titles copied.')
}

const copyMonth = (monthGroup) => {
  copyText(buildMonthBlock(monthGroup), 'Month archive copied.')
}

const copyMonthTitles = (monthGroup) => {
  copyText(buildMonthTitlesBlock(monthGroup), 'Month titles copied.')
}

const copyAll = () => {
  copyText(allArchiveText.value, 'Full archive copied.')
}

const copyAllTitles = () => {
  copyText(allArchiveTitlesText.value, 'All archive titles copied.')
}

const toggleYear = (year) => {
  expandedYears.value[year] = !expandedYears.value[year]
}

const toggleMonth = (monthKey) => {
  expandedMonths.value[monthKey] = !expandedMonths.value[monthKey]
}

const toggleDay = (dayKey) => {
  expandedDays.value[dayKey] = !expandedDays.value[dayKey]
}

const topicStatusClass = (status) => {
  if (status === 'used') {
    return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'
  }

  return 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300'
}

const formatTopicDate = (date) => {
  if (!date) return ''

  return new Date(date).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  })
}

const isYearExpanded = (year) => expandedYears.value[year] ?? true
const isMonthExpanded = (monthKey) => expandedMonths.value[monthKey] ?? false
const isDayExpanded = (dayKey) => expandedDays.value[dayKey] ?? false
</script>

<template>
  <TodoLayout>
    <div class="space-y-6">
      <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
        <div>
          <h1 class="text-3xl font-black text-slate-900 dark:text-white">
            Archive
          </h1>
          <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
            Completed tasks organized by year, month, and date.
          </p>
        </div>

        <div class="flex flex-wrap gap-3">
          <button
            v-if="allTasksFlat.length"
            type="button"
            @click="copyAll"
            class="rounded-2xl bg-cyan-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-cyan-700"
          >
            Copy All
          </button>

          <button
            v-if="allTasksFlat.length"
            type="button"
            @click="copyAllTitles"
            class="rounded-2xl border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
          >
            Copy All Titles
          </button>
        </div>
      </div>

      <div v-if="archiveTree.length" class="space-y-4">
        <section
          v-for="yearGroup in archiveTree"
          :key="yearGroup.year"
          class="rounded-[28px] border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900"
        >
          <button
            type="button"
            @click="toggleYear(yearGroup.year)"
            class="flex w-full items-center justify-between rounded-2xl px-4 py-4 text-left transition hover:bg-slate-50 dark:hover:bg-slate-800/40"
          >
            <div>
              <h2 class="text-2xl font-black text-slate-900 dark:text-white">
                {{ yearGroup.year }}
              </h2>
              <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                {{ yearGroup.months.length }} month(s)
              </p>
            </div>

            <span class="text-sm font-bold text-slate-500 dark:text-slate-400">
              {{ isYearExpanded(yearGroup.year) ? 'Collapse' : 'Expand' }}
            </span>
          </button>

          <div v-if="isYearExpanded(yearGroup.year)" class="mt-3 space-y-4">
            <div
              v-for="monthGroup in yearGroup.months"
              :key="monthGroup.key"
              class="rounded-[24px] border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/50"
            >
              <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <button
                  type="button"
                  @click="toggleMonth(monthGroup.key)"
                  class="flex items-center justify-between gap-4 rounded-2xl px-3 py-3 text-left transition hover:bg-white dark:hover:bg-slate-900"
                >
                  <div>
                    <h3 class="text-xl font-black text-slate-900 dark:text-white">
                      {{ monthGroup.month }} {{ monthGroup.year }}
                    </h3>
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                      {{ monthGroup.days.length }} day group(s)
                    </p>
                  </div>

                  <span class="text-sm font-bold text-slate-500 dark:text-slate-400">
                    {{ isMonthExpanded(monthGroup.key) ? 'Collapse' : 'Expand' }}
                  </span>
                </button>

                <div class="flex flex-wrap gap-2">
                  <button
                    type="button"
                    @click="copyMonth(monthGroup)"
                    class="rounded-xl bg-cyan-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-cyan-700"
                  >
                    Copy Month
                  </button>

                  <button
                    type="button"
                    @click="copyMonthTitles(monthGroup)"
                    class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                  >
                    Copy Month Titles
                  </button>
                </div>
              </div>

              <div v-if="isMonthExpanded(monthGroup.key)" class="mt-4 space-y-3">
                <div
                  v-for="dayGroup in monthGroup.days"
                  :key="dayGroup.day"
                  class="rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900"
                >
                  <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <button
                      type="button"
                      @click="toggleDay(dayGroup.day)"
                      class="flex items-center justify-between gap-4 rounded-xl px-3 py-3 text-left transition hover:bg-slate-50 dark:hover:bg-slate-800/40"
                    >
                      <div>
                        <h4 class="text-lg font-black text-slate-900 dark:text-white">
                          {{ formatDayLabel(dayGroup.day) }}
                        </h4>
                        <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                          {{ dayGroup.items.length }} completed item(s)
                        </p>
                      </div>

                      <span class="text-sm font-bold text-slate-500 dark:text-slate-400">
                        {{ isDayExpanded(dayGroup.day) ? 'Collapse' : 'Expand' }}
                      </span>
                    </button>

                    <div class="flex flex-wrap gap-2">
                      <button
                        type="button"
                        @click="copyDay(dayGroup)"
                        class="rounded-xl bg-cyan-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-cyan-700"
                      >
                        Copy Day
                      </button>

                      <button
                        type="button"
                        @click="copyDayTitles(dayGroup)"
                        class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                      >
                        Copy Day Titles
                      </button>
                    </div>
                  </div>

                  <div v-if="isDayExpanded(dayGroup.day)" class="mt-4 space-y-3">
                    <div
                      v-for="task in dayGroup.items"
                      :key="task.id"
                      class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60"
                    >
                      <div class="font-bold text-slate-900 dark:text-white">
                        {{ task.title }}
                      </div>

                      <div class="mt-2 flex flex-wrap gap-2 text-xs">
                        <span
                          v-if="task.publish_time"
                          class="rounded-full bg-amber-100 px-2.5 py-1 font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-300"
                        >
                          {{ task.publish_time }}
                        </span>

                        <span
                          v-if="task.platform"
                          class="rounded-full bg-cyan-100 px-2.5 py-1 font-semibold text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300"
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
                          class="rounded-full bg-indigo-100 px-2.5 py-1 font-semibold text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300"
                        >
                          {{ task.voice_tool }}
                        </span>
                      </div>

                      <div class="mt-4 rounded-2xl border border-dashed border-slate-300 bg-white p-4 text-sm whitespace-pre-wrap text-slate-700 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300">
                        {{ buildTaskBlock(task) }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>

      <div
        v-else
        class="rounded-[28px] border border-dashed border-slate-300 bg-white p-10 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400"
      >
        No archived tasks yet.
      </div>
    </div>
    <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
  <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    <div>
      <h2 class="text-2xl font-black text-slate-900 dark:text-white">
        Used Topics
      </h2>
      <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
        Library history moved out of the active library list.
      </p>
    </div>

    <div class="flex flex-wrap gap-2">
      <button
        v-if="topics.length"
        type="button"
        @click="copyTopicLines"
        class="rounded-xl bg-cyan-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-cyan-700"
      >
        Copy Platform Lines
      </button>

      <button
        v-if="topics.length"
        type="button"
        @click="copyTopicTitles"
        class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
      >
        Copy Titles
      </button>

      <button
        type="button"
        @click="toggleTopics"
        class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
      >
        {{ showTopics ? 'Collapse' : `Expand (${topics.length})` }}
      </button>
    </div>
  </div>

  <div v-if="showTopics" class="mt-5">
    <div v-if="topics.length" class="space-y-3">
      <div
        v-for="topic in topics"
        :key="topic.id"
        class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60"
      >
        <div class="text-base font-black text-slate-900 dark:text-white">
          {{ topic.title }}
        </div>

        <div class="mt-2 flex flex-wrap gap-2 text-xs">
          <span
            v-if="topic.platform"
            class="rounded-full bg-slate-200 px-2.5 py-1 font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300"
          >
            {{ topic.platform }}
          </span>

          <span
            v-if="topic.series"
            class="rounded-full bg-violet-100 px-2.5 py-1 font-semibold text-violet-700 dark:bg-violet-900/30 dark:text-violet-300"
          >
            {{ topic.series }}
          </span>

          <span
            v-if="topic.voice_tool"
            class="rounded-full bg-cyan-100 px-2.5 py-1 font-semibold text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300"
          >
            {{ topic.voice_tool }}
          </span>

          <span
            v-if="topic.category"
            class="rounded-full bg-amber-100 px-2.5 py-1 font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-300"
          >
            {{ topic.category }}
          </span>

          <span
            class="rounded-full px-2.5 py-1 font-semibold"
            :class="topicStatusClass(topic.status)"
          >
            {{ topic.status }}
          </span>

          <span
            v-if="topic.updated_at"
            class="rounded-full bg-slate-100 px-2.5 py-1 font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300"
          >
            {{ formatTopicDate(topic.updated_at) }}
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

        <div v-if="topic.script_notes" class="mt-3 text-sm text-slate-500 dark:text-slate-400">
          {{ topic.script_notes }}
        </div>
      </div>
    </div>

    <div
      v-else
      class="rounded-[28px] border border-dashed border-slate-300 bg-white p-10 text-center text-sm text-slate-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400"
    >
      No used topics in archive yet.
    </div>
  </div>
</section>
    
  </TodoLayout>
</template>