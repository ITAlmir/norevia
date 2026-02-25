<template>
  <MainLayout>
    <div class="max-w-7xl mx-auto px-4 py-8">

      <!-- Header -->
      <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
          <h1 class="ui-page-title text-3xl font-bold">Demo Dashboard</h1>
          <p class="ui-page-subtitle mt-2">
            Read-only preview. Create an account to save your own sponsors,
            collaborations and pages.
          </p>
        </div>

        <div class="flex gap-2">
          <Link href="/register" class="ui-btn ui-btn-primary">
            Create free account →
          </Link>

          <Link href="/downloads" class="ui-btn ui-btn-soft">
            Browse Downloads →
          </Link>
        </div>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="ui-card p-6">
          <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">
            {{ stats.totalPages }}
          </div>
          <div class="ui-subtle text-sm">Pages</div>
        </div>

        <div class="ui-card p-6">
          <div class="text-3xl font-bold text-green-600 dark:text-green-400">
            {{ stats.publishedPages }}
          </div>
          <div class="ui-subtle text-sm">Published</div>
        </div>

        <div class="ui-card p-6">
          <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">
            {{ stats.totalSponsors }}
          </div>
          <div class="ui-subtle text-sm">Sponsors</div>
        </div>

        <div class="ui-card p-6">
          <div class="text-3xl font-bold text-amber-600 dark:text-amber-400">
            {{ stats.totalCollaborations }}
          </div>
          <div class="ui-subtle text-sm">Collaborations</div>
        </div>
      </div>

      <!-- Actions -->
      <div class="ui-card p-6 mb-8">
        <div class="flex items-center justify-between gap-3 mb-4">
          <h2 class="ui-section-title">Quick Actions</h2>

          <span class="ui-chip ui-muted border-slate-300 dark:border-slate-600">
            Demo mode (read-only)
          </span>
        </div>

        <div class="flex flex-wrap gap-4">
          <button class="ui-btn ui-btn-soft opacity-60 cursor-not-allowed">
            🔒 Create new page
          </button>

          <button class="ui-btn ui-btn-soft opacity-60 cursor-not-allowed">
            🔒 Add sponsor
          </button>

          <button class="ui-btn ui-btn-soft opacity-60 cursor-not-allowed">
            🔒 New collaboration
          </button>

          <Link href="/register" class="ui-btn ui-btn-primary">
            Unlock full dashboard →
          </Link>
        </div>
      </div>

      <!-- Recent Pages -->
      <div class="ui-card overflow-hidden">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
          <div class="flex justify-between items-center">
            <h2 class="ui-section-title">Recent Pages (preview)</h2>

            <Link href="/register" class="text-emerald-600 dark:text-emerald-400 font-semibold text-sm hover:underline">
              Create yours →
            </Link>
          </div>
        </div>

        <div>
          <div
            v-for="p in recentPages"
            :key="p.id"
            class="p-6 border-b border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800/30"
          >
            <div class="flex justify-between items-start gap-4">
              <div class="flex-1">
                <h3 class="ui-text text-lg font-medium mb-1">
                  {{ p.title }}
                </h3>

                <p class="ui-muted text-sm mb-2">
                  {{ p.excerpt }}
                </p>

                <div class="flex items-center space-x-4">
                  <span
                    :class="[
                      'px-2 py-1 text-xs rounded-full border',
                      p.status === 'published'
                        ? 'bg-green-50 text-green-700 border-green-200 dark:bg-green-900/30 dark:text-green-300 dark:border-green-700'
                        : 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-700'
                    ]"
                  >
                    {{ p.status }}
                  </span>

                  <span class="ui-subtle text-sm">
                    {{ formatDate(p.created_at) }}
                  </span>

                  <span class="ui-subtle text-sm">
                    {{ p.topic }}
                  </span>
                </div>
              </div>

              <div class="flex space-x-2">
                <button class="ui-btn ui-btn-soft opacity-60 cursor-not-allowed text-sm">
                  🔒 Edit
                </button>
                <button class="ui-btn ui-btn-soft opacity-60 cursor-not-allowed text-sm">
                  🔒 View
                </button>
              </div>
            </div>

            <div class="mt-4 flex items-center bg-slate-50 dark:bg-slate-900/50 rounded-md p-2">
              <input
                type="text"
                readonly
                :value="p.url"
                class="flex-1 bg-transparent ui-subtle text-sm px-2 py-1 focus:outline-none"
              />
              <button
                @click="copyUrl(p.url)"
                class="ui-btn ui-btn-soft text-sm ml-2"
              >
                Copy URL
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- CTA -->
      <div class="mt-10 ui-card p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
          <div>
            <div class="ui-text text-lg font-semibold">
              Ready to use it for real?
            </div>
            <div class="ui-muted text-sm mt-1">
              Create a free account and start tracking sponsors + collaborations in minutes.
            </div>
          </div>

          <div class="flex gap-2">
            <Link href="/register" class="ui-btn ui-btn-primary">
              Create account →
            </Link>
            <Link href="/login" class="ui-btn ui-btn-soft">
              Log in →
            </Link>
          </div>
        </div>
      </div>

    </div>
  </MainLayout>
</template>


<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
import { Link } from '@inertiajs/vue3'

const stats = {
  totalPages: 12,
  publishedPages: 8,
  totalSponsors: 23,
  totalCollaborations: 41,
}

const recentPages = [
  {
    id: 1,
    title: 'CS2: Optimize Network Settings (No Packet Loss)',
    excerpt: 'Practical steps to reduce jitter and improve consistency in CS2 matchmaking.',
    status: 'published',
    topic: 'cs2',
    created_at: '2026-02-10',
    url: 'https://norevia.app/blog/cs2/optimize-network-settings',
  },
  {
    id: 2,
    title: 'Windows 11: Low-latency Power Plan Basics',
    excerpt: 'What actually matters for stable frametimes and input response — without risky tweaks.',
    status: 'draft',
    topic: 'pc-optimization',
    created_at: '2026-02-14',
    url: 'https://norevia.app/blog/pc-optimization/low-latency-power-plan',
  },
  {
    id: 3,
    title: 'Creator Workflow: Sponsor Outreach Email Template',
    excerpt: 'A short, effective template to start sponsor conversations and track follow-ups.',
    status: 'published',
    topic: 'creator-tools',
    created_at: '2026-02-17',
    url: 'https://norevia.app/blog/creator-tools/sponsor-outreach-template',
  },
]

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
}

const copyUrl = async (url) => {
  try {
    await navigator.clipboard.writeText(url)
  } catch (e) {}
}
</script>
