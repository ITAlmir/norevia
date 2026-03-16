<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import MainLayout from '@/Layouts/MainLayout.vue'
import ConfirmModal from '@/Components/ConfirmModal.vue'
import ToastMessage from '@/Components/ToastMessage.vue'
import { useUiFeedback } from '@/Composables/useUiFeedback'

const {
  confirmState,
  toastState,
  closeConfirm,
  confirmYes,
  closeToast,
} = useUiFeedback()

const page = usePage()

const navItems = [
  { label: 'Dashboard', href: '/todo' },
  { label: 'Tasks', href: '/todo/tasks' },
  { label: 'Library', href: '/todo/library' },
  { label: 'Profiles', href: '/todo/profiles' },
  { label: 'Monthly Plan', href: '/todo/monthly-plan' },
  { label: 'Archive', href: '/todo/archive' },
  { label: 'Analytics', href: '/todo/analytics' },
]

const currentPath = computed(() => page.url || '')
</script>

<template>
  <MainLayout>
    <Head title="Content Command Center" />

    <div class="min-h-screen">
      <div
        class="relative overflow-hidden rounded-[28px] border border-slate-200/80 bg-white/90 shadow-[0_25px_80px_-35px_rgba(15,23,42,0.35)]
               dark:border-slate-800 dark:bg-slate-950/80"
      >
        <div class="absolute inset-0 pointer-events-none">
          <div
            class="absolute -top-24 right-[-60px] h-56 w-56 rounded-full blur-3xl opacity-20
                   bg-violet-500 dark:opacity-25"
          />
          <div
            class="absolute bottom-[-70px] left-[-30px] h-48 w-48 rounded-full blur-3xl opacity-20
                   bg-cyan-500 dark:opacity-20"
          />
        </div>

        <div class="relative border-b border-slate-200/80 px-5 py-5 dark:border-slate-800 md:px-8 md:py-7">
          <div class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between">
            <div>
              <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400">
                Norevia Productivity Space
              </div>

              <h1 class="mt-4 text-2xl font-black tracking-tight text-slate-900 dark:text-white md:text-4xl">
                Content Command Center
              </h1>

              <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600 dark:text-slate-300 md:text-base">
                Fokusiran pregled dana, plana i sadržaja bez admin haosa.
              </p>
            </div>

            <div class="grid grid-cols-2 gap-3 md:grid-cols-3">
              <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-800 dark:bg-slate-900/80">
                <div class="text-[11px] uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">Workspace</div>
                <div class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">Private</div>
              </div>

              <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-800 dark:bg-slate-900/80">
                <div class="text-[11px] uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">Mode</div>
                <div class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">Execution</div>
              </div>

              <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-slate-800 dark:bg-slate-900/80 col-span-2 md:col-span-1">
                <div class="text-[11px] uppercase tracking-[0.15em] text-slate-500 dark:text-slate-400">Owner</div>
                <div class="mt-1 truncate text-sm font-semibold text-slate-900 dark:text-white">
                  {{ $page.props.auth.user?.name }}
                </div>
              </div>
            </div>
          </div>

          <div class="mt-6 flex flex-wrap gap-2">
            <Link
              v-for="item in navItems"
              :key="item.href"
              :href="item.href"
              class="rounded-2xl px-4 py-2.5 text-sm font-semibold transition"
              :class="currentPath === item.href
                ? 'bg-slate-900 text-white shadow-lg dark:bg-white dark:text-slate-900'
                : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800'"
            >
              {{ item.label }}
            </Link>
          </div>
        </div>

        <div class="relative px-5 py-6 md:px-8 md:py-8">
          <slot />
        </div>
      </div>
    </div>
  </MainLayout>
  <ConfirmModal
  :open="confirmState.open"
  :title="confirmState.title"
  :message="confirmState.message"
  :confirm-label="confirmState.confirmLabel"
  :cancel-label="confirmState.cancelLabel"
  :danger="confirmState.danger"
  @confirm="confirmYes"
  @close="closeConfirm"
/>

<ToastMessage
  :open="toastState.open"
  :message="toastState.message"
  :type="toastState.type"
  @close="closeToast"
/>
</template>