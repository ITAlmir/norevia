<script setup>
import TodoLayout from '@/Layouts/TodoLayout.vue'

const props = defineProps({
  stats: {
    type: Object,
    default: () => ({
      totalTasks: 0,
      doneTasks: 0,
      plannedTasks: 0,
      skippedTasks: 0,
    }),
  },
  platformStats: {
    type: Array,
    default: () => [],
  },
  seriesStats: {
    type: Array,
    default: () => [],
  },
  voiceToolStats: {
    type: Array,
    default: () => [],
  },
})
</script>

<template>
  <TodoLayout>
    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
      <section class="rounded-[24px] border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
        <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">
          Total Tasks
        </div>
        <div class="mt-3 text-3xl font-black text-slate-900 dark:text-white">
          {{ stats.totalTasks }}
        </div>
      </section>

      <section class="rounded-[24px] border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
        <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">
          Done
        </div>
        <div class="mt-3 text-3xl font-black text-emerald-600 dark:text-emerald-400">
          {{ stats.doneTasks }}
        </div>
      </section>

      <section class="rounded-[24px] border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
        <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">
          Planned
        </div>
        <div class="mt-3 text-3xl font-black text-cyan-600 dark:text-cyan-400">
          {{ stats.plannedTasks }}
        </div>
      </section>

      <section class="rounded-[24px] border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
        <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">
          Skipped
        </div>
        <div class="mt-3 text-3xl font-black text-amber-600 dark:text-amber-400">
          {{ stats.skippedTasks }}
        </div>
      </section>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
      <section class="rounded-[28px] border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
        <h3 class="text-xl font-black tracking-tight text-slate-900 dark:text-white">
          Platform Distribution
        </h3>

        <div v-if="platformStats.length" class="mt-6 space-y-3">
          <div
            v-for="item in platformStats"
            :key="item.label"
            class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-800 dark:bg-slate-950/60"
          >
            <span class="font-semibold text-slate-900 dark:text-white">{{ item.label }}</span>
            <span class="text-lg font-black text-slate-900 dark:text-white">{{ item.total }}</span>
          </div>
        </div>

        <div v-else class="mt-6 rounded-2xl border border-dashed border-slate-300 p-6 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
          No platform stats available.
        </div>
      </section>

      <section class="rounded-[28px] border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
        <h3 class="text-xl font-black tracking-tight text-slate-900 dark:text-white">
          Series Distribution
        </h3>

        <div v-if="seriesStats.length" class="mt-6 space-y-3">
          <div
            v-for="item in seriesStats"
            :key="item.label"
            class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-800 dark:bg-slate-950/60"
          >
            <span class="font-semibold text-slate-900 dark:text-white">{{ item.label }}</span>
            <span class="text-lg font-black text-slate-900 dark:text-white">{{ item.total }}</span>
          </div>
        </div>

        <div v-else class="mt-6 rounded-2xl border border-dashed border-slate-300 p-6 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
          No series stats available.
        </div>
      </section>

      <section class="rounded-[28px] border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
        <h3 class="text-xl font-black tracking-tight text-slate-900 dark:text-white">
          Voice Tool Usage
        </h3>

        <div v-if="voiceToolStats.length" class="mt-6 space-y-3">
          <div
            v-for="item in voiceToolStats"
            :key="item.label"
            class="flex items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-800 dark:bg-slate-950/60"
          >
            <span class="font-semibold text-slate-900 dark:text-white">{{ item.label }}</span>
            <span class="text-lg font-black text-slate-900 dark:text-white">{{ item.total }}</span>
          </div>
        </div>

        <div v-else class="mt-6 rounded-2xl border border-dashed border-slate-300 p-6 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
          No voice tool stats available.
        </div>
      </section>
    </div>
  </TodoLayout>
</template>