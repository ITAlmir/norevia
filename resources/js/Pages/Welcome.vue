<!-- resources/js/Pages/Welcome.vue -->
<template>
  <MainLayout>
    <div class="max-w-7xl mx-auto px-4 py-10 md:py-14">
      <!-- HERO -->
      <section class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white/70 p-6 md:p-10
                      dark:border-slate-800 dark:bg-slate-950/30">
        <!-- subtle glow -->
        <div class="pointer-events-none absolute -top-32 -right-32 h-80 w-80 rounded-full blur-3xl opacity-30
                    bg-emerald-400 dark:opacity-20" />
        <div class="pointer-events-none absolute -bottom-40 -left-40 h-96 w-96 rounded-full blur-3xl opacity-25
                    bg-blue-400 dark:opacity-15" />

        <div class="relative">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border text-xs font-semibold
                      bg-white border-slate-200 text-slate-700
                      dark:bg-slate-900/40 dark:border-slate-800 dark:text-slate-200">
            <span class="tracking-wide">NOREVIA</span>
            <span class="text-slate-400 dark:text-slate-500">•</span>
            <span class="font-medium text-slate-500 dark:text-slate-400">Dashboard • Downloads • Guides</span>
          </div>

          <h1 class="mt-5 text-4xl md:text-6xl font-black tracking-tight text-slate-900 dark:text-white">
            One home for your creator workflow —
            <span class="text-emerald-700 dark:text-emerald-400">plus tools</span>
            that actually help.
          </h1>

          <p class="mt-4 text-lg text-slate-600 dark:text-slate-300 max-w-2xl">
            Track sponsorships & collaborations, download small utilities, and read practical guides that link directly to tools.
          </p>

          <div class="mt-7 flex flex-col sm:flex-row gap-3">
            <Link
              v-if="auth.user"
              href="/dashboard"
              class="inline-flex items-center justify-center px-6 py-3 rounded-xl font-semibold text-white
                     bg-emerald-600 hover:bg-emerald-700 transition shadow-sm"
            >
              Open Dashboard →
            </Link>

            <Link
              v-else
              href="/register"
              class="inline-flex items-center justify-center px-6 py-3 rounded-xl font-semibold text-white
                     bg-emerald-600 hover:bg-emerald-700 transition shadow-sm"
            >
              Create free account →
            </Link>

            <Link
              v-if="!auth.user"
              href="/demo"
              class="inline-flex items-center justify-center px-6 py-3 rounded-xl font-semibold border transition
                     bg-white border-slate-300 text-slate-900 hover:bg-slate-100
                     dark:bg-slate-900/30 dark:border-slate-700 dark:text-slate-100 dark:hover:bg-slate-800/60"
            >
              View demo (no login) →
            </Link>

            <Link
              href="/downloads"
              class="inline-flex items-center justify-center px-6 py-3 rounded-xl font-semibold border transition
                     bg-white border-slate-300 text-slate-900 hover:bg-slate-100
                     dark:bg-slate-900/30 dark:border-slate-700 dark:text-slate-100 dark:hover:bg-slate-800/60"
            >
              Browse Downloads
            </Link>
          </div>

          <div class="mt-4 text-sm text-slate-500 dark:text-slate-400">
            No credit card. Built for organic growth: tools → guides → upgrades later.
          </div>

          <!-- micro trust row -->
          <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="rounded-2xl border p-4 bg-white/70 border-slate-200
                        dark:bg-slate-950/30 dark:border-slate-800">
              <div class="text-xs text-slate-500 dark:text-slate-400">Primary focus</div>
              <div class="mt-1 font-semibold text-slate-900 dark:text-white">Creators • CS2 • Windows utilities</div>
            </div>
            <div class="rounded-2xl border p-4 bg-white/70 border-slate-200
                        dark:bg-slate-950/30 dark:border-slate-800">
              <div class="text-xs text-slate-500 dark:text-slate-400">What you get</div>
              <div class="mt-1 font-semibold text-slate-900 dark:text-white">Workflow + downloads + guides</div>
            </div>
            <div class="rounded-2xl border p-4 bg-white/70 border-slate-200
                        dark:bg-slate-950/30 dark:border-slate-800">
              <div class="text-xs text-slate-500 dark:text-slate-400">Style</div>
              <div class="mt-1 font-semibold text-slate-900 dark:text-white">Fast UI, low friction</div>
            </div>
          </div>
        </div>
      </section>

      <hr class="my-12 border-slate-200 dark:border-slate-800" />

      <!-- WHAT IS NOREVIA -->
      <section class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
        <div class="md:col-span-5">
          <h2 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white">
            What Norevia is
          </h2>
          <p class="mt-3 text-slate-600 dark:text-slate-300">
            A practical ecosystem: you read a guide, download a tool, and save the workflow inside your dashboard.
            It’s built for long-term consistency — not “shiny feature overload”.
          </p>

          <div class="mt-5 flex flex-wrap gap-2">
            <Link
              href="/blog"
              class="px-4 py-2 rounded-xl text-sm font-semibold border transition
                     bg-white border-slate-300 text-slate-900 hover:bg-slate-100
                     dark:bg-slate-900/30 dark:border-slate-700 dark:text-slate-100 dark:hover:bg-slate-800/60"
            >
              Read guides →
            </Link>
            <Link
              href="/downloads"
              class="px-4 py-2 rounded-xl text-sm font-semibold border transition
                     bg-white border-slate-300 text-slate-900 hover:bg-slate-100
                     dark:bg-slate-900/30 dark:border-slate-700 dark:text-slate-100 dark:hover:bg-slate-800/60"
            >
              Get tools →
            </Link>
          </div>
        </div>

        <div class="md:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-4">
          <FeatureCard icon="📌" title="Collaboration pipeline" desc="Track deals, statuses, and follow-ups. Designed for daily use." />
          <FeatureCard icon="📬" title="Sponsor contacts" desc="Store sponsor info, notes, and search across everything fast." />
          <FeatureCard icon="🧰" title="Utility downloads" desc="Small tools that solve specific problems. Clean UI, clear pages." />
          <FeatureCard icon="🗂️" title="Guides & hubs" desc="Topic hubs with internal linking and practical “how-to” structure." />
        </div>
      </section>

      <hr class="my-12 border-slate-200 dark:border-slate-800" />

      <!-- LATEST DOWNLOADS -->
      <section class="rounded-3xl border border-slate-200 bg-white/70 p-6 md:p-8
                      dark:border-slate-800 dark:bg-slate-950/30">
        <div class="flex items-start md:items-center justify-between gap-4">
          <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Latest downloads</h2>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">
              Quick utilities with clear descriptions and a dedicated download confirmation page.
            </p>
          </div>

          <Link
            href="/downloads"
            class="shrink-0 px-4 py-2 rounded-xl text-sm font-semibold border transition
                   bg-white border-slate-300 text-slate-900 hover:bg-slate-100
                   dark:bg-slate-900/30 dark:border-slate-700 dark:text-slate-100 dark:hover:bg-slate-800/60"
          >
            View all →
          </Link>
        </div>

        <div v-if="dlLoading" class="mt-6 text-slate-600 dark:text-slate-300 text-sm">
          Loading latest downloads…
        </div>

        <div v-else-if="!latestDownloads.length" class="mt-6 text-slate-600 dark:text-slate-300 text-sm">
          No downloads yet. Add items in Content Admin → Downloads.
        </div>

        <div v-else class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <Link
            v-for="d in latestDownloads"
            :key="d.id"
            :href="`/downloads/${d.slug}`"
            class="group block rounded-2xl border border-slate-200 bg-white p-4 transition
                   hover:bg-slate-50 hover:-translate-y-[1px]
                   dark:border-slate-800 dark:bg-slate-950/40 dark:hover:bg-slate-900/40"
          >
            <div class="flex items-center justify-between gap-3">
              <div class="text-xs text-slate-500 dark:text-slate-400 truncate">
                {{ (d.category || 'Utility').toString().toUpperCase() }}
              </div>
              <span
                v-if="d.is_featured"
                class="text-[11px] px-2 py-0.5 rounded-full border
                       border-emerald-300 text-emerald-700 bg-emerald-50
                       dark:border-emerald-700 dark:text-emerald-300 dark:bg-emerald-900/20"
              >
                Featured
              </span>
            </div>

            <div class="mt-2 font-semibold text-slate-900 dark:text-white line-clamp-2">
              {{ d.title }}
            </div>

            <div class="mt-2 text-xs text-slate-600 dark:text-slate-300">
              v{{ d.version || '-' }} • {{ d.os || 'Windows' }} • {{ d.download_count ?? 0 }} downloads
            </div>

            <div class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-slate-900 dark:text-slate-100">
              Open page
              <span class="transition group-hover:translate-x-0.5">→</span>
            </div>
          </Link>
        </div>
      </section>

      <hr class="my-12 border-slate-200 dark:border-slate-800" />

      <!-- BLOG HUBS -->
      <section class="rounded-3xl border border-slate-200 bg-white/70 p-6 md:p-8
                      dark:border-slate-800 dark:bg-slate-950/30">
        <div class="flex items-center justify-between gap-4 mb-5">
          <div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Guides & hubs</h2>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">
              Clean hub pages for structured reading, internal links, and SEO.
            </p>
          </div>

          <Link href="/blog" class="text-sm font-semibold text-blue-700 hover:text-blue-600 dark:text-blue-300 dark:hover:text-blue-200">
            Visit Blog →
          </Link>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <HubCard href="/blog/cs2" icon="🎯" title="CS2" desc="FPS, input lag, config, launch options, network tuning." />
          <HubCard href="/blog/pc-optimization" icon="🧰" title="PC Optimization" desc="Windows performance, stability, drivers, clean tuning." />
          <HubCard href="/blog/creator-tools" icon="🎬" title="Creator Tools" desc="Workflow, email templates, editing, uploads, monetization basics." />
        </div>

        <div class="mt-8 rounded-2xl border p-5 bg-slate-50 border-slate-200
                    dark:bg-slate-950/30 dark:border-slate-800">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
              <div class="font-semibold text-slate-900 dark:text-white">
                Start with the demo, then save your workflow.
              </div>
              <div class="mt-1 text-sm text-slate-600 dark:text-slate-300">
                Explore first. Create a free account when you’re ready.
              </div>
            </div>

            <div class="flex gap-2">
              <Link
                v-if="!auth.user"
                href="/demo"
                class="px-5 py-3 rounded-xl font-semibold border transition
                       bg-white border-slate-300 text-slate-900 hover:bg-slate-100
                       dark:bg-slate-900/30 dark:border-slate-700 dark:text-slate-100 dark:hover:bg-slate-800/60"
              >
                Open demo →
              </Link>

              <Link
                v-if="!auth.user"
                href="/register"
                class="px-5 py-3 rounded-xl font-semibold text-white transition
                       bg-emerald-600 hover:bg-emerald-700"
              >
                Create account →
              </Link>

              <Link
                v-else
                href="/dashboard"
                class="px-5 py-3 rounded-xl font-semibold text-white transition
                       bg-emerald-600 hover:bg-emerald-700"
              >
                Go to dashboard →
              </Link>
            </div>
          </div>
        </div>
      </section>
    </div>
  </MainLayout>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import axios from 'axios'
import MainLayout from '@/Layouts/MainLayout.vue'

const page = usePage()
const auth = computed(() => page.props.auth || {})

const latestDownloads = ref([])
const dlLoading = ref(false)

async function fetchLatestDownloads() {
  dlLoading.value = true
  try {
    const res = await axios.get('/partials/latest-downloads')
    latestDownloads.value = res?.data?.items || []
  } catch {
    latestDownloads.value = []
  } finally {
    dlLoading.value = false
  }
}

onMounted(fetchLatestDownloads)

// local tiny components (keep Welcome clean)
const FeatureCard = (props) => null
const HubCard = (props) => null
</script>

<script>
export default {
  components: {
    FeatureCard: {
      props: { icon: String, title: String, desc: String },
      template: `
        <div class="rounded-2xl border p-5 bg-white/70 border-slate-200
                    dark:bg-slate-950/30 dark:border-slate-800">
          <div class="text-3xl">{{ icon }}</div>
          <div class="mt-2 font-bold text-slate-900 dark:text-white">{{ title }}</div>
          <div class="mt-1 text-sm text-slate-600 dark:text-slate-300">{{ desc }}</div>
        </div>
      `,
    },
    HubCard: {
      props: { href: String, icon: String, title: String, desc: String },
      template: `
        <a :href="href"
          class="group rounded-2xl border p-5 transition
                 bg-white border-slate-200 hover:bg-slate-50 hover:-translate-y-[1px]
                 dark:bg-slate-950/40 dark:border-slate-800 dark:hover:bg-slate-900/40">
          <div class="text-2xl">{{ icon }}</div>
          <div class="mt-2 font-semibold text-slate-900 dark:text-white">{{ title }}</div>
          <div class="mt-1 text-sm text-slate-600 dark:text-slate-300">{{ desc }}</div>
          <div class="mt-4 text-sm font-semibold text-slate-900 dark:text-slate-100">
            Open hub <span class="transition group-hover:translate-x-0.5 inline-block">→</span>
          </div>
        </a>
      `,
    },
  },
}
</script>