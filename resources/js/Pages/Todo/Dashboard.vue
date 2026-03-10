<script setup>
import TodoLayout from '@/Layouts/TodoLayout.vue'
import { router } from '@inertiajs/vue3'

defineProps({
  monthProgress: { type: Number, default: 0 },
  todayTasks: { type: Array, default: () => [] },
  scheduleToday: { type: Array, default: () => [] },
  lateTasks: { type: Array, default: () => [] },
  libraryStats: {
    type: Object,
    default: () => ({ cs2: 0, fb: 0, world: 0, general: 0 }),
  },
  bufferStats: {
    type: Object,
    default: () => ({ tiktok_ready: 0, fb_ready: 0, world_ready: 0 }),
  },
  activeProfiles: { type: Array, default: () => [] },
    dailyCapacity: { type: Number, default: 0 },
    availableTopics: { type: Number, default: 0 },
    coverageDays: { type: Number, default: 0 },
    missingTopics: { type: Number, default: 0 },
    daysRemainingInMonth: { type: Number, default: 0 },
    todayPlan: { type: Array, default: () => [] },
    upcomingPlan: { type: Array, default: () => [] },
    platformCoverage: { type: Array, default: () => [] },
})

const badgeClass = (status) => {
  if (status === 'done') {
    return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'
  }
  if (status === 'late') {
    return 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300'
  }
  if (status === 'planned') {
    return 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300'
  }
  return 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'
}

const toggleTodayTask = (item) => {
  if (!item.task_id) return

  router.patch(`/todo/tasks/${item.task_id}`, {
    status: item.task_status === 'done' ? 'pending' : 'done',
  }, {
    preserveScroll: true,
  })
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
              Pregled kapaciteta, plana i sadržaja za ostatak mjeseca.
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
            Available Topics
          </div>
          <div class="mt-3 text-3xl font-black text-slate-900 dark:text-white">
            {{ availableTopics }}
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
            Missing Topics
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
        Šta bi danas trebalo izaći po generatoru plana.
      </p>
    </div>

    <div class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
      {{ todayPlan.length }} planned
    </div>
  </div>

  <div v-if="todayPlan.length" class="mt-6 space-y-3">
    <div
      v-for="item in todayPlan"
      :key="item.id"
      :class="item.task_status === 'done'
        ? 'rounded-2xl border border-emerald-300 bg-emerald-50/60 p-4 dark:border-emerald-900/40 dark:bg-emerald-950/20'
        : 'rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60'"
    >
      <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <div class="text-base font-bold text-slate-900 dark:text-white">
            {{ item.task_title }}
          </div>

          <div class="mt-2 flex flex-wrap gap-2 text-xs">
            <span v-if="item.platform" class="rounded-full bg-slate-200 px-2.5 py-1 font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
              {{ item.platform }}
            </span>

            <span v-if="item.series" class="rounded-full bg-violet-100 px-2.5 py-1 font-semibold text-violet-700 dark:bg-violet-900/30 dark:text-violet-300">
              {{ item.series }}
            </span>

            <span v-if="item.voice_tool" class="rounded-full bg-cyan-100 px-2.5 py-1 font-semibold text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300">
              {{ item.voice_tool }}
            </span>

            <span v-if="item.publish_time" class="rounded-full bg-amber-100 px-2.5 py-1 font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
              {{ item.publish_time }}
            </span>
          </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
          <span
            class="rounded-full px-2.5 py-1 text-xs font-bold uppercase tracking-wide"
            :class="badgeClass(item.status)"
          >
            {{ item.status }}
          </span>

          <button
            v-if="item.task_id"
            type="button"
            class="rounded-xl px-3 py-2 text-xs font-bold transition"
            :class="item.task_status === 'done'
              ? 'border border-slate-300 text-slate-700 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800'
              : 'bg-emerald-600 text-white hover:bg-emerald-700'"
            @click="toggleTodayTask(item)"
          >
            {{ item.task_status === 'done' ? 'Mark Pending' : 'Mark Done' }}
          </button>
        </div>
      </div>
    </div>
  </div>

  <div v-else class="mt-6 rounded-2xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
    Nema generisanog plana za danas.
  </div>
</section>

      <section class="rounded-[28px] border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
  <h3 class="text-xl font-black tracking-tight text-slate-900 dark:text-white">
    Active Profiles
  </h3>
  <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
    Aktivni kanali i njihov dnevni target.
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
        <span class="rounded-full bg-slate-200 px-2.5 py-1 font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
          {{ profile.platform }}
        </span>

        <span class="rounded-full bg-cyan-100 px-2.5 py-1 font-semibold text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300">
          {{ profile.daily_target }} / day
        </span>

        <span
          v-if="profile.default_voice_tool"
          class="rounded-full bg-violet-100 px-2.5 py-1 font-semibold text-violet-700 dark:bg-violet-900/30 dark:text-violet-300"
        >
          {{ profile.default_voice_tool }}
        </span>

        <span
          v-if="profile.default_publish_time"
          class="rounded-full bg-amber-100 px-2.5 py-1 font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-300"
        >
          {{ profile.default_publish_time }}
        </span>
      </div>
    </div>
  </div>

  <div v-else class="mt-6 rounded-2xl border border-dashed border-slate-300 p-6 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
    Nema aktivnih publishing profila.
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
              Da li imaš dovoljno tema za ostatak mjeseca.
            </p>
          </div>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2">
          <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60">
            <div class="text-xs uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">Days Remaining</div>
            <div class="mt-2 text-3xl font-black text-slate-900 dark:text-white">{{ daysRemainingInMonth }}</div>
          </div>

          <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60">
            <div class="text-xs uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">Missing Topics</div>
            <div class="mt-2 text-3xl font-black text-rose-600 dark:text-rose-400">{{ missingTopics }}</div>
          </div>
        </div>

        <div
          class="mt-5 rounded-2xl border p-4 text-sm"
          :class="missingTopics > 0
            ? 'border-rose-200 bg-rose-50 text-rose-700 dark:border-rose-900/40 dark:bg-rose-950/20 dark:text-rose-300'
            : 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900/40 dark:bg-emerald-950/20 dark:text-emerald-300'"
        >
          <span v-if="missingTopics > 0">
            Nedostaje još <strong>{{ missingTopics }}</strong> tema da ispuniš trenutni mjesečni target.
          </span>
          <span v-else>
            Trenutni library pokriva planirani mjesečni target.
          </span>
        </div>
      </section>

      <section class="rounded-[28px] border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
  <div class="flex items-center justify-between gap-4">
    <div>
      <h3 class="text-xl font-black tracking-tight text-slate-900 dark:text-white">
        Platform Coverage
      </h3>
      <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
        Pokrivenost sadržajem po aktivnim profilima.
      </p>
    </div>
  </div>

  <div v-if="platformCoverage.length" class="mt-6 space-y-3">
    <div
      v-for="item in platformCoverage"
      :key="item.profile_id"
      class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60"
    >
      <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
        <div>
          <div class="text-base font-black text-slate-900 dark:text-white">
            {{ item.name }}
          </div>

          <div class="mt-2 flex flex-wrap gap-2 text-xs">
            <span class="rounded-full bg-slate-200 px-2.5 py-1 font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
              {{ item.platform }}
            </span>

            <span class="rounded-full bg-cyan-100 px-2.5 py-1 font-semibold text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300">
              {{ item.daily_target }} / day
            </span>

            <span class="rounded-full bg-emerald-100 px-2.5 py-1 font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
              {{ item.coverage_days }} days coverage
            </span>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
          <div class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-center dark:border-slate-700 dark:bg-slate-900">
            <div class="text-[11px] uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">Available</div>
            <div class="mt-1 text-lg font-black text-slate-900 dark:text-white">{{ item.available_topics }}</div>
          </div>

          <div class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-center dark:border-slate-700 dark:bg-slate-900">
            <div class="text-[11px] uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">Coverage</div>
            <div class="mt-1 text-lg font-black text-slate-900 dark:text-white">{{ item.coverage_days }}</div>
          </div>

          <div class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-center dark:border-slate-700 dark:bg-slate-900">
            <div class="text-[11px] uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">Missing</div>
            <div class="mt-1 text-lg font-black" :class="item.missing_topics > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400'">
              {{ item.missing_topics }}
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div v-else class="mt-6 rounded-2xl border border-dashed border-slate-300 p-6 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
    Nema aktivnih profila za coverage prikaz.
  </div>
</section>

      <section class="rounded-[28px] border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
  <div class="flex items-center justify-between gap-4">
    <div>
      <h3 class="text-xl font-black tracking-tight text-slate-900 dark:text-white">
        Upcoming Plan
      </h3>
      <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
        Sljedeće stavke iz plana.
      </p>
    </div>
  </div>

  <div v-if="upcomingPlan.length" class="mt-6 space-y-3">
    <div
      v-for="item in upcomingPlan"
      :key="item.id"
      class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60"
    >
      <div class="font-bold text-slate-900 dark:text-white">
        {{ item.task_title }}
      </div>

      <div class="mt-2 flex flex-wrap gap-2 text-xs">
        <span class="rounded-full bg-slate-200 px-2.5 py-1 font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
          {{ item.plan_date }}
        </span>

        <span v-if="item.platform" class="rounded-full bg-cyan-100 px-2.5 py-1 font-semibold text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300">
          {{ item.platform }}
        </span>

        <span v-if="item.series" class="rounded-full bg-violet-100 px-2.5 py-1 font-semibold text-violet-700 dark:bg-violet-900/30 dark:text-violet-300">
          {{ item.series }}
        </span>

        <span v-if="item.publish_time" class="rounded-full bg-amber-100 px-2.5 py-1 font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
          {{ item.publish_time }}
        </span>
      </div>
    </div>
  </div>

  <div v-else class="mt-6 rounded-2xl border border-dashed border-slate-300 p-6 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
    Nema narednih stavki plana.
  </div>
</section>
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-2">
      <section class="rounded-[28px] border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
        <h3 class="text-xl font-black tracking-tight text-slate-900 dark:text-white">
          Late Tasks
        </h3>

        <div v-if="lateTasks.length" class="mt-6 space-y-3">
          <div
            v-for="task in lateTasks"
            :key="task.id"
            class="rounded-2xl border border-rose-200 bg-rose-50 p-4 dark:border-rose-900/40 dark:bg-rose-950/20"
          >
            <div class="font-bold text-slate-900 dark:text-white">
              {{ task.title }}
            </div>
            <div class="mt-1 text-sm text-slate-600 dark:text-slate-400">
              {{ task.scheduled_for }}
            </div>
          </div>
        </div>

        <div v-else class="mt-6 rounded-2xl border border-dashed border-slate-300 p-6 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
          Nema zakašnjelih taskova.
        </div>
      </section>

      <section class="rounded-[28px] border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
        <h3 class="text-xl font-black tracking-tight text-slate-900 dark:text-white">
          Library Status
        </h3>

        <div class="mt-6 grid gap-4 sm:grid-cols-4">
          <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60">
            <div class="text-xs uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">CS2</div>
            <div class="mt-2 text-3xl font-black text-slate-900 dark:text-white">{{ libraryStats.cs2 }}</div>
          </div>

          <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60">
            <div class="text-xs uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">FB</div>
            <div class="mt-2 text-3xl font-black text-slate-900 dark:text-white">{{ libraryStats.fb }}</div>
          </div>

          <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60">
            <div class="text-xs uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">WORLD</div>
            <div class="mt-2 text-3xl font-black text-slate-900 dark:text-white">{{ libraryStats.world }}</div>
          </div>

          <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60">
            <div class="text-xs uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">GENERAL</div>
            <div class="mt-2 text-3xl font-black text-slate-900 dark:text-white">{{ libraryStats.general }}</div>
          </div>
        </div>
      </section>
    </div>
  </TodoLayout>
</template>