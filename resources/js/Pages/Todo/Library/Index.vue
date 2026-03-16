<script setup>
import TodoLayout from '@/Layouts/TodoLayout.vue'
import { useForm, router } from '@inertiajs/vue3'
import { useUiFeedback } from '@/Composables/useUiFeedback'
import { computed, ref, watch, nextTick } from 'vue'

const props = defineProps({
  topics: {
    type: Array,
    default: () => [],
  },
  profiles: {
    type: Array,
    default: () => [],
  },
})

const { openConfirm, showToast } = useUiFeedback()

const editingId = ref(null)

const bulkForm = useForm({
  bulk_input: '',
})

const importPreview = ref(null)
const allowHistoricalWarnings = ref(false)
const previewConfirmed = ref(false)
const importMode = ref('idle') 
const importPreviewRef = ref(null)

const editForm = useForm({
  title: '',
  platform: '',
  series: '',
  voice_tool: '',
  content_bucket: '',
  caption: '',
  description: '',
  hashtags: '',
  script_notes: '',
  status: 'available',
})

const previewBulk = () => {
  if (!bulkForm.bulk_input.trim()) return

  router.post('/todo/library/preview-import', {
    bulk_input: bulkForm.bulk_input,
  }, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: (page) => {
      const preview = page?.props?.flash?.importPreview
      if (preview) {
        importPreview.value = preview
      }
    },
    onError: () => {
      showToast('Preview failed.', 'error')
    },
  })
}

const submitBulk = async () => {
  if (!bulkForm.bulk_input.trim()) return

  if (importMode.value === 'review') {
    confirmImport()
    return
  }

  const preview = await runImportPreview()
  if (!preview) return

  const hasDuplicates = (preview.summary?.skipped_duplicates_count || 0) > 0
  const hasWarnings = (preview.summary?.historical_warnings_count || 0) > 0

  if (hasDuplicates || hasWarnings) {
    importMode.value = 'review'
    showToast('Review the import result, then confirm import.', 'success')
    await scrollToImportPreview()
    return
  }

  confirmImport()
}

const startEdit = (topic) => {
  editingId.value = topic.id
  editForm.title = topic.title ?? ''
  editForm.platform = topic.platform ?? ''
  editForm.series = topic.series ?? ''
  editForm.voice_tool = topic.voice_tool ?? ''
  editForm.content_bucket = topic.content_bucket ?? ''
  editForm.caption = topic.caption ?? ''
  editForm.description = topic.description ?? ''
  editForm.hashtags = topic.hashtags ?? ''
  editForm.script_notes = topic.script_notes ?? ''
  editForm.status = topic.status ?? 'available'
}

const submitEdit = () => {
  if (!editingId.value) return

  editForm.patch(`/todo/library/${editingId.value}`, {
    preserveScroll: true,
    onSuccess: () => {
      showToast('Topic updated.', 'success')
      cancelEdit()
    },
  })
}

const cancelEdit = () => {
  editingId.value = null
  editForm.reset()
  editForm.clearErrors()
  editForm.status = 'available'
}

const updateStatus = (topic, status) => {
  router.patch(`/todo/library/${topic.id}`, { status }, {
    preserveScroll: true,
    onSuccess: () => {
      showToast(
        status === 'used'
          ? 'Topic marked as used.'
          : status === 'archived'
            ? 'Topic archived.'
            : 'Topic updated.',
        'success'
      )
    },
  })
}

const removeTopic = (topic) => {
  openConfirm({
    title: 'Delete Topic',
    message: `Delete "${topic.title}"? This action cannot be undone.`,
    confirmLabel: 'Delete Topic',
    danger: true,
    action: () => router.delete(`/todo/library/${topic.id}`, {
      preserveScroll: true,
      onSuccess: () => {
        if (editingId.value === topic.id) {
          cancelEdit()
        }
        showToast('Topic deleted.', 'success')
      },
    }),
  })
}

const clearAvailableTopics = () => {
  openConfirm({
    title: 'Clear Available Topics',
    message: 'Delete all available topics? Used and archived topics will stay untouched.',
    confirmLabel: 'Clear Topics',
    danger: true,
    action: () => router.post('/todo/library/clear-available', {}, {
      preserveScroll: true,
      onSuccess: () => {
        if (editingId.value) {
          cancelEdit()
        }
        showToast('Available topics cleared.', 'success')
      },
    }),
  })
}

const resetActiveWorkflow = () => {
  openConfirm({
    title: 'Reset Active Workflow',
    message: 'This will remove all available topics, unfinished tasks, and active monthly plan items. Completed tasks and used topics will stay in archive.',
    confirmLabel: 'Reset Workflow',
    danger: true,
    action: () => router.post('/todo/library/reset-active-workflow', {}, {
      preserveScroll: true,
      onSuccess: () => {
        if (editingId.value) {
          cancelEdit()
        }
        showToast('Active workflow reset completed.', 'success')
      },
    }),
  })
}

const openArchive = () => {
  router.visit('/todo/archive')
}

const openMonthlyPlan = () => {
  router.visit('/todo/monthly-plan')
}

const copy = async (text) => {
  if (!text) return

  await navigator.clipboard.writeText(text)
  showToast('Copied to clipboard.', 'success')
}

const copyTitle = async (topic) => {
  if (!topic?.title) return

  await navigator.clipboard.writeText(topic.title)
  showToast('Title copied.', 'success')
}

const copyPack = async (topic) => {
  const parts = []

  if (topic.caption) parts.push(topic.caption)
  if (topic.description) parts.push(topic.description)
  if (topic.hashtags) parts.push(topic.hashtags)

  const text = parts.join('\n\n')

  if (!text.trim()) return

  await navigator.clipboard.writeText(text)
  showToast('Full post copied.', 'success')
}

const statusClass = (status) => {
  if (status === 'used') {
    return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'
  }

  if (status === 'archived') {
    return 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300'
  }

  return 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300'
}

const availableTopics = computed(() => {
  return props.topics.filter(topic => topic.status === 'available')
})

const stats = computed(() => {
  return {
    total: props.topics.length,
    available: props.topics.filter(t => t.status === 'available').length,
    used: props.topics.filter(t => t.status === 'used').length,
    archived: props.topics.filter(t => t.status === 'archived').length,
  }
})

const estimateMonthlyTopics = (profile) => {
  const target = Number(profile.daily_target || 0)
  const scheduleType = profile.schedule_type || 'daily'
  const scheduleDays = Array.isArray(profile.schedule_days) ? profile.schedule_days : []

  if (scheduleType === 'daily') {
    return target * 30
  }

  if (scheduleType === 'weekly') {
    const weeklyDaysCount = scheduleDays.length || target || 1
    return weeklyDaysCount * 4
  }

  if (scheduleType === 'monthly') {
    const monthlyDaysCount = scheduleDays.length || target || 1
    return monthlyDaysCount
  }

  return target * 30
}

const runImportPreview = async () => {
  if (!bulkForm.bulk_input.trim()) {
    importPreview.value = null
    previewConfirmed.value = false
    importMode.value = 'idle'
    return null
  }

  try {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''

    const response = await fetch('/todo/library/preview-import', {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': csrf,
      },
      body: JSON.stringify({
        bulk_input: bulkForm.bulk_input,
      }),
    })

    if (!response.ok) {
      const text = await response.text()
      console.error('Preview response error:', text)
      throw new Error('Preview request failed')
    }

    const data = await response.json()
    importPreview.value = data
    previewConfirmed.value = true

    return data
  } catch (error) {
    console.error('Preview failed:', error)
    importPreview.value = null
    previewConfirmed.value = false
    importMode.value = 'idle'
    showToast('Preview failed.', 'error')
    return null
  }
}

const scrollToImportPreview = async () => {
  await nextTick()

  if (!importPreviewRef.value) return

  importPreviewRef.value.scrollIntoView({
    behavior: 'smooth',
    block: 'center',
  })
}

const confirmImport = () => {
  const allowedWarningTitles = allowHistoricalWarnings.value && importPreview.value
    ? (importPreview.value.historical_warnings || []).map(item => item.title)
    : []

  bulkForm.transform((data) => ({
    ...data,
    allowed_warning_titles: allowedWarningTitles,
  })).post('/todo/library', {
    preserveScroll: true,
    onSuccess: () => {
      bulkForm.reset()
      importPreview.value = null
      allowHistoricalWarnings.value = false
      previewConfirmed.value = false
      importMode.value = 'idle'
      showToast('Topics imported.', 'success')
    },
  })
}

const allowAllWarningsAndImport = () => {
  allowHistoricalWarnings.value = true
  confirmImport()
}

const declineAllWarningsAndImport = () => {
  allowHistoricalWarnings.value = false
  confirmImport()
}

const activeProfilesForModel = computed(() => {
  return (props.profiles || []).filter(profile => profile.is_active)
})

const groupedSharedProfiles = computed(() => {
  const groups = {}

  for (const profile of activeProfilesForModel.value) {
    if (!profile.use_shared_content || !profile.content_group) continue

    if (!groups[profile.content_group]) {
      groups[profile.content_group] = []
    }

    groups[profile.content_group].push(profile)
  }

  return groups
})

const sharedGroupSummaries = computed(() => {
  return Object.entries(groupedSharedProfiles.value).map(([groupName, profiles]) => {
    const estimatedSharedMonthlyTopics = Math.max(
      ...profiles.map(profile => estimateMonthlyTopics(profile)),
      0
    )

    return {
      group_name: groupName,
      profiles,
      content_bucket: profiles[0]?.content_bucket || '',
      estimated_shared_monthly_topics: estimatedSharedMonthlyTopics,
    }
  })
})

const contentModelText = computed(() => {
  const lines = []

  lines.push('CONTENT GENERATION MODEL')
  lines.push('')
  lines.push('Profiles:')
  lines.push('')

  activeProfilesForModel.value.forEach((profile, index) => {
    lines.push(`${index + 1}.`)
    lines.push(`Name: ${profile.name}`)
    lines.push(`Platform: ${profile.platform}`)
    lines.push(`Schedule type: ${profile.schedule_type || 'daily'}`)
    lines.push(`Target: ${profile.daily_target}`)
    lines.push(`Estimated monthly topics: ${estimateMonthlyTopics(profile)}`)

    if (profile.default_voice_tool) {
      lines.push(`Default voice tool: ${profile.default_voice_tool}`)
    }

    if (profile.description) {
      lines.push(`Description: ${profile.description}`)
    }

    if (profile.use_shared_content && profile.content_group) {
      lines.push(`Shared content group: ${profile.content_group}`)
    } else {
      lines.push('Shared content group: none')
    }
    if (profile.content_bucket) {
  lines.push(`Content bucket: ${profile.content_bucket}`)
}

    lines.push('')
  })

  lines.push('Shared groups:')
  lines.push('')

  if (sharedGroupSummaries.value.length) {
    sharedGroupSummaries.value.forEach((group) => {
      lines.push(group.group_name)
      if (group.content_bucket) {
  lines.push(`Content bucket: ${group.content_bucket}`)
}
      lines.push('Profiles:')

      group.profiles.forEach((profile) => {
        lines.push(`- ${profile.name}`)
      })

      lines.push(`Estimated shared monthly topics needed: ${group.estimated_shared_monthly_topics}`)
      lines.push('')
    })
  } else {
    lines.push('No shared groups defined.')
    lines.push('')
  }

  lines.push('Instructions:')
  lines.push('- Generate unique topics for each profile or shared content group.')
  lines.push('- Profiles in the same shared content group must use the same content_bucket.')
  lines.push('- Every generated topic line must include content_bucket.')
  lines.push('- Keep the topics aligned with each profile description.')
  lines.push('- Output format for shortest bulk import: platform|series|content_bucket|title')
  lines.push('- Output format for short bulk import: platform|series|voice_tool|content_bucket|title')
  lines.push('- Output format for medium bulk import: platform|series|voice_tool|content_bucket|title|caption')
  lines.push('- Output format for long bulk import: platform|series|voice_tool|content_bucket|title|caption|description|hashtags')

  return lines.join('\n')
})

const copyContentModel = async () => {
  if (!contentModelText.value.trim()) return

  await navigator.clipboard.writeText(contentModelText.value)
  showToast('Content model copied.', 'success')
}

watch(
  () => bulkForm.bulk_input,
  () => {
    importPreview.value = null
    allowHistoricalWarnings.value = false
    previewConfirmed.value = false
    importMode.value = 'idle'
  }
)
</script>

<template>
  <TodoLayout>
    <div class="grid gap-6 xl:grid-cols-[0.95fr,1.05fr]">
      <section class="rounded-[28px] border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
        <div>
          <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">
            Bulk Import Topics
          </h2>
          <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
            Paste multiple topics at once. One line = one topic.
          </p>
        </div>

        <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600 dark:border-slate-800 dark:bg-slate-950/60 dark:text-slate-300">
          <div class="font-semibold text-slate-900 dark:text-white">Supported Formats:</div>
          <div class="mt-2 space-y-1 font-mono text-xs leading-6">
            <div>Shortest: platform | series | content_bucket | title</div>
            <div>Short: platform | series | voice_tool | content_bucket | title</div>
            <div>Medium: platform | series | voice_tool | content_bucket | title | caption</div>
            <div>Long: platform | series | voice_tool | content_bucket | title | caption | description | hashtags</div>
          </div>

          <div class="mt-4 font-semibold text-slate-900 dark:text-white">Example:</div>
          <div class="mt-2 font-mono text-xs leading-6">
            tiktok|worldfacts|norevia-world-4821|Why ice floats on water
          </div>
        </div>

        <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600 dark:border-slate-800 dark:bg-slate-950/60 dark:text-slate-300">
          <div class="font-semibold text-slate-900 dark:text-white">Workflow:</div>
          <div class="mt-2 leading-6">
            Only active <strong>available</strong> topics are shown in this library list.
            Used history belongs in archive.
          </div>
        </div>

        <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60">
  <form class="space-y-4" @submit.prevent>
    <div>
      <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">
        Bulk Input
      </label>

      <textarea
        v-model="bulkForm.bulk_input"
        rows="14"
        class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-4 text-sm text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
        placeholder="tiktok|cs2fps|bucket-1234|CS2 launch options"
      />

      <div v-if="bulkForm.errors.bulk_input" class="mt-2 text-sm text-rose-500">
        {{ bulkForm.errors.bulk_input }}
      </div>
    </div>

    <div
  v-if="importPreview"
  ref="importPreviewRef"
  class="rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-800 dark:bg-slate-900"
>
      <div class="text-lg font-black text-slate-900 dark:text-white">
        Import Preview
      </div>

      <div class="mt-4 grid gap-3 sm:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-800 dark:bg-slate-950/60">
          <div class="text-xs uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">Parsed</div>
          <div class="mt-1 text-xl font-black text-slate-900 dark:text-white">
            {{ importPreview.summary?.parsed_count || 0 }}
          </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-800 dark:bg-slate-950/60">
          <div class="text-xs uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">Safe Import</div>
          <div class="mt-1 text-xl font-black text-emerald-600 dark:text-emerald-400">
            {{ importPreview.summary?.safe_import_count || 0 }}
          </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-800 dark:bg-slate-950/60">
          <div class="text-xs uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">Skipped Duplicates</div>
          <div class="mt-1 text-xl font-black text-rose-600 dark:text-rose-400">
            {{ importPreview.summary?.skipped_duplicates_count || 0 }}
          </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-800 dark:bg-slate-950/60">
          <div class="text-xs uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">Historical Warnings</div>
          <div class="mt-1 text-xl font-black text-amber-600 dark:text-amber-400">
            {{ importPreview.summary?.historical_warnings_count || 0 }}
          </div>
        </div>
      </div>

      <div
        v-if="importPreview.skipped_duplicates?.length"
        class="mt-4 rounded-2xl border border-rose-200 bg-rose-50 p-4 dark:border-rose-900/40 dark:bg-rose-950/20"
      >
        <div class="text-sm font-black text-rose-700 dark:text-rose-300">
          Skipped duplicates from current paste
        </div>

        <div class="mt-2 space-y-1 text-sm text-rose-700 dark:text-rose-300">
          <div
            v-for="(item, index) in importPreview.skipped_duplicates"
            :key="`dup-${index}`"
          >
            {{ item.title }}
            <span class="opacity-70">({{ item.platform }} · {{ item.content_bucket }})</span>
          </div>
        </div>
      </div>

      <div
  v-if="importPreview.historical_warnings?.length"
  class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-900/40 dark:bg-amber-950/20"
>
  <div class="text-sm font-black text-amber-700 dark:text-amber-300">
    Used before!
  </div>

  <hr class="my-3 border-amber-200 dark:border-amber-800" />

  <ul class="space-y-1 text-sm text-amber-700 dark:text-amber-300">
    <li
      v-for="(item, index) in importPreview.historical_warnings"
      :key="`warn-${index}`"
      class="leading-6"
    >
      - {{ item.title }}
      <span class="opacity-80">
        — previously used on {{ item.previously_used_at || 'unknown date' }}
      </span>
    </li>
  </ul>

  <label class="mt-4 flex items-center gap-3 text-sm font-semibold text-amber-700 dark:text-amber-300">
    <input
      v-model="allowHistoricalWarnings"
      type="checkbox"
      class="h-4 w-4 rounded"
    />
    Allow all historical warnings in this import
  </label>

  <div class="mt-3 flex flex-wrap gap-2">
    <button
      type="button"
      @click="allowAllWarningsAndImport"
      class="rounded-xl bg-amber-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-amber-700"
    >
      Allow All Warnings
    </button>

    <button
      type="button"
      @click="declineAllWarningsAndImport"
      class="rounded-xl border border-amber-300 px-4 py-2 text-sm font-bold text-amber-700 transition hover:bg-amber-100 dark:border-amber-800 dark:text-amber-300 dark:hover:bg-amber-950/30"
    >
      Decline All Warnings
    </button>
  </div>
</div>
    </div>

    <div class="flex flex-wrap items-center gap-3 pt-2">
      <button
        type="button"
        :disabled="bulkForm.processing"
        @click="submitBulk"
        class="inline-flex items-center rounded-2xl bg-cyan-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-cyan-700 disabled:opacity-60"
      >
        {{
          bulkForm.processing
            ? 'Importing...'
            : importPreview && previewConfirmed
              ? 'Confirm Import'
              : 'Import Topics'
        }}
      </button>

      <button
        type="button"
        @click="clearAvailableTopics"
        class="inline-flex items-center rounded-2xl bg-rose-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-rose-700"
      >
        Clear Available Topics
      </button>

      <button
        type="button"
        @click="openMonthlyPlan"
        class="inline-flex items-center rounded-2xl border border-slate-300 px-4 py-2 text-sm font-bold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
      >
        Open Monthly Plan
      </button>
    </div>
  </form>
</div>
        <div class="mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60">
          <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
            <div>
              <div class="text-lg font-black text-slate-900 dark:text-white">
                Generated Content Model
              </div>
              <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                Copy this model into ChatGPT to generate the exact topics needed for your active profiles.
              </p>
            </div>

            <div class="flex flex-wrap gap-2">              
              <button
                type="button"
                @click="copyContentModel"
                class="mt-4 inline-flex items-center rounded-2xl bg-cyan-600 px-2 py-1 text-sm font-bold text-white transition hover:bg-cyan-700"
              >
                Copy Model
              </button>
              
            </div>
          </div>

          <div class="mt-0 rounded-2xl border border-dashed border-slate-300 bg-white p-4 text-xs whitespace-pre-wrap text-slate-700 dark:border-slate-700 dark:bg-slate-950 dark:text-slate-300">
            {{ contentModelText }}
          </div>
        </div>
       
      </section>

      <section class="space-y-6">
        <div
          v-if="editingId"
          class="rounded-[24px] border border-cyan-200 bg-cyan-50/40 p-5 dark:border-cyan-900/40 dark:bg-cyan-950/10"
        >
          <h2 class="text-xl font-black text-slate-900 dark:text-white">
            Edit Topic
          </h2>
          <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
            Update a single topic without deleting it.
          </p>

          <form class="mt-5 space-y-4" @submit.prevent="submitEdit">
            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">Title</label>
              <input
                v-model="editForm.title"
                type="text"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
              />
            </div>

            <div class="grid gap-4 md:grid-cols-3">
              <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">Platform</label>
                <input
                  v-model="editForm.platform"
                  type="text"
                  class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                />
              </div>

              <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">Series</label>
                <input
                  v-model="editForm.series"
                  type="text"
                  class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                />
              </div>

              <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">Voice Tool</label>
                <input
                  v-model="editForm.voice_tool"
                  type="text"
                  class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
                />
              </div>

              <div>
                <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">Content Bucket</label>
                <input
                  :value="editForm.content_bucket"
                  type="text"
                  readonly
                  class="w-full rounded-2xl border border-slate-300 bg-slate-100 px-4 py-3 text-sm text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300"
                />
              </div>

            </div>

            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">Caption</label>
              <textarea
                v-model="editForm.caption"
                rows="3"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
              />
            </div>

            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">Description</label>
              <textarea
                v-model="editForm.description"
                rows="3"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
              />
            </div>

            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">Hashtags</label>
              <textarea
                v-model="editForm.hashtags"
                rows="2"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
              />
            </div>

            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">Script Notes</label>
              <textarea
                v-model="editForm.script_notes"
                rows="3"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
              />
            </div>

            <div>
              <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-slate-300">Status</label>
              <select
                v-model="editForm.status"
                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-cyan-500 dark:border-slate-700 dark:bg-slate-950 dark:text-white"
              >
                <option value="available">available</option>
                <option value="used">used</option>
                <option value="archived">archived</option>
              </select>
            </div>

            <div class="flex flex-wrap items-center gap-3">
              <button
                type="submit"
                :disabled="editForm.processing"
                class="inline-flex items-center rounded-2xl bg-cyan-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-cyan-700 disabled:opacity-60"
              >
                {{ editForm.processing ? 'Saving...' : 'Save Changes' }}
              </button>

              <button
                type="button"
                @click="cancelEdit"
                class="inline-flex items-center rounded-2xl border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
              >
                Cancel
              </button>
            </div>
          </form>
        </div>

        <div class="grid gap-4 sm:grid-cols-4">
          <div class="rounded-[24px] border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
            <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">
              Total
            </div>
            <div class="mt-3 text-3xl font-black text-slate-900 dark:text-white">
              {{ stats.total }}
            </div>
          </div>

          <div class="rounded-[24px] border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
            <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">
              Available
            </div>
            <div class="mt-3 text-3xl font-black text-cyan-600 dark:text-cyan-400">
              {{ stats.available }}
            </div>
          </div>

          <div class="rounded-[24px] border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
            <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">
              Used
            </div>
            <div class="mt-3 text-3xl font-black text-emerald-600 dark:text-emerald-400">
              {{ stats.used }}
            </div>
          </div>

          <div class="rounded-[24px] border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
            <div class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 dark:text-slate-400">
              Archived
            </div>
            <div class="mt-3 text-3xl font-black text-slate-900 dark:text-white">
              {{ stats.archived }}
            </div>
          </div>
        </div>

        <div class="rounded-[28px] border border-slate-200 bg-white p-6 dark:border-slate-800 dark:bg-slate-900">
          <div class="flex items-center justify-between gap-4">
            <div>

            <button
              type="button"
              @click="openArchive"
              class="inline-flex items-center rounded-2xl border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
            >
              Open Archive
            </button>

              <h2 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                Content Library
              </h2>
              <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                Only active available topics are shown here.
              </p>

              <button
              type="button"
              @click="resetActiveWorkflow"
              class="inline-flex items-center rounded-2xl bg-rose-700 px-5 py-3 text-sm font-bold text-white transition hover:bg-rose-800"
            >
              Reset Active Workflow
            </button>
            </div>

            <div class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300">
              {{ availableTopics.length }} active topics
            </div>
          </div>

          <div v-if="availableTopics.length" class="mt-6 space-y-3">
            <div
              v-for="topic in availableTopics"
              :key="topic.id"
              :class="editingId === topic.id
                ? 'rounded-2xl border border-cyan-400 bg-cyan-50/40 p-4 dark:border-cyan-700 dark:bg-cyan-950/10'
                : 'rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-800 dark:bg-slate-950/60'"
            >
              <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                <div class="min-w-0 flex-1">
                  <div class="text-base font-black leading-6 break-words text-slate-900 dark:text-white">
                    {{ topic.title }}
                  </div>

                  <div class="mt-2 flex flex-wrap gap-2 text-xs">
                    <span
                      v-if="topic.content_bucket"
                      class="rounded-full bg-slate-900 px-2.5 py-1 font-semibold text-white dark:bg-white dark:text-slate-900"
                    >
                      {{ topic.content_bucket }}
                    </span>

                    <span
                      v-if="topic.shared_content_group"
                      class="rounded-full bg-emerald-100 px-2.5 py-1 font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300"
                    >
                      {{ topic.shared_content_group }}
                    </span>

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
                      :class="statusClass(topic.status)"
                    >
                      {{ topic.status }}
                    </span>
                  </div>

                  <div v-if="topic.caption" class="mt-3 text-sm leading-6 text-slate-700 dark:text-slate-300">
                    {{ topic.caption }}
                  </div>

                  <div v-if="topic.description" class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-400">
                    {{ topic.description }}
                  </div>

                  <div v-if="topic.hashtags" class="mt-2 text-xs break-words text-cyan-600 dark:text-cyan-400">
                    {{ topic.hashtags }}
                  </div>
                </div>

                <div class="flex w-full flex-wrap items-center gap-2 xl:w-auto xl:max-w-[360px] xl:justify-end">
                  <button
                    type="button"
                    @click="startEdit(topic)"
                    class="rounded-xl border border-cyan-300 px-3 py-2 text-sm font-semibold text-cyan-700 transition hover:bg-cyan-50 dark:border-cyan-800 dark:text-cyan-300 dark:hover:bg-cyan-950/30"
                  >
                    Edit
                  </button>

                  <button
                    type="button"
                    @click="copyTitle(topic)"
                    class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                  >
                    Copy Title
                  </button>

                  <button
                    type="button"
                    @click="copy(topic.caption)"
                    class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                  >
                    Copy Caption
                  </button>

                  <button
                    type="button"
                    @click="copy(topic.description)"
                    class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                  >
                    Copy Description
                  </button>

                  <button
                    type="button"
                    @click="copy(topic.hashtags)"
                    class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                  >
                    Copy Hashtags
                  </button>

                  <button
                    type="button"
                    @click="copyPack(topic)"
                    class="rounded-xl bg-cyan-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-cyan-700"
                  >
                    Copy All
                  </button>

                  <button
                    type="button"
                    @click="updateStatus(topic, 'used')"
                    class="rounded-xl border border-emerald-300 px-3 py-2 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50 dark:border-emerald-800 dark:text-emerald-300 dark:hover:bg-emerald-950/30"
                  >
                    Mark Used
                  </button>

                  <button
                    type="button"
                    @click="updateStatus(topic, 'archived')"
                    class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
                  >
                    Archive
                  </button>

                  <button
                    type="button"
                    @click="removeTopic(topic)"
                    class="rounded-xl bg-rose-600 px-3 py-2 text-sm font-semibold text-white transition hover:bg-rose-700"
                  >
                    Delete
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div
            v-else
            class="mt-6 rounded-2xl border border-dashed border-slate-300 p-8 text-center text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400"
          >
            No available topics right now.
          </div>
        </div>
      </section>
    </div>
  </TodoLayout>
</template>
