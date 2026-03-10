<script setup>
import TodoLayout from '@/Layouts/TodoLayout.vue'
import { useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'

defineProps({
  profiles: {
    type: Array,
    default: () => [],
  },
  summary: {
    type: Object,
    default: () => ({
      total_profiles: 0,
      active_profiles: 0,
      daily_capacity: 0,
    }),
  },
})

const editingId = ref(null)

const form = useForm({
  name: '',
  platform: 'TikTok',
  daily_target: 1,
  default_voice_tool: '',
  default_publish_time: '',
  is_active: true,
})

const resetForm = () => {
  editingId.value = null
  form.reset()
  form.clearErrors()
  form.name = ''
  form.platform = 'TikTok'
  form.daily_target = 1
  form.default_voice_tool = ''
  form.default_publish_time = ''
  form.is_active = true
}

const startEdit = (profile) => {
  editingId.value = profile.id
  form.name = profile.name ?? ''
  form.platform = profile.platform
    ? profile.platform.charAt(0).toUpperCase() + profile.platform.slice(1)
    : 'TikTok'
  form.daily_target = profile.daily_target ?? 1
  form.default_voice_tool = profile.default_voice_tool ?? ''
  form.default_publish_time = profile.default_publish_time ?? ''
  form.is_active = !!profile.is_active
}

const submit = () => {
  if (editingId.value) {
    form.patch(`/todo/profiles/${editingId.value}`, {
      preserveScroll: true,
      onSuccess: () => resetForm(),
    })
    return
  }

  form.post('/todo/profiles', {
    preserveScroll: true,
    onSuccess: () => resetForm(),
  })
}

const cancelEdit = () => {
  resetForm()
}

const toggleActive = (profile) => {
  router.patch(`/todo/profiles/${profile.id}`, {
    is_active: !profile.is_active,
  }, {
    preserveScroll: true,
  })
}

const removeProfile = (profile) => {
  if (!confirm(`Delete profile "${profile.name}"?`)) return

  if (editingId.value === profile.id) {
    resetForm()
  }

  router.delete(`/todo/profiles/${profile.id}`, {
    preserveScroll: true,
  })
}
</script>

<template>
  <TodoLayout>
    <div class="grid gap-6 xl:grid-cols-[0.9fr,1.1fr]">
      <section class="rounded-[28px] border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
        <div>
          <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">
            {{ editingId ? 'Edit Publishing Profile' : 'Add Publishing Profile' }}
            </h2>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
            {{ editingId
                ? 'Ažuriraj postojeći kanal i njegova pravila objave.'
                : 'Definiši svoje kanale i koliko objava želiš dnevno.' }}
            </p>
        </div>

        <form class="mt-6 space-y-4" @submit.prevent="submit">
          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
              Channel / Page Name
            </label>
            <input
              v-model="form.name"
              type="text"
              class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
              placeholder="Norevia Discovery"
            />
            <div v-if="form.errors.name" class="mt-2 text-sm text-rose-500">{{ form.errors.name }}</div>
          </div>

          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                Platform
              </label>
              <select
                v-model="form.platform"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
              >
                <option>TikTok</option>
                <option>Facebook</option>
                <option>YouTube</option>
                <option>Instagram</option>
                <option>Website</option>
                <option>Other</option>
              </select>
              <div v-if="form.errors.platform" class="mt-2 text-sm text-rose-500">{{ form.errors.platform }}</div>
            </div>

            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                Daily Target
              </label>
              <select
                v-model="form.daily_target"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
              >
                <option v-for="n in 10" :key="n" :value="n">{{ n }}</option>
              </select>
              <div v-if="form.errors.daily_target" class="mt-2 text-sm text-rose-500">{{ form.errors.daily_target }}</div>
            </div>
          </div>

          <div>
  <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
    Default Voice Tool
  </label>
  <input
    v-model="form.default_voice_tool"
    type="text"
    class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
    placeholder="PlayHT / TTSMaker / ElevenLabs"
  />
</div>

                <div>
        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
            Default Publish Time
        </label>
        <input
            v-model="form.default_publish_time"
            type="time"
            class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
        />
        <div v-if="form.errors.default_publish_time" class="mt-2 text-sm text-rose-500">
            {{ form.errors.default_publish_time }}
        </div>
        </div>

          <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 dark:border-slate-800 dark:bg-slate-950/60 dark:text-slate-300">
            <input v-model="form.is_active" type="checkbox" class="h-4 w-4 rounded" />
            Active profile
          </label>

          <div class="flex flex-wrap items-center gap-3">
  <button
    type="submit"
    :disabled="form.processing"
    class="inline-flex items-center rounded-2xl bg-cyan-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-cyan-700 disabled:opacity-60"
  >
    {{ form.processing
      ? (editingId ? 'Updating...' : 'Saving...')
      : (editingId ? 'Update Profile' : 'Save Profile') }}
  </button>

  <button
    v-if="editingId"
    type="button"
    class="inline-flex items-center rounded-2xl border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
    @click="cancelEdit"
  >
    Cancel Edit
  </button>
</div>
        </form>
      </section>

      <section class="space-y-6">
        <div class="grid gap-4 sm:grid-cols-3">
          <div class="rounded-[24px] border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
            <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">Profiles</div>
            <div class="mt-3 text-3xl font-black text-slate-900 dark:text-white">{{ summary.total_profiles }}</div>
          </div>

          <div class="rounded-[24px] border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
            <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">Active</div>
            <div class="mt-3 text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ summary.active_profiles }}</div>
          </div>

          <div class="rounded-[24px] border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
            <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">Daily Capacity</div>
            <div class="mt-3 text-3xl font-black text-cyan-600 dark:text-cyan-400">{{ summary.daily_capacity }}</div>
          </div>
        </div>

        <div class="rounded-[28px] border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
          <div class="flex items-center justify-between gap-4">
            <div>
              <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                Publishing Profiles
              </h2>
              <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                Sistem po kojem će generator kasnije raspoređivati sadržaj.
              </p>
            </div>

            <div class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
              {{ profiles.length }} total
            </div>
          </div>

          <div v-if="profiles.length" class="mt-6 space-y-3">
  <div
    v-for="profile in profiles"
    :key="profile.id"
    :class="editingId === profile.id
      ? 'rounded-2xl border border-cyan-400 bg-cyan-50/40 p-4 dark:border-cyan-700 dark:bg-cyan-950/10'
      : 'rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60'"
  >
    <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
      <div>
        <div class="text-base font-black text-slate-900 dark:text-white">
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

          <span
            class="rounded-full px-2.5 py-1 font-semibold"
            :class="profile.is_active
              ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'
              : 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300'"
          >
            {{ profile.is_active ? 'active' : 'inactive' }}
          </span>
        </div>
      </div>

      <div class="flex flex-wrap items-center gap-2">
        <button
          type="button"
          class="rounded-xl border border-cyan-300 px-3 py-2 text-sm font-semibold text-cyan-700 transition hover:bg-cyan-50 dark:border-cyan-800 dark:text-cyan-300 dark:hover:bg-cyan-950/30"
          @click="startEdit(profile)"
        >
          Edit
        </button>

        <button
          type="button"
          class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
          @click="toggleActive(profile)"
        >
          {{ profile.is_active ? 'Deactivate' : 'Activate' }}
        </button>

        <button
          type="button"
          class="rounded-xl bg-rose-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-rose-700"
          @click="removeProfile(profile)"
        >
          Delete
        </button>
      </div>
    </div>
  </div>
</div>

          <div v-else class="mt-6 rounded-2xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
            Nema publishing profila još.
          </div>
        </div>
      </section>
    </div>
  </TodoLayout>
</template>