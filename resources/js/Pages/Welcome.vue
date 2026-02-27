<!-- resources/js/Pages/Welcome.vue -->
<template>
  <MainLayout>
    <div class="max-w-7xl mx-auto px-4 py-10 md:py-14">
      <!-- HERO -->
      <section class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white/70 p-6 md:p-10
                      dark:border-slate-800 dark:bg-slate-950/30">
        <!-- subtle glow -->
        

        <div class="relative">
          
          <!-- HERO CARDS (collapsed -> expand) -->
<div class="mt-1 grid grid-cols-1 gap-4">

  <!-- CARD 1: CS2 (RED/BLACK - high contrast) -->
<div
  class="group relative rounded-3xl overflow-hidden border
         border-slate-200 dark:border-slate-800
         shadow-[0_12px_40px_-26px_rgba(0,0,0,0.55)]"
>
  <!-- background (hard contrast gradient) -->
  <div class="absolute inset-0 bg-gradient-to-br from-black via-[#160409] to-[#3b0a12]"></div>

  <!-- top strip (tag) -->
  <div class="absolute inset-x-0 top-0 h-14 bg-gradient-to-r from-[#ff1f4b]/20 via-transparent to-transparent"></div>

  <!-- HEADER -->
  <button
    type="button"
    class="relative w-full text-left p-5 md:p-6 flex items-start justify-between gap-4"
    @click="openId = (openId === 'cs2' ? '' : 'cs2')"
  >
    <div class="min-w-0 flex gap-4">
      <!-- CS2 ICON / THUMB (left) -->
      <div
  class="shrink-0 h-28 w-28 md:h-24 md:w-24 rounded-2xl border
         border-white/10 bg-gradient-to-br from-[#ff2a55]/25 to-black
         shadow-[0_18px_40px_-25px_rgba(255,42,85,0.9)]
         flex items-center justify-center"
>
  <img src="/images/cs2.png"
       alt="CS2"
       class="h-26 w-26 opacity-100
              drop-shadow-[0_18px_40px_rgba(255,42,85,0.6)]"
                        onerror="this.style.display='none'"
 />
</div>
      <div class="min-w-0">
        <!-- TAG ROW (bold + gradient text feel) -->
        <div class="inline-flex items-center gap-2 text-[11px] font-extrabold tracking-wide">
          <span class="text-white">NOREVIA</span>
          <span class="text-white/45">•</span>
          <span class="text-white/70">CS2 • PERFORMANCE • MODEL</span>
        </div>

        <!-- TITLE (glows when collapsed; bigger when expanded) -->
        <div
          class="mt-1 font-black tracking-tight
                 text-2xl md:text-3xl"
          :class="openId === 'cs2'
            ? 'text-white drop-shadow-[0_6px_18px_rgba(255,42,85,0.40)]'
            : 'text-white drop-shadow-[0_10px_30px_rgba(255,42,85,0.55)]'"
        >
          MODEL: CS2 Performance System
        </div>

        <!-- helper line: shown only when CLOSED -->
        <div v-if="openId !== 'cs2'" class="mt-2 text-sm text-white/70">
          Tap to expand <span class="text-white font-semibold">→</span>
        </div>
      </div>
    </div>

    <!-- chevron -->
    <span
      class="shrink-0 inline-flex items-center justify-center h-10 w-10 rounded-2xl border
             border-white/10 bg-black/40 text-white transition
             shadow-[0_12px_30px_-22px_rgba(0,0,0,0.9)]"
      :class="openId === 'cs2' ? 'rotate-180' : 'group-hover:translate-y-[-1px]'"
    >
      ▼
    </span>
  </button>

  <!-- EXPANDED -->
   <Transition
  enter-active-class="transition duration-250 ease-out"
  enter-from-class="opacity-0 -translate-y-1 scale-[0.99]"
  enter-to-class="opacity-100 translate-y-0 scale-100"
  leave-active-class="transition duration-200 ease-in"
  leave-from-class="opacity-100 translate-y-0 scale-100"
  leave-to-class="opacity-0 -translate-y-1 scale-[0.99]"
>
  <div v-if="openId === 'cs2'" class="relative px-5 md:px-6 pb-6">
    <!-- enterprise content frame (clean separation, no mixed colors) -->
    <div
      class="rounded-2xl border border-white/10 bg-black/55 backdrop-blur
             shadow-[0_20px_60px_-35px_rgba(0,0,0,0.85)]
             overflow-hidden"
    >
      <!-- top divider bar -->
      <div class="h-1.5 bg-gradient-to-r from-[#ff2a55] via-[#ff2a55]/40 to-transparent"></div>

      <div class="p-5 md:p-6 grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
        <!-- LEFT: copy + links -->
        <div class="md:col-span-7">
          <div class="text-sm md:text-base text-white/80 leading-relaxed">
            A structured performance model for CS2: consistency-first tuning (FPS stability, latency, input feel).
            Follow the guide like a checklist.
          </div>

          <div class="mt-5 flex flex-wrap gap-2">
            <Link
  href="/blog/cs2/cs2-performance-system"
  class="group relative px-5 py-3 rounded-xl text-sm md:text-base
         font-extrabold text-white transition-all duration-200
         bg-[#ff2a55]
         shadow-[0_18px_45px_-28px_rgba(255,42,85,0.95)]
         hover:shadow-[0_28px_70px_-35px_rgba(255,42,85,1)]
         hover:-translate-y-[2px]
         hover:scale-[1.02]
         active:scale-[0.99]
         focus:outline-none focus:ring-2 focus:ring-[#ff2a55]/40
         backdrop-blur-sm
         hover:bg-[#8b1c2f]
          hover:shadow-[0_25px_70px_-35px_rgba(139,28,47,0.95)]
          transition-all duration-200"
>
  <span class="relative z-10">Open system model →</span>

  <!-- subtle inner glow -->
  <span
    class="absolute inset-0 rounded-xl opacity-0
           bg-gradient-to-r from-white/10 via-white/5 to-transparent
           transition-opacity duration-200
           group-hover:opacity-100"
  ></span>
</Link>

            <Link
              href="/blog/cs2"
              class="px-5 py-3 rounded-xl text-sm md:text-base font-bold border transition
                     border-white/15 text-white/90 bg-white/5 hover:bg-white/10"
            >
              CS2 hub →
            </Link>
          </div>
        </div>

        <!-- RIGHT: clickable “thumb” / card that links to model -->
        <div class="md:col-span-5">
          <Link
  href="/blog/cs2/cs2-performance-system"
  class="group/thumb relative block rounded-2xl border overflow-hidden
         border-white/10
         bg-gradient-to-br from-[#2b060d] via-black to-black
         shadow-[0_22px_55px_-36px_rgba(0,0,0,0.95)]
         transition-all duration-250
         hover:-translate-y-[3px]
         hover:shadow-[0_35px_90px_-45px_rgba(255,42,85,0.55)]
         backdrop-blur-sm"
>
  <!-- hover glow overlay -->
  <div
    class="absolute inset-0 opacity-0 transition-opacity duration-300
           bg-gradient-to-br from-[#ff2a55]/15 via-transparent to-transparent
           group-hover/thumb:opacity-100"
  ></div>

  <div class="relative p-5">
    <div class="text-[11px] font-extrabold tracking-wide text-white/75">
      CS2 PERFORMANCE
    </div>

    <div
      class="mt-1 text-xl md:text-2xl font-black text-white
             drop-shadow-[0_10px_30px_rgba(255,42,85,0.35)]
             transition-all duration-200
             group-hover/thumb:drop-shadow-[0_18px_45px_rgba(255,42,85,0.55)]"
    >
      System Model
    </div>

    <div class="mt-2 text-sm text-white/70">
      Latency • Stability • FPS consistency
    </div>

    <div class="mt-5 inline-flex items-center gap-2 text-sm font-extrabold text-white">
      Open →
      <span class="transition group-hover/thumb:translate-x-1">→</span>
    </div>
  </div>

  <!-- accent line (animira se na hover) -->
  <div
    class="h-1 bg-[#ff2a55] transition-all duration-300
           group-hover/thumb:h-1.5"
  ></div>
</Link>
        </div>
      </div>
    </div>
  </div>
  </Transition>
</div>

  <!-- CARD 2: MODEL (GREEN) — existing gradient, don't touch style too much -->
  <div
  class="group relative rounded-3xl overflow-hidden border
         border-slate-200 dark:border-slate-800
         shadow-[0_12px_40px_-26px_rgba(0,0,0,0.55)]"
>
  <!-- background -->
  <div class="absolute inset-0 bg-gradient-to-br from-black via-[#04160f] to-[#0a2f1e]"></div>

  <!-- top strip -->
  <div class="absolute inset-x-0 top-0 h-14 bg-gradient-to-r from-emerald-400/20 via-transparent to-transparent"></div>

  <!-- HEADER -->
  <button
    type="button"
    class="relative w-full text-left p-5 md:p-6 flex items-start justify-between gap-4"
    @click="openId = (openId === 'model' ? '' : 'model')"
  >
    <div class="min-w-0 flex gap-4">
      <!-- ICON / THUMB -->
      <div
        class="shrink-0 h-28 w-28 rounded-2xl border
               border-white/10 bg-gradient-to-br from-emerald-400/20 to-black
               shadow-[0_18px_40px_-25px_rgba(16,185,129,0.75)]
               flex items-center justify-center"
      >
        <!-- ako imaš sliku, zamijeni src -->
        <img
          src="/images/creator.png"
          alt="Creator"
          class="h-26 w-26 opacity-100
                 drop-shadow-[0_18px_40px_rgba(16,185,129,0.45)]"
          onerror="this.style.display='none'"
        />
        <!-- fallback ako nema slike -->
        <!-- <span class="text-white font-black tracking-tight text-xl"></span> -->
      </div>

      <div class="min-w-0">
        <!-- TAG ROW -->
        <div class="inline-flex items-center gap-2 text-[11px] font-extrabold tracking-wide">
          <span class="text-white">NOREVIA</span>
          <span class="text-white/45">•</span>
          <span class="text-white/70">YOUTUBERS • STREAMERS • CREATORS</span>
        </div>

        <!-- TITLE -->
        <div
          class="mt-1 font-black tracking-tight text-2xl md:text-3xl"
          :class="openId === 'model'
            ? 'text-white drop-shadow-[0_6px_18px_rgba(16,185,129,0.35)]'
            : 'text-white drop-shadow-[0_10px_30px_rgba(16,185,129,0.50)]'"
        >
          Creator Collaboration App
        </div>

        <!-- helper (only closed) -->
        <div v-if="openId !== 'model'" class="mt-2 text-sm text-white/70">
          Tap to expand <span class="text-white font-semibold">→</span>
        </div>
      </div>
    </div>

    <!-- chevron -->
    <span
      class="shrink-0 inline-flex items-center justify-center h-10 w-10 rounded-2xl border
             border-white/10 bg-black/40 text-white transition
             shadow-[0_12px_30px_-22px_rgba(0,0,0,0.9)]"
      :class="openId === 'model' ? 'rotate-180' : 'group-hover:translate-y-[-1px]'"
    >
      ▼
    </span>
  </button>
    <Transition
  enter-active-class="transition duration-250 ease-out"
  enter-from-class="opacity-0 -translate-y-1 scale-[0.99]"
  enter-to-class="opacity-100 translate-y-0 scale-100"
  leave-active-class="transition duration-200 ease-in"
  leave-from-class="opacity-100 translate-y-0 scale-100"
  leave-to-class="opacity-0 -translate-y-1 scale-[0.99]"
>
  <div v-if="openId === 'model'" class="relative px-5 md:px-6 pb-6">
    <!-- enterprise content frame -->
    <div
      class="rounded-2xl border border-slate-200 bg-white/80 backdrop-blur
             shadow-[0_20px_60px_-35px_rgba(0,0,0,0.35)]
             dark:border-emerald-500/20 dark:bg-slate-950/35
             dark:shadow-[0_20px_60px_-35px_rgba(0,0,0,0.85)]
             overflow-hidden"
    >
      <!-- top divider bar -->
      <div class="h-1.5 bg-gradient-to-r from-emerald-500 via-emerald-500/40 to-transparent"></div>

      <div class="p-5 md:p-6 grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
        <!-- LEFT -->
        <div class="md:col-span-7">
          <div class="text-sm md:text-base text-slate-700 leading-relaxed
                      dark:text-slate-200/85">
            Track sponsorships & collaborations, download small utilities, and read practical guides that link directly to tools.
          </div>

          <div class="mt-5 flex flex-wrap gap-2">
            <!-- PRIMARY BUTTON (emerald -> subtle “toward thumb”) -->
            <Link
              href="/dashboard"
              class="group relative px-5 py-3 rounded-xl text-sm md:text-base
                     font-extrabold text-white transition-all duration-200
                     bg-emerald-600 hover:bg-emerald-700
                     shadow-[0_18px_45px_-28px_rgba(16,185,129,0.85)]
                     hover:shadow-[0_26px_70px_-38px_rgba(0,0,0,0.45)]
                     hover:-translate-y-[2px]
                     hover:scale-[1.02]
                     active:scale-[0.99]
                     focus:outline-none focus:ring-2 focus:ring-emerald-400/35"
            >
              <span class="relative z-10">Open dashboard →</span>
              <span
                class="absolute inset-0 rounded-xl opacity-0
                       bg-gradient-to-r from-white/12 via-white/6 to-transparent
                       transition-opacity duration-200
                       group-hover:opacity-100"
              ></span>
            </Link>

            <!-- SECONDARY -->
            <Link
              href="/downloads"
              class="px-5 py-3 rounded-xl text-sm md:text-base font-bold border transition
                     border-slate-300 text-slate-900 bg-white hover:bg-slate-100
                     dark:border-slate-700 dark:text-slate-100 dark:bg-white/5 dark:hover:bg-white/10"
            >
              Browse downloads →
            </Link>
          </div>
        </div>

        <!-- RIGHT THUMB (pulls slightly toward button) -->
        <div class="md:col-span-5">
          <Link
            href="/demo"
            class="group/thumb relative block rounded-2xl border overflow-hidden
                   border-slate-200 bg-white/70
                   shadow-[0_22px_55px_-40px_rgba(0,0,0,0.35)]
                   transition-all duration-250
                   hover:-translate-y-[3px]
                   hover:shadow-[0_35px_90px_-55px_rgba(16,185,129,0.35)]
                   dark:border-white/10 dark:bg-slate-950/35
                   dark:shadow-[0_22px_55px_-36px_rgba(0,0,0,0.95)]
                   backdrop-blur-sm"
          >
            <!-- hover glow overlay -->
            <div
              class="absolute inset-0 opacity-0 transition-opacity duration-300
                     bg-gradient-to-br from-emerald-500/12 via-transparent to-transparent
                     group-hover/thumb:opacity-100"
            ></div>

            <div class="relative p-5">
              <div class="text-[11px] font-extrabold tracking-wide text-slate-500 dark:text-slate-200/70">
                CREATOR WORKFLOW
              </div>

              <div
                class="mt-1 text-xl md:text-2xl font-black text-slate-900 dark:text-white
                       transition-all duration-200
                       group-hover/thumb:drop-shadow-[0_18px_45px_rgba(16,185,129,0.35)]"
              >
                Demo & setup
              </div>

              <div class="mt-2 text-sm text-slate-600 dark:text-slate-300">
                Explore first. Create a free account when you’re ready.
              </div>

              <div class="mt-5 inline-flex items-center gap-2 text-sm font-extrabold text-slate-900 dark:text-white">
                Open →
                <span class="transition group-hover/thumb:translate-x-1">→</span>
              </div>
            </div>

            <!-- accent line -->
            <div class="h-1 bg-emerald-600 transition-all duration-300 group-hover/thumb:h-1.5"></div>
          </Link>
        </div>
      </div>
    </div>
  </div>
</Transition>
  </div>

  <!-- CARD 3: TOOLS (BLUE/BLACK - high contrast header) -->
<div
  class="group relative rounded-3xl overflow-hidden border
         border-slate-200 dark:border-slate-800
         shadow-[0_12px_40px_-26px_rgba(0,0,0,0.55)]"
>
  <!-- background -->
  <div class="absolute inset-0 bg-gradient-to-br from-black via-[#061225] to-[#0b2a52]"></div>

  <!-- top strip -->
  <div class="absolute inset-x-0 top-0 h-14 bg-gradient-to-r from-sky-400/20 via-transparent to-transparent"></div>

  <!-- HEADER -->
  <button
    type="button"
    class="relative w-full text-left p-5 md:p-6 flex items-start justify-between gap-4"
    @click="openId = (openId === 'opt' ? '' : 'opt')"
  >
    <div class="min-w-0 flex gap-4">
      <!-- ICON / THUMB -->
      <div
        class="shrink-0 h-28 w-28 rounded-2xl border
               border-white/10 bg-gradient-to-br from-sky-400/20 to-black
               shadow-[0_18px_40px_-25px_rgba(56,189,248,0.75)]
               flex items-center justify-center"
      >
        <img
          src="/images/tools.png"
          alt="Tools"
          class="h-26 w-26 opacity-100
                 drop-shadow-[0_18px_40px_rgba(56,189,248,0.45)]"
          onerror="this.style.display='none'"
        />
      </div>

      <div class="min-w-0">
        <!-- TAG ROW -->
        <div class="inline-flex items-center gap-2 text-[11px] font-extrabold tracking-wide">
          <span class="text-white">NOREVIA</span>
          <span class="text-white/45">•</span>
          <span class="text-white/70">FREE TOOLS • ORGANIZE • OPTIMIZE</span>
        </div>

        <!-- TITLE -->
        <div
          class="mt-1 font-black tracking-tight text-2xl md:text-3xl"
          :class="openId === 'opt'
            ? 'text-white drop-shadow-[0_6px_18px_rgba(56,189,248,0.35)]'
            : 'text-white drop-shadow-[0_10px_30px_rgba(56,189,248,0.50)]'"
        >
          Free Optimization Tools
        </div>

        <!-- helper (only closed) -->
        <div v-if="openId !== 'opt'" class="mt-2 text-sm text-white/70">
          Tap to expand <span class="text-white font-semibold">→</span>
        </div>
      </div>
    </div>

    <!-- chevron -->
    <span
      class="shrink-0 inline-flex items-center justify-center h-10 w-10 rounded-2xl border
             border-white/10 bg-black/40 text-white transition
             shadow-[0_12px_30px_-22px_rgba(0,0,0,0.9)]"
      :class="openId === 'opt' ? 'rotate-180' : 'group-hover:translate-y-[-1px]'"
    >
      ▼
    </span>
  </button>
    <Transition
  enter-active-class="transition duration-250 ease-out"
  enter-from-class="opacity-0 -translate-y-1 scale-[0.99]"
  enter-to-class="opacity-100 translate-y-0 scale-100"
  leave-active-class="transition duration-200 ease-in"
  leave-from-class="opacity-100 translate-y-0 scale-100"
  leave-to-class="opacity-0 -translate-y-1 scale-[0.99]"
>
  <div v-if="openId === 'opt'" class="relative px-5 md:px-6 pb-6">
    <div
      class="rounded-2xl border border-slate-200 bg-white/80 backdrop-blur
             shadow-[0_20px_60px_-35px_rgba(0,0,0,0.35)]
             dark:border-sky-500/20 dark:bg-slate-950/35
             dark:shadow-[0_20px_60px_-35px_rgba(0,0,0,0.85)]
             overflow-hidden"
    >
      <div class="h-1.5 bg-gradient-to-r from-sky-500 via-sky-500/40 to-transparent"></div>

      <div class="p-5 md:p-6 grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
        <!-- LEFT -->
        <div class="md:col-span-7">
          <div class="text-sm md:text-base text-slate-700 leading-relaxed
                      dark:text-slate-200/85">
            Three free utilities for fast performance wins: network checks, startup trimming, and cleanup.
          </div>

          <div class="mt-5 flex flex-wrap gap-2">
            <!-- PRIMARY (blue -> subtle “toward thumb”) -->
            <Link
              href="/downloads"
              class="group relative px-5 py-3 rounded-xl text-sm md:text-base
                     font-extrabold text-white transition-all duration-200
                     bg-sky-600 hover:bg-sky-700
                     shadow-[0_18px_45px_-28px_rgba(14,165,233,0.85)]
                     hover:shadow-[0_26px_70px_-38px_rgba(0,0,0,0.45)]
                     hover:-translate-y-[2px]
                     hover:scale-[1.02]
                     active:scale-[0.99]
                     focus:outline-none focus:ring-2 focus:ring-sky-400/35"
            >
              <span class="relative z-10">View all tools →</span>
              <span
                class="absolute inset-0 rounded-xl opacity-0
                       bg-gradient-to-r from-white/12 via-white/6 to-transparent
                       transition-opacity duration-200
                       group-hover:opacity-100"
              ></span>
            </Link>

            <!-- SECONDARY -->
            <Link
              href="/blog/pc-optimization"
              class="px-5 py-3 rounded-xl text-sm md:text-base font-bold border transition
                     border-slate-300 text-slate-900 bg-white hover:bg-slate-100
                     dark:border-slate-700 dark:text-slate-100 dark:bg-white/5 dark:hover:bg-white/10"
            >
              Optimization guides →
            </Link>
          </div>
        </div>

        <!-- RIGHT: tools cards -->
        <div class="md:col-span-5">
          <div class="grid grid-cols-1 gap-3">
            <Link
              href="/downloads/norevia-ping-tool"
              class="group/tile rounded-2xl border p-4 transition-all duration-200
                     border-slate-200 bg-white/70 hover:bg-white
                     hover:-translate-y-[2px]
                     hover:shadow-[0_28px_70px_-45px_rgba(14,165,233,0.35)]
                     dark:border-white/10 dark:bg-slate-950/30 dark:hover:bg-slate-950/45"
            >
              <div class="text-sm font-extrabold text-slate-900 dark:text-white">Ping Tool</div>
              <div class="mt-1 text-xs text-slate-600 dark:text-slate-300">Latency & packet checks.</div>
              <div class="mt-3 text-sm font-extrabold text-slate-900 dark:text-white">
                Open <span class="inline-block transition group-hover/tile:translate-x-1">→</span>
              </div>
              <div class="mt-3 h-1 rounded-full bg-sky-500/70"></div>
            </Link>

            <Link
              href="/downloads/norevia-startup-manager-lite"
              class="group/tile rounded-2xl border p-4 transition-all duration-200
                     border-slate-200 bg-white/70 hover:bg-white
                     hover:-translate-y-[2px]
                     hover:shadow-[0_28px_70px_-45px_rgba(14,165,233,0.35)]
                     dark:border-white/10 dark:bg-slate-950/30 dark:hover:bg-slate-950/45"
            >
              <div class="text-sm font-extrabold text-slate-900 dark:text-white">Startup Manager Lite</div>
              <div class="mt-1 text-xs text-slate-600 dark:text-slate-300">Trim background apps.</div>
              <div class="mt-3 text-sm font-extrabold text-slate-900 dark:text-white">
                Open <span class="inline-block transition group-hover/tile:translate-x-1">→</span>
              </div>
              <div class="mt-3 h-1 rounded-full bg-sky-500/70"></div>
            </Link>

            <Link
              href="/downloads/windows-clean-booster-light"
              class="group/tile rounded-2xl border p-4 transition-all duration-200
                     border-slate-200 bg-white/70 hover:bg-white
                     hover:-translate-y-[2px]
                     hover:shadow-[0_28px_70px_-45px_rgba(14,165,233,0.35)]
                     dark:border-white/10 dark:bg-slate-950/30 dark:hover:bg-slate-950/45"
            >
              <div class="text-sm font-extrabold text-slate-900 dark:text-white">Windows Clean Booster</div>
              <div class="mt-1 text-xs text-slate-600 dark:text-slate-300">Clean temp & junk.</div>
              <div class="mt-3 text-sm font-extrabold text-slate-900 dark:text-white">
                Open <span class="inline-block transition group-hover/tile:translate-x-1">→</span>
              </div>
              <div class="mt-3 h-1 rounded-full bg-sky-500/70"></div>
            </Link>
          </div>
        </div>
      </div>
    </div>
  </div>
</Transition>
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
const openId = ref('')

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