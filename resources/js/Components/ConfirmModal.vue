<script setup>
const props = defineProps({
  open: {
    type: Boolean,
    default: false,
  },
  title: {
    type: String,
    default: 'Confirm Action',
  },
  message: {
    type: String,
    default: 'Are you sure you want to continue?',
  },
  confirmLabel: {
    type: String,
    default: 'Confirm',
  },
  cancelLabel: {
    type: String,
    default: 'Cancel',
  },
  danger: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['confirm', 'close'])

const onClose = () => {
  emit('close')
}

const onConfirm = () => {
  emit('confirm')
}
</script>

<template>
  <div
    v-if="open"
    class="fixed inset-0 z-[100] flex items-center justify-center px-4"
  >
    <div
      class="absolute inset-0 bg-black/60 backdrop-blur-sm"
      @click="onClose"
    ></div>

    <div
      class="relative w-full max-w-md rounded-[28px] border border-slate-200 bg-white p-6 shadow-2xl dark:border-slate-800 dark:bg-slate-900"
    >
      <div class="pr-8">
        <h3 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">
          {{ title }}
        </h3>

        <p class="mt-3 text-sm leading-6 text-slate-600 dark:text-slate-400">
          {{ message }}
        </p>
      </div>

      <button
        type="button"
        @click="onClose"
        class="absolute right-4 top-4 inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 text-slate-500 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
        aria-label="Close"
      >
        ✕
      </button>

      <div class="mt-6 flex flex-wrap items-center justify-end gap-3">
        <button
          type="button"
          @click="onClose"
          class="inline-flex items-center rounded-2xl border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800"
        >
          {{ cancelLabel }}
        </button>

        <button
          type="button"
          @click="onConfirm"
          class="inline-flex items-center rounded-2xl px-5 py-3 text-sm font-bold text-white transition"
          :class="danger
            ? 'bg-rose-600 hover:bg-rose-700'
            : 'bg-cyan-600 hover:bg-cyan-700'"
        >
          {{ confirmLabel }}
        </button>
      </div>
    </div>
  </div>
</template>