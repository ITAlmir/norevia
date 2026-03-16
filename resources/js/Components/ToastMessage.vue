<script setup>
const props = defineProps({
  open: {
    type: Boolean,
    default: false,
  },
  message: {
    type: String,
    default: '',
  },
  type: {
    type: String,
    default: 'success',
  },
})

const emit = defineEmits(['close'])

const closeToast = () => {
  emit('close')
}
</script>

<template>
  <transition
    enter-active-class="transition duration-200 ease-out"
    enter-from-class="translate-y-2 opacity-0"
    enter-to-class="translate-y-0 opacity-100"
    leave-active-class="transition duration-150 ease-in"
    leave-from-class="translate-y-0 opacity-100"
    leave-to-class="translate-y-2 opacity-0"
  >
    <div
      v-if="open"
      class="fixed bottom-5 right-5 z-[110] w-full max-w-sm rounded-2xl border px-4 py-4 shadow-2xl"
      :class="type === 'success'
        ? 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-900/40 dark:bg-emerald-950/90 dark:text-emerald-200'
        : type === 'error'
          ? 'border-rose-200 bg-rose-50 text-rose-800 dark:border-rose-900/40 dark:bg-rose-950/90 dark:text-rose-200'
          : 'border-slate-200 bg-white text-slate-800 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-100'"
    >
      <div class="flex items-start justify-between gap-3">
        <div class="text-sm font-semibold leading-6">
          {{ message }}
        </div>

        <button
          type="button"
          @click="closeToast"
          class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-current/15 text-xs opacity-70 transition hover:opacity-100"
          aria-label="Close"
        >
          ✕
        </button>
      </div>
    </div>
  </transition>
</template>