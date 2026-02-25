<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'

defineProps({
  stats: Object,
  last7: Array,
  topOverall: Array,
  top7days: Array,
  byCategory: Array,
  topIps: Array,
topAgents: Array,
guestUserLast7: Array,
suspiciousAgents: Array,
suspiciousIps24h: Array,
topByCategory: Object,
topByOs: Array,
peakDay: Object,
avgPerDay: Number,
trend30: Array,



})

const maxLast7 = (arr) => Math.max(1, ...arr.map(x => x.count))
</script>

<template>
  <MainLayout>
    <div class="max-w-6xl mx-auto px-4 py-8">
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-white">Download Analytics</h1>
        <p class="text-gray-400 mt-2">Superadmin view — internal stats</p>
      </div>

      <!-- Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-5">
          <div class="text-gray-400 text-sm">Total downloads (counter)</div>
          <div class="text-white text-3xl font-bold mt-2">{{ stats.totalDownloadsCounter }}</div>
          <div class="text-gray-500 text-xs mt-2">Fast value from items.download_count</div>
        </div>

        <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-5">
          <div class="text-gray-400 text-sm">Total log entries</div>
          <div class="text-white text-3xl font-bold mt-2">{{ stats.totalLogs }}</div>
          <div class="text-gray-500 text-xs mt-2">Every download attempt logged</div>
        </div>

        <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-5">
          <div class="text-gray-400 text-sm">Unique users / IPs</div>
          <div class="text-white text-3xl font-bold mt-2">
            {{ stats.uniqueUsers }} / {{ stats.uniqueIps }}
          </div>
          <div class="text-gray-500 text-xs mt-2">Distinct user_id and ip_address</div>
        </div>

        <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-5">
          <div class="text-gray-400 text-sm">Guest vs User downloads</div>
          <div class="text-white text-2xl font-bold mt-2">
            {{ stats.guestDownloads }} / {{ stats.userDownloads }}
          </div>
          <div class="text-gray-500 text-xs mt-2">download_logs.is_guest</div>
        </div>

        <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-5">
          <div class="text-gray-400 text-sm">Items</div>
          <div class="text-white text-2xl font-bold mt-2">
            {{ stats.publishedItems }} / {{ stats.totalItems }}
          </div>
          <div class="text-gray-500 text-xs mt-2">Published / total</div>
        </div>

        <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-5">
          <a
            :href="route('contentAdmin.downloads.index')"
            class="inline-flex items-center justify-center w-full px-4 py-3 rounded-lg font-medium text-white
                   bg-gradient-to-r from-blue-700 to-slate-800 hover:from-blue-600 hover:to-slate-700"
          >
            Back to Downloads
          </a>
          <div class="text-gray-500 text-xs mt-3">Manage items, edit, delete</div>
        </div>
      </div>

      <!-- Last 7 days -->
      <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-6 mb-8">
        <h2 class="text-white font-semibold text-lg mb-4">Last 7 days</h2>

        <div class="space-y-2">
          <div v-for="d in last7" :key="d.day" class="flex items-center gap-3">
            <div class="text-gray-400 text-sm w-28">{{ d.day }}</div>
            <div class="flex-1 bg-slate-900 rounded-lg h-3 overflow-hidden border border-slate-700">
              <div
                class="h-3 bg-blue-700"
                :style="{ width: (d.count / maxLast7(last7) * 100) + '%' }"
              />
            </div>
            <div class="text-gray-300 text-sm w-12 text-right">{{ d.count }}</div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top overall -->
        <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-6">
          <h2 class="text-white font-semibold text-lg mb-4">Top 10 (overall)</h2>
          <div class="space-y-3">
            <div v-for="(it, i) in topOverall" :key="it.id" class="flex items-center justify-between">
              <div class="min-w-0">
                <div class="text-gray-200 font-medium truncate">
                  {{ i+1 }}. {{ it.title }}
                </div>
                <div class="text-gray-500 text-xs">{{ it.category }} • {{ it.slug }}</div>
              </div>
              <div class="text-gray-300 font-semibold">{{ it.download_count }}</div>
            </div>
          </div>
        </div>
<!-- Top Items po kategoriji -->
        <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-6 mt-8">
  <h2 class="text-white font-semibold text-lg mb-4">Top items by category</h2>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div v-for="cat in ['gaming','tools','apps']" :key="cat" class="rounded-xl border border-slate-700 bg-slate-900/40 p-4">
      <div class="text-gray-400 text-xs uppercase mb-3">{{ cat }}</div>

      <div v-if="topByCategory?.[cat]?.length" class="space-y-2">
        <div v-for="(it, i) in topByCategory[cat]" :key="it.id" class="flex items-center justify-between">
          <div class="text-gray-200 text-sm truncate">{{ i+1 }}. {{ it.title }}</div>
          <div class="text-gray-300 font-semibold">{{ it.download_count }}</div>
        </div>
      </div>

      <div v-else class="text-gray-500 text-sm">No items yet.</div>
    </div>
  </div>
</div>


        <!-- Top 7 days -->
        <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-6">
          <h2 class="text-white font-semibold text-lg mb-4">Top 10 (last 7 days)</h2>
          <div class="space-y-3">
            <div v-for="(it, i) in top7days" :key="it.item_id" class="flex items-center justify-between">
              <div class="min-w-0">
                <div class="text-gray-200 font-medium truncate">
                  {{ i+1 }}. {{ it.title }}
                </div>
                <div class="text-gray-500 text-xs">
                  {{ it.category || '-' }} • {{ it.slug || 'deleted' }}
                </div>
              </div>
              <div class="text-gray-300 font-semibold">{{ it.count }}</div>
            </div>
          </div>
        </div>

        <!-- Category breakdown -->
        <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-6 lg:col-span-2">
          <h2 class="text-white font-semibold text-lg mb-4">Category breakdown (overall)</h2>
          <div class="flex flex-wrap gap-3">
            <div
              v-for="c in byCategory"
              :key="c.category"
              class="px-4 py-3 rounded-xl border border-slate-700 bg-slate-900/40"
            >
              <div class="text-gray-400 text-xs uppercase">{{ c.category }}</div>
              <div class="text-white text-xl font-bold mt-1">{{ c.count }}</div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
  <!-- Top IPs -->
  <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-6">
    <h2 class="text-white font-semibold text-lg mb-4">Top IPs</h2>
    <div v-if="topIps?.length" class="space-y-3">
      <div v-for="(r, i) in topIps" :key="r.ip" class="flex items-center justify-between">
        <div class="text-gray-200 truncate">
          {{ i+1 }}. {{ r.ip }}
        </div>
        <div class="text-gray-300 font-semibold">{{ r.count }}</div>
      </div>
    </div>
    <div v-else class="text-gray-400">No data yet.</div>
  </div>

  <!-- Top User Agents -->
  <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-6">
    <h2 class="text-white font-semibold text-lg mb-4">Top User Agents</h2>
    <div v-if="topAgents?.length" class="space-y-3">
      <div v-for="(r, i) in topAgents" :key="i" class="flex items-center justify-between gap-4">
        <div class="text-gray-200 text-sm truncate flex-1">
          {{ i+1 }}. {{ r.ua }}
        </div>
        <div class="text-gray-300 font-semibold">{{ r.count }}</div>
      </div>
    </div>
    <div v-else class="text-gray-400">No data yet.</div>
  </div>

  <!-- Guests vs Users last 7 days -->
  <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-6 lg:col-span-2">
    <h2 class="text-white font-semibold text-lg mb-4">Guests vs Users (last 7 days)</h2>

    <div v-if="guestUserLast7?.length" class="space-y-2">
      <div v-for="d in guestUserLast7" :key="d.day" class="flex items-center justify-between">
        <div class="text-gray-400 text-sm w-28">{{ d.day }}</div>
        <div class="text-gray-300 text-sm">
          Guests: <span class="font-semibold">{{ d.guests }}</span>
          <span class="mx-2 text-gray-600">|</span>
          Users: <span class="font-semibold">{{ d.users }}</span>
        </div>
      </div>
    </div>

    <div v-else class="text-gray-400">No data yet.</div>
  </div>
</div>
<!-- Suspicios Bots -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
  <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-6">
    <h2 class="text-white font-semibold text-lg mb-4">Suspicious User Agents</h2>
    <div v-if="suspiciousAgents?.length" class="space-y-3">
      <div v-for="(r, i) in suspiciousAgents" :key="i" class="flex items-center justify-between gap-4">
        <div class="text-gray-200 text-sm truncate flex-1">{{ i+1 }}. {{ r.ua }}</div>
        <div class="text-gray-300 font-semibold">{{ r.count }}</div>
      </div>
    </div>
    <div v-else class="text-gray-400">No suspicious agents detected.</div>
  </div>

  <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-6">
    <h2 class="text-white font-semibold text-lg mb-4">High-volume IPs (24h)</h2>
    <div class="text-xs text-gray-500 mb-3">IPs with ≥ 20 downloads in the last 24 hours</div>
    <div v-if="suspiciousIps24h?.length" class="space-y-3">
      <div v-for="(r, i) in suspiciousIps24h" :key="r.ip" class="flex items-center justify-between">
        <div class="text-gray-200 truncate">{{ i+1 }}. {{ r.ip }}</div>
        <div class="text-gray-300 font-semibold">{{ r.count }}</div>
      </div>
    </div>
    <div v-else class="text-gray-400">No high-volume IPs in last 24h.</div>
  </div>
</div>
<!-- Top OS -->
<div class="bg-slate-800/50 border border-slate-700 rounded-xl p-6 mt-8">
  <h2 class="text-white font-semibold text-lg mb-4">OS breakdown (overall)</h2>

  <div class="flex flex-wrap gap-3">
    <div v-for="r in topByOs" :key="r.os" class="px-4 py-3 rounded-xl border border-slate-700 bg-slate-900/40">
      <div class="text-gray-400 text-xs uppercase">{{ r.os }}</div>
      <div class="text-white text-xl font-bold mt-1">{{ r.count }}</div>
    </div>
  </div>
</div>
<!-- Peek Day -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
  <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-5">
    <div class="text-gray-400 text-sm">Avg / day (30d)</div>
    <div class="text-white text-3xl font-bold mt-2">{{ avgPerDay }}</div>
  </div>

  <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-5">
    <div class="text-gray-400 text-sm">Peak day (30d)</div>
    <div class="text-white text-lg font-bold mt-2">
      {{ peakDay?.day || '-' }} — {{ peakDay?.count || 0 }}
    </div>
  </div>

  <div class="bg-slate-800/50 border border-slate-700 rounded-xl p-5">
    <div class="text-gray-400 text-sm">Trend points</div>
    <div class="text-white text-3xl font-bold mt-2">{{ trend30?.length || 0 }}</div>
  </div>
</div>

      </div>

    </div>
  </MainLayout>
</template>
