<script setup>
import TodoLayout from '@/Layouts/TodoLayout.vue'
import { useForm } from '@inertiajs/vue3'
import { useUiFeedback } from '@/Composables/useUiFeedback'

const props = defineProps({
  plan: Object,
  items: Array,
  defaultEndDate: String,
})

const { openConfirm, showToast } = useUiFeedback()

const form = useForm({
  end_date: props.defaultEndDate || '',
})

const generatePlan = () => {
  if (!form.end_date) {
    showToast('Please select an end date first.', 'error')
    return
  }

  openConfirm({
    title: 'Generate Monthly Plan',
    message: 'Generate a new plan from available library topics until the selected end date?',
    confirmLabel: 'Generate Plan',
    danger: false,
    action: () => form.post('/todo/monthly-plan/generate', {
      preserveScroll: true,
      onSuccess: () => {
        showToast('Monthly plan generated.', 'success')
      },
    }),
  })
}

const formatDate = (date) => {
  if (!date) return ''

  return new Date(date).toLocaleDateString('en-US', {
    weekday: 'short',
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  })
}
</script>

<template>
  <TodoLayout>
    <div class="space-y-6">
      <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
          <h1 class="text-3xl font-black text-slate-900 dark:text-white">
            Monthly Plan
          </h1>
          <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
            Generate a schedule until a selected end date.
          </p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
              Generate Until
            </label>
            <input
              v-model="form.end_date"
              type="date"
              class="date-picker-dark rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white"
            />
          </div>

          <button
            type="button"
            @click="generatePlan"
            :disabled="form.processing"
            class="rounded-xl bg-cyan-600 px-5 py-3 font-semibold text-white transition hover:bg-cyan-700 disabled:opacity-60"
          >
            {{ form.processing ? 'Generating...' : 'Generate Plan' }}
          </button>
        </div>
      </div>

      <div v-if="items.length" class="space-y-3">
        <div
          v-for="item in items"
          :key="item.id"
          class="rounded-xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900"
        >
          <div class="flex justify-between gap-4">
            <div>
              <div class="font-bold text-slate-900 dark:text-white">
                {{ item.task_title }}
              </div>

              <div class="text-sm text-slate-500 dark:text-slate-400">
                {{ formatDate(item.plan_date) }}

                <span v-if="item.platform">
                  • {{ item.platform }}
                </span>

                <span v-if="item.series">
                  • {{ item.series }}
                </span>

                <span v-if="item.voice_tool">
                  • {{ item.voice_tool }}
                </span>

                <span v-if="item.publish_time">
                  • {{ item.publish_time }}
                </span>
              </div>
            </div>

            <span class="rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300">
              {{ item.status }}
            </span>
          </div>
        </div>
      </div>

      <div
        v-else
        class="rounded-2xl border border-dashed border-slate-300 p-10 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400"
      >
        No plan generated yet.
      </div>
    </div>
  </TodoLayout>
</template>

<style scoped>
.dark .date-picker-dark::-webkit-calendar-picker-indicator {
  filter: invert(1) brightness(2);
}
</style>