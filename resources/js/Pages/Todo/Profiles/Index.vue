<script setup>
import TodoLayout from '@/Layouts/TodoLayout.vue'
import { useForm, router } from '@inertiajs/vue3'
import { useUiFeedback } from '@/Composables/useUiFeedback'
import { ref, watch } from 'vue'

const props = defineProps({
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

const { openConfirm, showToast } = useUiFeedback()

const editingId = ref(null)

const form = useForm({
  name: '',
  platform: 'TikTok',
  daily_target: 1,
  default_voice_tool: '',
  default_publish_time: '',
  publish_times: [''],
  schedule_type: 'daily',
  schedule_days: [],
  description: '',
  use_shared_content: false,
  content_group_mode: 'group-1',
  custom_content_group: '',
  content_group: '',
  is_active: true,
})

const weeklyOptions = [
  { value: 1, label: 'Mon' },
  { value: 2, label: 'Tue' },
  { value: 3, label: 'Wed' },
  { value: 4, label: 'Thu' },
  { value: 5, label: 'Fri' },
  { value: 6, label: 'Sat' },
  { value: 7, label: 'Sun' },
]

const monthlyOptions = Array.from({ length: 31 }, (_, i) => ({
  value: i + 1,
  label: String(i + 1),
}))

const sharedGroupOptions = [
  { value: 'group-1', label: 'Group 1' },
  { value: 'group-2', label: 'Group 2' },
  { value: 'group-3', label: 'Group 3' },
  { value: 'group-4', label: 'Group 4' },
  { value: 'custom', label: 'Custom' },
]

watch(
  () => [form.use_shared_content, form.content_group_mode, form.custom_content_group],
  () => {
    if (!form.use_shared_content) {
      form.content_group = ''
      return
    }

    if (form.content_group_mode === 'custom') {
      form.content_group = (form.custom_content_group || '').trim()
      return
    }

    form.content_group = form.content_group_mode
  },
  { immediate: true }
)

watch(
  () => form.schedule_type,
  (value) => {
    if (value === 'daily') {
      form.schedule_days = []
    }
  }
)

const resetForm = () => {
  editingId.value = null
  form.reset()
  form.clearErrors()
  form.name = ''
  form.platform = 'TikTok'
  form.daily_target = 1
  form.default_voice_tool = ''
  form.default_publish_time = ''
  form.publish_times = ['']
  form.schedule_type = 'daily'
  form.schedule_days = []
  form.description = ''
  form.use_shared_content = false
  form.content_group_mode = 'group-1'
  form.custom_content_group = ''
  form.content_group = ''
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
  form.publish_times = profile.publish_times?.length
    ? [...profile.publish_times]
    : [profile.default_publish_time || '']
  form.schedule_type = profile.schedule_type ?? 'daily'
  form.schedule_days = profile.schedule_days ?? []
  form.description = profile.description ?? ''
  form.use_shared_content = !!profile.use_shared_content
  form.is_active = !!profile.is_active

  const existingGroup = profile.content_group ?? ''
  const presetValues = ['group-1', 'group-2', 'group-3', 'group-4']

  if (!existingGroup) {
    form.content_group_mode = 'group-1'
    form.custom_content_group = ''
    form.content_group = ''
  } else if (presetValues.includes(existingGroup)) {
    form.content_group_mode = existingGroup
    form.custom_content_group = ''
    form.content_group = existingGroup
  } else {
    form.content_group_mode = 'custom'
    form.custom_content_group = existingGroup
    form.content_group = existingGroup
  }
}

const addPublishTime = () => {
  form.publish_times.push('')
}

const removePublishTime = (index) => {
  if (form.publish_times.length === 1) {
    form.publish_times = ['']
    return
  }

  form.publish_times.splice(index, 1)
}

const toggleScheduleDay = (value) => {
  const numericValue = Number(value)

  if (form.schedule_days.includes(numericValue)) {
    form.schedule_days = form.schedule_days.filter(day => day !== numericValue)
    return
  }

  form.schedule_days = [...form.schedule_days, numericValue].sort((a, b) => a - b)
}

const scheduleSummary = (profile) => {
  const scheduleType = profile.schedule_type || 'daily'
  const target = profile.daily_target ?? 1

  if (scheduleType === 'weekly') {
    return `${target} / week`
  }

  if (scheduleType === 'monthly') {
    return `${target} / month`
  }

  return `${target} / day`
}

const submit = () => {
  if (!form.use_shared_content) {
    form.content_group = ''
  } else if (form.content_group_mode === 'custom') {
    form.content_group = (form.custom_content_group || '').trim()
  } else {
    form.content_group = form.content_group_mode
  }

  if (editingId.value) {
    form.patch(`/todo/profiles/${editingId.value}`, {
      preserveScroll: true,
      onSuccess: () => {
        showToast('Profile updated.', 'success')
        resetForm()
      },
    })
    return
  }

  form.post('/todo/profiles', {
    preserveScroll: true,
    onSuccess: () => {
      showToast('Profile created.', 'success')
      resetForm()
    },
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
    onSuccess: () => {
      showToast(
        profile.is_active ? 'Profile deactivated.' : 'Profile activated.',
        'success'
      )
    },
  })
}

const removeProfile = (profile) => {
  openConfirm({
    title: 'Delete Profile',
    message: `Delete "${profile.name}"? This action cannot be undone.`,
    confirmLabel: 'Delete Profile',
    danger: true,
    action: () => {
      if (editingId.value === profile.id) {
        resetForm()
      }

      router.delete(`/todo/profiles/${profile.id}`, {
        preserveScroll: true,
        onSuccess: () => {
          showToast('Profile deleted.', 'success')
        },
      })
    },
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
              ? 'Update an existing channel and its publishing rules.'
              : 'Define your channels and how much content you want to publish.' }}
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
              class="time-picker-dark w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
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
                Target Count
              </label>
              <select
                v-model="form.daily_target"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
              >
                <option v-for="n in 31" :key="n" :value="n">{{ n }}</option>
              </select>
              <div v-if="form.errors.daily_target" class="mt-2 text-sm text-rose-500">{{ form.errors.daily_target }}</div>
            </div>
          </div>

          <div>
            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
              Profile Description
            </label>
            <textarea
              v-model="form.description"
              rows="3"
              class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
              placeholder="Describe what kind of topics belong to this channel..."
            />
            <div class="mt-2 text-xs text-slate-500 dark:text-slate-400">
              Optional. This helps generate a better content model later.
            </div>
          </div>
          <div v-if="editingId">
  <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
    Content Bucket
  </label>
  <input
    :value="profiles.find(p => p.id === editingId)?.content_bucket || ''"
    type="text"
    readonly
    class="w-full rounded-2xl border border-slate-300 bg-slate-100 px-4 py-3 text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300"
  />
  <div class="mt-2 text-xs text-slate-500 dark:text-slate-400">
    Auto-generated. Shared groups always use the same content bucket.
  </div>
</div>
          <div class="mt-5 space-y-4">
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                Schedule Type
              </label>
              <select
                v-model="form.schedule_type"
                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white"
              >
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
              </select>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600 dark:border-slate-800 dark:bg-slate-950/60 dark:text-slate-300">
              <span v-if="form.schedule_type === 'daily'">
                This profile will publish <strong>{{ form.daily_target }}</strong> item(s) per day.
              </span>
              <span v-else-if="form.schedule_type === 'weekly'">
                This profile will publish <strong>{{ form.daily_target }}</strong> item(s) per week.
              </span>
              <span v-else>
                This profile will publish <strong>{{ form.daily_target }}</strong> item(s) per month.
              </span>
            </div>

            <div>
              <div class="mb-2 flex items-center justify-between">
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">
                  Publish Time Slots
                </label>
                <button
                  type="button"
                  @click="addPublishTime"
                  class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold dark:border-slate-700 dark:text-slate-200"
                >
                  Add Slot
                </button>
              </div>

              <div class="space-y-2">
                <div
                  v-for="(slot, index) in form.publish_times"
                  :key="index"
                  class="flex items-center gap-2"
                >
                  <input
                    v-model="form.publish_times[index]"
                    type="time"
                    class="time-picker-dark w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                  />
                  <button
                    type="button"
                    @click="removePublishTime(index)"
                    class="rounded-lg border border-rose-300 px-3 py-3 text-xs font-semibold text-rose-600 dark:border-rose-800 dark:text-rose-300"
                  >
                    Remove
                  </button>
                </div>
              </div>
            </div>

            <div v-if="form.schedule_type === 'weekly'">
              <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                Weekly Days
              </label>
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="day in weeklyOptions"
                  :key="day.value"
                  type="button"
                  @click="toggleScheduleDay(day.value)"
                  class="rounded-full px-3 py-2 text-xs font-semibold"
                  :class="form.schedule_days.includes(day.value)
                    ? 'bg-cyan-600 text-white'
                    : 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300'"
                >
                  {{ day.label }}
                </button>
              </div>
              <div class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                Select which days of the week this profile should publish on.
              </div>
            </div>

            <div v-if="form.schedule_type === 'monthly'">
              <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                Monthly Days
              </label>
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="day in monthlyOptions"
                  :key="day.value"
                  type="button"
                  @click="toggleScheduleDay(day.value)"
                  class="rounded-full px-3 py-2 text-xs font-semibold"
                  :class="form.schedule_days.includes(day.value)
                    ? 'bg-cyan-600 text-white'
                    : 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300'"
                >
                  {{ day.label }}
                </button>
              </div>
              <div class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                Select which days of the month this profile should publish on.
              </div>
            </div>
          </div>

          <div class="grid gap-4 md:grid-cols-2">
            <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 dark:border-slate-800 dark:bg-slate-950/60 dark:text-slate-300">
              <input v-model="form.use_shared_content" type="checkbox" class="h-4 w-4 rounded" />
              Shared Content Group
            </label>

            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
                Shared Group Preset
              </label>
              <select
                v-model="form.content_group_mode"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-500 disabled:opacity-60 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                :disabled="!form.use_shared_content"
              >
                <option
                  v-for="group in sharedGroupOptions"
                  :key="group.value"
                  :value="group.value"
                >
                  {{ group.label }}
                </option>
              </select>
            </div>
          </div>

          <div v-if="form.use_shared_content && form.content_group_mode === 'custom'">
            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
              Custom Group Name
            </label>
            <input
              v-model="form.custom_content_group"
              type="text"
              class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
              placeholder="world-main"
            />
            <div class="mt-2 text-xs text-slate-500 dark:text-slate-400">
              Use a custom name only if the default group presets are not enough.
            </div>
          </div>

          <div v-if="form.use_shared_content && form.content_group_mode !== 'custom'" class="text-xs text-slate-500 dark:text-slate-400">
            Profiles using the same group will be treated as shared-content channels in the generated content model.
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
              class="time-picker-dark w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
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
            <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">Capacity</div>
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
                The structure used by the generator to schedule content.
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
                      {{ scheduleSummary(profile) }}
                    </span>

                    <span
                      class="rounded-full bg-indigo-100 px-2.5 py-1 font-semibold text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300"
                    >
                      {{ profile.schedule_type || 'daily' }}
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

                    <span
                      v-if="profile.use_shared_content && profile.content_group"
                      class="rounded-full bg-emerald-100 px-2.5 py-1 font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300"
                    >
                      {{ profile.content_group }}
                    </span>

                    <span
                      v-if="profile.content_bucket"
                      class="rounded-full bg-slate-900 px-2.5 py-1 font-semibold text-white dark:bg-white dark:text-slate-900"
                    >
                      {{ profile.content_bucket }}
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

                  <div
                    v-if="profile.schedule_type === 'weekly' && profile.schedule_days?.length"
                    class="mt-2 text-xs text-slate-500 dark:text-slate-400"
                  >
                    Weekly days: {{ profile.schedule_days.join(', ') }}
                  </div>

                  <div
                    v-if="profile.schedule_type === 'monthly' && profile.schedule_days?.length"
                    class="mt-2 text-xs text-slate-500 dark:text-slate-400"
                  >
                    Monthly days: {{ profile.schedule_days.join(', ') }}
                  </div>

                  <div
                    v-if="profile.description"
                    class="mt-3 text-sm text-slate-600 dark:text-slate-400"
                  >
                    {{ profile.description }}
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
            No publishing profiles yet.
          </div>
        </div>
      </section>
    </div>
  </TodoLayout>
</template>

<style scoped>
.dark .time-picker-dark::-webkit-calendar-picker-indicator {
  filter: invert(1) brightness(2);
}
</style>