<script setup>
import TodoLayout from '@/Layouts/TodoLayout.vue'
import { router } from '@inertiajs/vue3'

defineProps({
  plan: Object,
  items: Array
})

const generatePlan = () => {

  if(!confirm("Generate plan from library topics?")) return

  router.post('/todo/monthly-plan/generate')
}

const formatDate = (date) => {
  if (!date) return ''

  return new Date(date).toLocaleDateString('de-DE', {
    day: '2-digit',
    month: 'short',
  })
}
</script>

<template>
<TodoLayout>

<div class="space-y-6">

<div class="flex items-center justify-between">

<h1 class="text-3xl font-black text-slate-900 dark:text-white">
Monthly Plan
</h1>

<button
@click="generatePlan"
class="px-5 py-3 bg-cyan-600 hover:bg-cyan-700 text-white rounded-xl font-semibold"
>
Generate Plan
</button>

</div>

<div v-if="items.length" class="space-y-3">

<div
v-for="item in items"
:key="item.id"
class="p-4 border rounded-xl border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900"
>

<div class="flex justify-between">

<div>

<div class="font-bold text-slate-900 dark:text-white">
{{ item.task_title }}
</div>

<div class="text-sm text-slate-500">

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

<span class="text-xs px-3 py-1 rounded-full bg-cyan-100 text-cyan-700">
{{ item.status }}
</span>

</div>

</div>

</div>

<div v-else class="text-center p-10 text-slate-500">
No plan generated yet
</div>

</div>

</TodoLayout>
</template>