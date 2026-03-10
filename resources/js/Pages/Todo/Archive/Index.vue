<script setup>
import TodoLayout from '@/Layouts/TodoLayout.vue'

defineProps({
  tasks: { type: Array, default: () => [] },
})
</script>

<template>
  <TodoLayout>

    <div class="max-w-5xl">

      <h1 class="text-3xl font-black text-slate-900 dark:text-white">
        Archive
      </h1>

      <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
        Završeni taskovi i objavljeni sadržaj.
      </p>

      <div v-if="tasks.length" class="mt-8 space-y-4">

        <div
          v-for="task in tasks"
          :key="task.id"
          class="rounded-2xl border border-emerald-200 bg-emerald-50/60 p-4 dark:border-emerald-900/40 dark:bg-emerald-950/20"
        >

          <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">

            <div>

              <div class="font-bold text-slate-900 dark:text-white">
                {{ task.title }}
              </div>

              <div class="mt-2 flex flex-wrap gap-2 text-xs">

                <span
                  v-if="task.platform"
                  class="rounded-full bg-slate-200 px-2 py-1 font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300"
                >
                  {{ task.platform }}
                </span>

                <span
                  v-if="task.series"
                  class="rounded-full bg-violet-100 px-2 py-1 font-semibold text-violet-700 dark:bg-violet-900/30 dark:text-violet-300"
                >
                  {{ task.series }}
                </span>

                <span
                  v-if="task.voice_tool"
                  class="rounded-full bg-cyan-100 px-2 py-1 font-semibold text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300"
                >
                  {{ task.voice_tool }}
                </span>

              </div>

            </div>

            <div class="flex flex-wrap gap-2 text-xs">

              <span
                class="rounded-full bg-slate-200 px-2 py-1 font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300"
              >
                {{ task.scheduled_for }}
              </span>

              <span
                v-if="task.publish_time"
                class="rounded-full bg-amber-100 px-2 py-1 font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-300"
              >
                {{ task.publish_time }}
              </span>

            </div>

          </div>

        </div>

      </div>

      <div
        v-else
        class="mt-10 rounded-2xl border border-dashed border-slate-300 p-8 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400"
      >
        Još nema završenih taskova.
      </div>

    </div>

  </TodoLayout>
</template>