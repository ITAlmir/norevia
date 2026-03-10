<script setup>
import TodoLayout from '@/Layouts/TodoLayout.vue'

defineProps({
  totalTasks: Number,
  doneTasks: Number,
  pendingTasks: Number,
  platformStats: Array,
  seriesStats: Array,
  voiceStats: Array,
})
</script>

<template>
<TodoLayout>

<div class="max-w-6xl">

<h1 class="text-3xl font-black text-slate-900 dark:text-white">
Creator Analytics
</h1>

<p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
Statistika objavljenog sadržaja za ovaj mjesec.
</p>

<!-- overview cards -->

<div class="grid gap-4 mt-8 sm:grid-cols-3">

<div class="card">
<div class="label">Total Tasks</div>
<div class="value">{{ totalTasks }}</div>
</div>

<div class="card">
<div class="label">Completed</div>
<div class="value text-emerald-600">{{ doneTasks }}</div>
</div>

<div class="card">
<div class="label">Pending</div>
<div class="value text-amber-600">{{ pendingTasks }}</div>
</div>

</div>

<!-- platform stats -->

<div class="mt-10">

<h2 class="section-title">Platform Distribution</h2>

<div class="space-y-3 mt-4">

<div
v-for="item in platformStats"
:key="item.platform"
class="stat-row"
>

<div class="font-bold">
{{ item.platform }}
</div>

<div class="text-sm">
{{ item.count }}
</div>

</div>

</div>

</div>

<!-- series stats -->

<div class="mt-10">

<h2 class="section-title">Top Series</h2>

<div class="space-y-3 mt-4">

<div
v-for="item in seriesStats"
:key="item.series"
class="stat-row"
>

<div class="font-bold">
{{ item.series }}
</div>

<div class="text-sm">
{{ item.count }}
</div>

</div>

</div>

</div>

<!-- voice tools -->

<div class="mt-10">

<h2 class="section-title">Voice Tools</h2>

<div class="space-y-3 mt-4">

<div
v-for="item in voiceStats"
:key="item.voice_tool"
class="stat-row"
>

<div class="font-bold">
{{ item.voice_tool }}
</div>

<div class="text-sm">
{{ item.count }}
</div>

</div>

</div>

</div>

</div>

</TodoLayout>
</template>

<style scoped>

.card{
padding:20px;
border-radius:18px;
border:1px solid #e2e8f0;
background:white;
}

.dark .card{
border-color:#1e293b;
background:#0f172a;
}

.label{
font-size:12px;
text-transform:uppercase;
color:#64748b;
}

.value{
font-size:32px;
font-weight:900;
margin-top:6px;
}

.section-title{
font-weight:900;
font-size:18px;
}

.stat-row{
display:flex;
justify-content:space-between;
padding:12px;
border-radius:12px;
background:#f8fafc;
}

.dark .stat-row{
background:#020617;
}

</style>