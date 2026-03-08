<script setup>
import { ref, nextTick } from 'vue'

const model = defineModel({ type: String, default: '' })

const props = defineProps({
  rows: { type: Number, default: 16 },
  placeholder: {
    type: String,
    default: 'Write description...',
  },
  showTemplate: { type: Boolean, default: false },
})

const textareaRef = ref(null)

function insertAtCursor(text) {
  const el = textareaRef.value

  if (!el) {
    model.value = (model.value || '') + text
    return
  }

  const start = el.selectionStart ?? 0
  const end = el.selectionEnd ?? 0
  const value = model.value || ''

  model.value = value.slice(0, start) + text + value.slice(end)

  nextTick(() => {
    el.focus()
    const pos = start + text.length
    el.setSelectionRange(pos, pos)
  })
}

function insertTemplate() {
  model.value = `Tool short introduction.

Built for gamers, creators and PC users.

Features

• Feature one
• Feature two
• Feature three

Who is it for?

• Target group one
• Target group two

System Requirements

• Windows 10 / 11
• 64-bit system`
}
</script>

<template>
  <div>
    <div class="flex flex-wrap gap-2 mb-3">
      <button
        v-if="showTemplate"
        type="button"
        @click="insertTemplate"
        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
               border border-emerald-300 dark:border-emerald-700/50
               text-emerald-700 dark:text-emerald-300
               text-sm font-semibold
               hover:bg-emerald-50 dark:hover:bg-emerald-900/20
               transition-all duration-200"
      >
        <span>＋</span>
        Insert Template
      </button>

      <button
        type="button"
        @click="insertAtCursor('\n')"
        class="px-3 py-2 rounded-lg border border-slate-600 text-gray-300 text-sm hover:bg-slate-700"
      >
        BR
      </button>

      <button
        type="button"
        @click="insertAtCursor('\n\n')"
        class="px-3 py-2 rounded-lg border border-slate-600 text-gray-300 text-sm hover:bg-slate-700"
      >
        Paragraph
      </button>

      <button
        type="button"
        @click="insertAtCursor('• ')"
        class="px-3 py-2 rounded-lg border border-slate-600 text-gray-300 text-sm hover:bg-slate-700"
      >
        Bullet
      </button>

      <button
        type="button"
        @click="insertAtCursor('Features\n\n')"
        class="px-3 py-2 rounded-lg border border-slate-600 text-gray-300 text-sm hover:bg-slate-700"
      >
        Features
      </button>

      <button
        type="button"
        @click="insertAtCursor('Who is it for?\n\n')"
        class="px-3 py-2 rounded-lg border border-slate-600 text-gray-300 text-sm hover:bg-slate-700"
      >
        Who is it for?
      </button>

      <button
        type="button"
        @click="insertAtCursor('System Requirements\n\n')"
        class="px-3 py-2 rounded-lg border border-slate-600 text-gray-300 text-sm hover:bg-slate-700"
      >
        System Requirements
      </button>
    </div>

    <textarea
      ref="textareaRef"
      v-model="model"
      :rows="rows"
      :placeholder="placeholder"
      class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-gray-200
             focus:outline-none focus:ring-2 focus:ring-blue-600 whitespace-pre-wrap"
    ></textarea>
  </div>
</template>