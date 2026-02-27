<template>
  <MainLayout>
      <div class="flex items-center gap-6">
<div class="p-2 rounded-2xl 
            inline-block">
  <img src="/images/creator.png" class="h-24 w-auto" />
</div>
  <!-- ICON BLOCK -->
  

  <!-- TEXT BLOCK -->
  <div>
    <h1 class="text-3xl md:text-4xl font-bold ui-page-title mb-1">
      Dashboard
    </h1>

    <p class="text-lg md:text-xl font-semibold text-slate-600 dark:text-slate-300">
      Welcome back, {{ $page.props.auth.user.name }}!
    </p>
  </div>

</div>
 

<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
  <button
    @click="showAll"
    class="text-left ui-card ui-card-hover p-6"
  >
    <div class="text-3xl font-bold text-purple-400">{{ totalCollaborations }}</div>
    <div class="ui-muted">Total Collaborations</div>
  </button>

  <button
    @click="showActive"
    class="text-left ui-card ui-card-hover p-6"
  >
    <div class="text-3xl font-bold text-green-400">{{ activeCount }}</div>
    <div class="ui-muted">Active</div>
  </button>

  <button
    @click="showToday"
    class="text-left ui-card ui-card-hover p-6"
  >
    <div class="text-3xl font-bold text-blue-400">{{ followUpsTodayCount }}</div>
    <div class="ui-muted">Follow-ups Today</div>
  </button>

  <button
    @click="showOverdue"
    class="text-left ui-card ui-card-hover p-6"
  >
    <div class="text-3xl font-bold text-red-400">{{ overdueCount }}</div>
    <div class="ui-muted">Overdue</div>
  </button>
</div>


      <!-- Quick Actions - KORISTIMO OBIČNE <a> TAGOVE -->
       <div class="ui-card p-6">
        <!-- Ispisujemo Overdue i need attention -->
        <div class="ui-card p-6">
  <!-- Reminders summary -->
  <div
    v-if="$page.props.reminders && ($page.props.reminders.overdueCount > 0 || $page.props.reminders.todayCount > 0)"
    class="mb-6 rounded-2xl border border-slate-200 bg-white p-4
           shadow-sm
           dark:border-slate-700 dark:bg-slate-800/40 dark:shadow-none"
  >
    <div class="flex flex-wrap items-center justify-between gap-3">
      <div>
        <div class="font-semibold text-slate-900 dark:text-white">
          <span class="text-lg md:text-xl font-bold text-rose-700 dark:text-rose-300">
            Reminders & Follow-ups
          </span>
        </div>

        <div class="mt-1 text-sm text-slate-600 dark:text-slate-300">
          <span v-if="$page.props.reminders.todayCount">
            Today:
            <span class="font-semibold text-sky-700 dark:text-sky-200">
              {{ $page.props.reminders.todayCount }}
            </span>
          </span>

          <span v-if="$page.props.reminders.todayCount && $page.props.reminders.overdueCount"> • </span>

          <span v-if="$page.props.reminders.overdueCount">
            Needs attention:
            <span class="font-semibold text-rose-700 dark:text-rose-200">
              {{ $page.props.reminders.overdueCount }}
            </span>
          </span>
        </div>
      </div>

      <button
        v-if="$page.props.reminders.overdueCount"
        type="button"
        @click="showOverdue();"
        class="px-4 py-2 rounded-lg text-sm font-semibold border transition
               bg-rose-50 border-rose-200 text-rose-800 hover:bg-rose-100
               dark:bg-rose-900/40 dark:border-rose-700/40 dark:text-rose-100 dark:hover:bg-rose-800/50"
      >
        Review overdue
      </button>

      <button
        v-else
        type="button"
        @click="showToday();"
        class="px-4 py-2 rounded-lg text-sm font-semibold border transition
               bg-sky-50 border-sky-200 text-sky-800 hover:bg-sky-100
               dark:bg-sky-900/30 dark:border-sky-700/40 dark:text-sky-100 dark:hover:bg-sky-800/40"
      >
        View today
      </button>
    </div>

    <div
      v-if="$page.props.reminders.overdueTop?.length || $page.props.reminders.todayTop?.length"
      class="mt-4 grid md:grid-cols-2 gap-4"
    >
      <!-- Today card -->
      <div
        v-if="$page.props.reminders.todayTop?.length"
        class="rounded-xl border border-slate-200 bg-slate-50 p-3
               dark:border-slate-700 dark:bg-slate-900/40"
      >
        <div class="text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2">Today</div>
        <ul class="space-y-2">
          <li
            v-for="it in $page.props.reminders.todayTop"
            :key="it.id"
            class="text-sm text-slate-700 dark:text-slate-200"
          >
            <span class="font-medium text-slate-900 dark:text-white">{{ it.follow_up_date }}</span>
            — {{ it.title }}
            <span v-if="it.sponsor?.name" class="text-amber-700 dark:text-amber-300">
              ({{ it.sponsor.name }})
            </span>
          </li>
        </ul>
      </div>

      <!-- Overdue card -->
      <div
        v-if="$page.props.reminders.overdueTop?.length"
        class="rounded-xl border border-slate-200 bg-slate-50 p-3
               dark:border-slate-700 dark:bg-slate-900/40"
      >
        <div class="text-sm font-semibold text-slate-800 dark:text-slate-200 mb-2">Needs attention</div>
        <ul class="space-y-2">
          <li
            v-for="it in $page.props.reminders.overdueTop"
            :key="it.id"
            class="text-sm text-slate-700 dark:text-slate-200"
          >
            <span class="font-medium text-slate-900 dark:text-white">{{ it.follow_up_date }}</span>
            — {{ it.title }}
            <span v-if="it.sponsor?.name" class="text-amber-700 dark:text-amber-300">
              ({{ it.sponsor.name }})
            </span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>

        <h2 class="text-xl font-bold text-gray-100 mb-4">Quick Actions</h2>
        <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3">

          <!-- ZA SVE ADMINE (content_admin i super_admin) -->
          <template v-if="$page.props.auth.user?.role === 'content_admin' || $page.props.auth.user?.role === 'super_admin'">
            <a href="/admin/items/create"
             class="w-full sm:w-auto px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
              Add Item
            </a>
            <a href="/admin/categories" class="w-full sm:w-auto px-6 py-3 bg-slate-700 hover:bg-slate-600 text-gray-300 rounded-lg font-medium">
              Categories
            </a>
          </template>
          
          <!-- SAMO ZA SUPER ADMIN -->
          <template v-if="$page.props.auth.user?.role === 'super_admin'">
            <a href="/super-admin" class="w-full sm:w-auto px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium">
              Super Admin Panel
            </a>
            <a href="/super-admin/users" class="w-full sm:w-auto px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium">
              Manage Users
            </a>
          </template>
          
          <!-- ZA CONTENT ADMIN ILI SUPER ADMIN -->
          <template v-if="$page.props.auth.user?.role === 'content_admin' || $page.props.auth.user?.role === 'super_admin'">
            <a href="/content-admin" class="w-full sm:w-auto px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">
              Content Admin
            </a>
            <a href="/content-admin/pages" class="w-full sm:w-auto px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium">
              My Pages
            </a>
          </template>
          <template v-else>
              <span class="text-gray-200 text-sm mt-2">No actions to show</span> 
          </template>
        </div>
      </div>

<!-- Reminders -->
<div class="mt-8 ui-card p-6">
  <div class="flex flex-wrap items-start justify-between gap-4">
  <div>
    <h2 class="ui-section-title">Reminders</h2>
    <p class="ui-section-subtitle mt-1">Email reminders for upcoming follow-ups.</p>
  </div>

  <!-- actions right -->
  <div class="ml-auto flex items-center gap-3">
    <button
      type="button"
      @click="rebuildReminders"
      :disabled="rebuildLoading || !remindersEnabled"
      class="ui-btn ui-btn-soft disabled:opacity-50 disabled:cursor-not-allowed"
    >
      {{ rebuildLoading ? 'Rebuilding…' : 'Rebuild reminders' }}
    </button> 
  </div>
</div>

<div class="flex items-center justify-between gap-4">
  <div class="flex flex-wrap items-center gap-5">
    <label class="flex items-center gap-2 ui-muted">
      <input type="checkbox" v-model="remindersEnabled" />
      Enable reminders
    </label>

    <label class="flex items-center gap-2 ui-muted">
      <input type="checkbox" v-model="dailyDigestEnabled" :disabled="!remindersEnabled" />
      Daily digest
    </label>

    <div class="flex items-center gap-2">
      <span class="ui-muted text-sm">Send time</span>
      <input
        type="time"
        v-model="dailyDigestTime"
        :disabled="!remindersEnabled || !dailyDigestEnabled"
        class="ui-input"
      />
    </div>
  </div>

  <button
    @click="saveReminderSettings"
    class="ui-btn ui-btn-primary ml-auto"
  >
    Save settings
  </button>
</div>


  <div class="mt-5">
  <div class="flex items-center justify-between mb-3">
    <span v-if="remindersSaving" class="ui-muted text-sm">Saving…</span>
  </div>

  <div v-if="remindersLoading" class="ui-muted text-sm">Loading…</div>

  <div v-else-if="upcomingReminders.length === 0" class="ui-muted text-sm">
    No scheduled reminders.
    <span v-if="!remindersEnabled" class="ui-muted text-sm">Reminders are disabled.</span>
  </div>

  <div v-else class="overflow-x-auto">
    <div class="flex items-center justify-between mb-3">
      <h3 class="text-cyan-500 font-medium">Upcoming scheduled</h3>
    </div>

    <table class="w-full table-auto">
    <thead>
      <tr class="ui-thead">
        <th class="py-2 pr-3">Sponsor</th>
        <th class="py-2 pr-3">Title</th>
        <th class="py-2 pr-3">Follow-up</th>
        <th class="py-2 pr-3">Reminder</th>
        <th class="py-2 pl-3 text-right"><p class="py-2 pl-3 text-right">Actions</p></th>
      </tr>
    </thead>

     <tbody>
        <tr
          v-for="r in upcomingReminders"
          :key="r.id"
          class="border-b border-slate-800/60"
          :class="r.status !== 'pending' ? 'opacity-40 line-through' : ''"
        >
    <td class="text-amber-700 dark:text-amber-300">{{ r.sponsor || '—' }}</td>

    <td class="py-3 pr-3">
      <a :href="`/dashboard?collab=${r.collaboration_id}`"
         class="ui-muted text-sm underline underline-offset-4">
        {{ r.title || '—' }}
      </a>
    </td>

    <td class="ui-muted text-sm">{{ fmtDate(r.follow_up_date) || '—' }}</td>

    <td
  class="ui-muted text-sm whitespace-nowrap"
  :class="r.expired ? 'text-pink-300' : 'text-amber-300'"
>
  {{ fmtDateTime(r.send_at) }}
</td>


    <td class="py-3 pl-3 text-right">
      <div class="inline-flex items-center gap-2">
        <label class="ui-muted text-sm flex items-center gap-2">
          <input
  type="checkbox"
  :checked="r.status === 'pending'"
  :disabled="r.expired"
  @change="toggleReminder(r.id, $event.target.checked)"
/>


          enabled
          <span v-if="r.expired" class="text-xs text-pink-300">expired</span>

        </label>

        <button
          @click="removeReminder(r.id)"
          class="px-2 py-1 text-xs rounded bg-rose-900/40 hover:bg-rose-800/60 text-rose-100 border border-rose-700/30"
        >
          Remove
        </button>
      </div>
    </td>
  </tr>
</tbody>

  </table>

</div>

  </div>
</div>


<!-- Sponsors -->
<div class="mt-8 ui-card p-6">
  <h2 class="text-xl font-bold text-green-700 mb-4">My Sponsors</h2>

  <!-- Add Sponsor -->
  <div class="flex gap-4 mb-6">
    <input v-model="newSponsor.name"
           placeholder="Sponsor name"
           class="ui-input w-1/3">

    <input v-model="newSponsor.email"
           placeholder="Email"
           class="ui-input w-1/3">

    <input v-model="newSponsor.website"
           placeholder="Website"
           class="ui-input w-1/3">

    <button @click="addSponsor"
            class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded text-white">
      Add
    </button>
  </div>

  <!-- Sponsors list -->
  <div v-if="sponsors.length === 0" class="text-gray-200">
    No sponsors yet.
  </div>

  <ul>
  <li v-for="(s, index) in sortedSponsors" :key="s.id">

  <!-- top purple separator (ako ga želiš iznad svakog carda) -->
  <div
    v-if="index === 0"
    class="h-[2px] bg-gradient-to-r from-transparent via-purple-600 to-transparent my-4"
  />

  <!-- Sponsor card -->
  <div class="rounded-t p-3 flex items-start justify-between gap-4 border shadow-sm
            bg-amber-50 border-amber-200
            dark:bg-slate-900 dark:border-slate-700 dark:shadow-none">
    <div>
      <button
  type="button"
  @click="filterBySponsor(s.name)"
  class="font-medium text-amber-700 hover:text-amber-800
       underline decoration-amber-600/40 hover:decoration-amber-700/60
       underline-offset-4 text-left
       dark:text-amber-400 dark:hover:text-amber-300
       dark:decoration-amber-500/40 dark:hover:decoration-amber-300/60"

>
  {{ s.name }}
</button>
      <div class="ui-muted text-sm">{{ s.email }}</div>
      <div class="text-xs ui-muted mt-1">
  added: {{ formatSponsorDate(s.created_at) }}
</div>
      <div class="ui-muted text-sm">{{ s.website }}</div>
      
    </div>

    <button
      @click="openConfirm({
        title: 'Delete sponsor',
        message: 'Are you sure? This can affect collaborations linked to this sponsor.',
        action: () => deleteSponsor(s.id)
      })"
      class="px-3 py-1 text-sm rounded bg-red-700 hover:bg-red-600 text-white whitespace-nowrap"
    >
      Delete
    </button>
  </div>

  <!-- blue underline for every card -->
  <div class="h-[2px] bg-gradient-to-r from-transparent via-blue-500 to-transparent"></div>
</li>

</ul>
</div>

<!-- Add Collaboration -->
<div class="mt-8 ui-card p-6">
  <h2 class="text-xl font-bold text-purple-700 mb-4 border-slate-700">New Collaboration</h2>

  <div class="flex gap-4 flex-wrap">
    <!-- Sponsor -->
    <select v-model="newCollab.sponsor_id"
      class="ui-input w-1/3">
      <option value="">Select sponsor</option>
      <option v-for="s in sponsors" :key="s.id" :value="s.id">
        {{ s.name }}
      </option>
    </select>

    <!-- Title -->
    <input v-model="newCollab.title"
      placeholder="Collaboration title"
      class="ui-input w-1/3" />

    <!-- Button -->
    <button @click="addCollaboration"
      class="bg-purple-600 hover:bg-purple-700 px-6 py-2 rounded text-white">
      Add
    </button>
  </div>
</div>

      <!-- Collaborations Table -->
<div id="collaborations-section" class="mt-8 ui-card p-6">
  <h2 class="text-xl font-bold text-gray-100 mb-4">My Collaborations</h2>
  <div class="flex flex-wrap gap-4 mb-4">
  <!-- Status filter -->
  <select v-model="filters.status"
    class="ui-input rounded px-3 py-2 text-white">
    <option value="">All statuses</option>
    <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
  </select>

  <!-- Overdue toggle -->
  <label class="flex items-center gap-2 ui-muted">
    <input type="checkbox" v-model="filters.onlyOverdue" />
    Only overdue
  </label>

  <!-- Only Today toggle -->
  <label class="flex items-center gap-2 ui-muted">
  <input type="checkbox" v-model="filters.onlyToday" />
  Follow-ups Today
</label>


  <!-- Sort -->
  <select v-model="filters.sort"
    class="ui-input rounded px-3 py-2 text-white">
    <option value="newest">Newest</option>
    <option value="followup">Follow-up date</option>
  </select>
</div>
<hr class="h-[2px] bg-gradient-to-r from-transparent via-purple-600 to-transparent border-0">

<!-- Active filters bar (nicer chips + remove x) -->
<div
  v-if="hasActiveFilters"
  class="mt-4 mb-3 flex flex-wrap items-center gap-2"
>
  <span class="ui-muted text-sm mr-1">Active:</span>

  <!-- Search -->
<span
  v-if="filters.q"
  class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm border
         bg-sky-50 text-sky-800 border-sky-200
         dark:bg-sky-900/25 dark:text-sky-200 dark:border-sky-700/40"
>
  <span>Search: <span class="font-medium">{{ filters.q }}</span></span>
  <button
    type="button"
    @click="clearSearch"
    class="w-5 h-5 inline-flex items-center justify-center rounded-full
           bg-sky-100 hover:bg-sky-200 text-sky-700
           dark:bg-sky-800/40 dark:hover:bg-sky-700/50 dark:text-sky-100"
    aria-label="Remove search filter"
  >
    ×
  </button>
</span>

<!-- Mode -->
<span
  v-if="filters.q && filters.mode && filters.mode !== 'all'"
  class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm border
         bg-sky-50 text-sky-800 border-sky-200
         dark:bg-sky-900/25 dark:text-sky-200 dark:border-sky-700/40"
>
  <span>In: <span class="font-medium">{{ filters.mode }}</span></span>
  <button
    type="button"
    @click="clearMode"
    class="w-5 h-5 inline-flex items-center justify-center rounded-full
           bg-sky-100 hover:bg-sky-200 text-sky-700
           dark:bg-sky-800/40 dark:hover:bg-sky-700/50 dark:text-sky-100"
    aria-label="Remove mode filter"
  >
    ×
  </button>
</span>


  <!-- Status -->
  <span
    v-if="filters.status"
    class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm
           bg-emerald-900/20 text-emerald-200 border border-emerald-700/30"
  >
    <span>Status: <span class="font-medium">{{ filters.status }}</span></span>
    <button
      type="button"
      @click="clearStatus"
      class="w-5 h-5 inline-flex items-center justify-center rounded-full
             bg-emerald-800/30 hover:bg-emerald-700/40 text-emerald-100"
      aria-label="Remove status filter"
    >
      ×
    </button>
  </span>

  <!-- Overdue -->
  <span
    v-if="filters.onlyOverdue"
    class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm
           bg-rose-900/25 text-rose-200 border border-rose-700/30"
  >
    <span>Overdue</span>
    <button
      type="button"
      @click="clearOverdue"
      class="w-5 h-5 inline-flex items-center justify-center rounded-full
             bg-rose-800/30 hover:bg-rose-700/40 text-rose-100"
      aria-label="Remove overdue filter"
    >
      ×
    </button>
  </span>

  <!-- Today -->
  <span
    v-if="filters.onlyToday"
    class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm
           bg-sky-900/25 text-sky-200 border border-sky-700/30"
  >
    <span>Today</span>
    <button
      type="button"
      @click="clearToday"
      class="w-5 h-5 inline-flex items-center justify-center rounded-full
             bg-sky-800/30 hover:bg-sky-700/40 text-sky-100"
      aria-label="Remove today filter"
    >
      ×
    </button>
  </span>

  <!-- Sort -->
  <span
    v-if="filters.sort && filters.sort !== 'newest'"
    class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm
           bg-slate-800/60 text-gray-200 border border-slate-600/60"
  >
    <span>Sort: <span class="font-medium">{{ filters.sort }}</span></span>
    <button
      type="button"
      @click="clearSort"
      class="w-5 h-5 inline-flex items-center justify-center rounded-full
             bg-slate-700/70 hover:bg-slate-600 text-gray-200"
      aria-label="Remove sort filter"
    >
      ×
    </button>
  </span>

  <!-- Clear all (as a chip; not stuck to far right) -->
  <button
    type="button"
    @click="resetFilters"
    class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm
          border border-rose-300 bg-rose-50 text-rose-700 hover:bg-rose-100
        dark:border-rose-700/40 dark:bg-rose-900/40 dark:text-rose-100 dark:hover:bg-rose-800/50
"
  >
    Clear all
    <span class="w-5 h-5 inline-flex items-center justify-center rounded-full bg-slate-800/60">
      ×
    </span>
  </button>
</div>



<div v-if="filteredCollaborations.length <= 0" class="text-gray-200 mb-3">
  No collaborations match the selected filters.
</div>
<div v-if="filteredCollaborations.length > 0" class="w-full overflow-x-auto -mx-4 px-4">
  <table class="min-w-[900px] ui-table">
    <thead>
      <tr class="ui-thead">
        <th class="py-2 px-4">Title</th>
        <th class="py-2 px-4">Sponsor</th>
        <th class="py-2 px-4">Status</th>
        <th class="py-2 px-4">Follow-up</th>
        <th class="py-2 px-4">Actions</th>
      </tr>
    </thead>
    <tbody>
      
      <template v-for="(collab, index) in filteredCollaborations" :key="collab.id">
  <tr
    :class="[
      'hover:bg-slate-700/30',
      isOverdue(collab) ? 'bg-red-900/20' : ''
    ]"
  >
    <td class="py-3 px-4 ui-td">{{ collab.title }}</td>

    <td class="py-3 px-4">
      <button
  type="button"
  @click="filterBySponsor(collab.sponsor.name)"
  class="bg-blue-900 rounded font-medium text-amber-400 hover:text-amber-300 underline decoration-amber-500/40 hover:decoration-amber-300/60 underline-offset-4"
>
  {{ collab.sponsor.name }}
</button>

    </td>

    <td class="py-3 px-4">
      <span
        :class="[
          'px-3 py-1 rounded-full text-sm font-medium',
          statusBadgeClass(collab.status)
        ]"
      >
        {{ collab.status }}
      </span>
    </td>

    <td class="py-3 px-4">
  <div class="flex items-center gap-2 flex-wrap">
    <button
      type="button"
      @click="openFollowUp(collab)"
      class="px-3 py-1 text-sm rounded bg-slate-800/60 hover:bg-slate-700 text-white border border-slate-600/60"
    >
      {{ collab.follow_up_date ? collab.follow_up_date : 'Set follow-up' }}
    </button>

    <button
  v-if="!collab.follow_up_date"
  type="button"
  @click="quickFollowUp(collab, 'today')"
  class="px-2 py-1 text-xs rounded border
         bg-sky-50 text-sky-800 border-sky-200 hover:bg-sky-100
         dark:bg-sky-900/25 dark:hover:bg-sky-800/40 dark:text-sky-200 dark:border-sky-700/40"
>
  Today
</button>

<button
  v-if="!collab.follow_up_date"
  type="button"
  @click="quickFollowUp(collab, 'tomorrow')"
  class="px-2 py-1 text-xs rounded border
         bg-sky-50 text-sky-800 border-sky-200 hover:bg-sky-100
         dark:bg-sky-900/25 dark:hover:bg-sky-800/40 dark:text-sky-200 dark:border-sky-700/40"
>
  Tomorrow
</button>


    <span
      v-if="isToday(collab)"
      class="px-2 py-1 text-xs rounded-full bg-blue-700/60 text-blue-100 border border-blue-600/40"
    >
      TODAY
    </span>

    <span
      v-else-if="isOverdue(collab)"
      class="px-2 py-1 text-xs rounded-full bg-red-700/60 text-red-100 border border-red-600/40"
    >
      OVERDUE
    </span>
  </div>
</td>



    <td class="py-3 px-4">
      <!-- Desktop buttons -->
      <div class="hidden sm:flex gap-2 flex-wrap justify-start items-center">
        <button
  type="button"
  v-for="s in statuses"
  :key="s"
  @click="updateStatus(collab.id, s)"
  :class="statusBtnClass(s)"
  class="px-2 py-1 text-sm rounded font-medium whitespace-nowrap"
>
  {{ s }}
</button>

    <button
  type="button"
  @click="openEmail(collab)"
  class="px-2 py-1 text-sm rounded bg-sky-700 hover:bg-sky-600 text-white whitespace-nowrap"
>
  Email
</button>

        <!-- delete separate, not mixed -->
        <button
          @click="openConfirm({
            title: 'Delete collaboration',
            message: 'Delete this collaboration permanently?',
            action: () => deleteCollaboration(collab.id)
          })"
          class="px-2 py-1 text-sm rounded bg-red-700 hover:bg-red-600 text-white whitespace-nowrap"
        >
          Delete
        </button>
      </div>

      <!-- Mobile dropdown + delete -->
      <div class="sm:hidden space-y-2">
        <select
          class="bg-slate-900 border border-slate-700 rounded px-3 py-2 text-white w-full"
          :value="collab.status"
          @change="updateStatus(collab.id, $event.target.value)"
        >
          <option v-for="s in statuses" :key="s" :value="s">
            {{ s }}
          </option>
        </select>

        <button
          @click="openConfirm({
            title: 'Delete collaboration',
            message: 'Delete this collaboration permanently?',
            action: () => deleteCollaboration(collab.id)
          })"
          class="w-full px-3 py-2 text-sm rounded bg-red-700 hover:bg-red-600 text-white"
        >
          Delete
        </button>

        <button
  type="button"
  @click="openEmail(collab)"
  class="w-full px-3 py-2 text-sm rounded bg-sky-700 hover:bg-sky-600 text-white"
>
  Email sponsor
</button>

      </div>
    </td>
  </tr>

  <!-- ✅ BLUE GRADIENT SEPARATOR -->
  <tr v-if="index < filteredCollaborations.length - 1">
    <td colspan="5" class="p-0">
      <div class="h-[2px] bg-gradient-to-r from-transparent via-blue-500 to-transparent"></div>
    </td>
  </tr>
</template>

    </tbody>
  </table>
</div>
</div>

    <!-- Email Modal -->
<div v-if="emailModal.open" class="fixed inset-0 z-50 flex items-center justify-center">
  <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="closeEmail"></div>

  <div class="relative w-full max-w-lg mx-4 bg-slate-900 border border-slate-700 rounded-xl p-6">
    <h3 class="text-lg font-bold text-white mb-1">Email sponsor</h3>
    <p class="text-gray-200 text-sm mb-4">
      To: <span class="text-amber-300">{{ emailModal.to || 'No email on sponsor' }}</span>
    </p>

    <!-- Template -->
<div class="flex flex-wrap items-center gap-3">
  <label class="text-sm text-gray-200">Template</label>

  <select
    v-model="emailModal.templateKey"
    @change="applyEmailTemplate(emailModal.templateKey)"
    class="bg-slate-800 border border-slate-600 rounded px-3 py-2 text-white"
  >
    <optgroup label="Built-in">
      <option v-for="t in builtinTemplates" :key="t.key" :value="t.key">
        {{ t.label }}
      </option>
    </optgroup>

    <optgroup v-if="userTemplates.length" label="My templates">
      <option v-for="t in userTemplates" :key="t.key" :value="t.key">
        {{ t.label }}
      </option>
    </optgroup>
  </select>

  <button
    type="button"
    @click="openSaveTemplate"
    class="px-3 py-2 rounded bg-slate-700 hover:bg-slate-600 text-white text-sm"
  >
    Save as template
  </button>

  <button
    v-if="isSelectedUserTemplate"
    type="button"
    @click="deleteSelectedTemplate"
    class="px-3 py-2 rounded bg-rose-900/40 hover:bg-rose-800/50 text-rose-100 text-sm"
  >
    Delete template
  </button>
</div>


    <div class="space-y-3">
      <input
        v-model="emailModal.subject"
        class="w-full bg-slate-800 border border-slate-600 rounded px-3 py-2 text-white"
        placeholder="Subject"
      />

      <textarea
        v-model="emailModal.body"
        rows="8"
        class="w-full bg-slate-800 border border-slate-600 rounded px-3 py-2 text-white"
        placeholder="Message"
      ></textarea>
    </div>

    <div class="mt-5 flex flex-wrap justify-end gap-3">
      <button
        type="button"
        @click="closeEmail"
        class="ui-btn ui-btn-soft"
      >
        Cancel
      </button>

      <button
        type="button"
        :disabled="!emailModal.to"
        @click="sendMailto"
        class="px-4 py-2 rounded bg-sky-700 hover:bg-sky-600 text-white disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Open email app
      </button>
    </div>
  </div>
</div>
<!-- Save Template Modal -->
<div v-if="saveTplModal.open" class="fixed inset-0 z-50 flex items-center justify-center">
  <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="closeSaveTemplate"></div>

  <div class="relative w-full max-w-md mx-4 bg-slate-900 border border-slate-700 rounded-xl p-6">
    <h3 class="text-lg font-bold text-white mb-2">Save template</h3>

    <input
      v-model="saveTplModal.label"
      class="w-full bg-slate-800 border border-slate-600 rounded px-3 py-2 text-white"
      placeholder="Template name (e.g. My Intro)"
    />

    <p class="text-gray-200 text-sm mt-3">
      This saves subject + body from the current email form (stored locally in your browser).
    </p>

    <div class="mt-5 flex justify-end gap-3">
      <button
        type="button"
        @click="closeSaveTemplate"
        class="ui-btn ui-btn-soft"
      >
        Cancel
      </button>
      <button
        type="button"
        @click="saveCurrentAsTemplate"
        class="px-4 py-2 rounded bg-sky-700 hover:bg-sky-600 text-white"
      >
        Save
      </button>
    </div>
  </div>
</div>
    <!-- Confirm Modal -->
<div
  v-if="confirmModal.open"
  class="fixed inset-0 z-50 flex items-center justify-center"
>
  <!-- backdrop -->
  <div
    class="absolute inset-0 bg-black/60 backdrop-blur-sm"
    @click="closeConfirm"
  ></div>

  <!-- modal -->
  <div class="relative w-full max-w-md mx-4 bg-slate-900 border border-slate-700 rounded-xl p-6">
    <h3 class="text-lg font-bold text-white mb-2">{{ confirmModal.title }}</h3>
    <p class="text-gray-300 mb-6">{{ confirmModal.message }}</p>
    <label class="text-sm text-gray-200">Follow-up date</label>
<input type="date" v-model="followUpModal.date" class="..." />

<label class="flex items-center gap-2 text-gray-300 mt-4">
  <input type="checkbox" v-model="followUpModal.reminderEnabled" />
  Email reminder
</label>

<input
  v-if="followUpModal.reminderEnabled"
  type="datetime-local"
  v-model="followUpModal.reminderSendAt"
  class="w-full mt-2 bg-slate-800 rounded px-3 py-2 text-white border"
  :class="followUpErrors.reminderSendAt ? 'border-rose-500 ring-1 ring-rose-500/40' : 'border-slate-600'"
/>

<p
  v-if="followUpErrors.reminderSendAt"
  class="mt-2 text-sm text-rose-300"
>
  {{ followUpErrors.reminderSendAt }}
</p>

    <div class="flex justify-end gap-3">
      <button
        @click="closeConfirm"
        class="ui-btn ui-btn-soft"
      >
        Cancel
      </button>
      <button
        @click="confirmYes"
        class="px-4 py-2 rounded bg-red-700 hover:bg-red-600 text-white"
      >
        Delete
      </button>
    </div>
  </div>
</div>
<!-- Follow-up Modal -->
<div v-if="followUpModal.open" class="fixed inset-0 z-50 flex items-center justify-center">
  <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="closeFollowUp"></div>

  <div class="relative w-full max-w-md mx-4 rounded-xl border border-slate-700 bg-slate-900 p-6">
  <div class="mb-4">
    <h3 class="text-lg font-semibold text-white">Set follow-up</h3>
    <p class="mt-1 text-sm text-gray-200">
      <span class="text-gray-200">{{ followUpModal.title }}</span>
    </p>
  </div>

  <!-- Follow-up date -->
  <div>
    <label class="block text-sm text-gray-200 mb-2">Follow-up date</label>
    <input
      type="date"
      v-model="followUpModal.date"
      class="w-full rounded-lg border border-slate-600 bg-slate-800 px-3 py-2 text-white outline-none focus:ring-2 focus:ring-sky-500/30"
    />
  </div>

  <!-- Reminder -->
  <div class="mt-5">
    <label class="flex items-center gap-2 ui-muted">
      <input type="checkbox" v-model="followUpModal.reminderEnabled" />
      <span class="text-sm">Email reminder</span>
    </label>

    <div v-if="followUpModal.reminderEnabled" class="mt-3">
      <label class="block text-sm text-gray-200 mb-2">Reminder date & time</label>

      <input
        type="datetime-local"
        v-model="followUpModal.reminderSendAt"
        class="w-full rounded-lg border bg-slate-800 px-3 py-2 text-white outline-none focus:ring-2"
        :class="followUpErrors.reminderSendAt
          ? 'border-rose-500 ring-rose-500/30 focus:ring-rose-500/30'
          : 'border-slate-600 focus:ring-sky-500/30'"
      />

      <p v-if="followUpErrors.reminderSendAt" class="mt-2 text-sm text-rose-300">
        {{ followUpErrors.reminderSendAt }}
      </p>

      <div class="mt-3 flex flex-wrap gap-2">
        <button type="button" @click="setReminderOffset(0)"
          class="rounded-lg border border-sky-700/40 bg-sky-900/20 px-3 py-1.5 text-sm text-sky-200 hover:bg-sky-900/30">
          Today 08:30
        </button>
        <button type="button" @click="setReminderOffset(1)"
          class="rounded-lg border border-sky-700/40 bg-sky-900/20 px-3 py-1.5 text-sm text-sky-200 hover:bg-sky-900/30">
          Tomorrow 08:30
        </button>
        <button type="button" @click="setReminderOffset(3)"
          class="rounded-lg border border-sky-700/40 bg-sky-900/20 px-3 py-1.5 text-sm text-sky-200 hover:bg-sky-900/30">
          +3 days 08:30
        </button>
        <button type="button" @click="setReminderOffset(7)"
          class="rounded-lg border border-sky-700/40 bg-sky-900/20 px-3 py-1.5 text-sm text-sky-200 hover:bg-sky-900/30">
          +1 week 08:30
        </button>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <div class="mt-6 flex items-center justify-between gap-3">
    <button
      v-if="followUpModal.date"
      type="button"
      @click="clearFollowUpDate"
      class="rounded-lg border border-rose-700/30 bg-rose-900/30 px-3 py-2 text-sm text-rose-100 hover:bg-rose-900/40"
    >
      Clear date
    </button>

    <div class="ml-auto flex gap-3">
      <button
        type="button"
        @click="closeFollowUp"
        class="rounded-lg bg-slate-700 px-4 py-2 text-white hover:bg-slate-600"
      >
        Cancel
      </button>

      <button
        type="button"
        @click="saveFollowUp"
        :disabled="followUpModal.reminderEnabled && !followUpModal.reminderSendAt"
        class="rounded-lg bg-emerald-700 px-4 py-2 text-white hover:bg-emerald-600 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Save
      </button>
    </div>
  </div>
</div>

</div>

  </MainLayout>
</template>

<script setup>
import { ref, onMounted, computed, nextTick, watch, reactive } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import MainLayout from '@/Layouts/MainLayout.vue'
import axios from 'axios'

const loading = ref(false)
const loadError = ref('')
const remindersEnabled = ref(true)           // ili false, kako hoćeš default
const dailyDigestEnabled = ref(false)
const dailyDigestTime = ref('08:30')

const followUpErrors = reactive({
  reminderSendAt: '',
})


const page = usePage()

const syncSearchFromUrl = async () => {
  const url = new URL(window.location.href)
  const q = url.searchParams.get('q') || ''
  const mode = url.searchParams.get('mode') || 'all'

  // set filters (samo ako se razlikuje)
  if ((q || '') !== (filters.value.q || '')) filters.value.q = q
  if ((mode || 'all') !== (filters.value.mode || 'all')) filters.value.mode = mode

  // Ako ima query, scroll do collaborations
  if (q) await scrollToCollaborations()
}

const emailReminders = ref(!!page.props.auth?.user?.email_reminders)
const saving = ref(false)

// samo sync UI kad server vrati novo stanje
watch(
  () => page.props.auth?.user?.email_reminders,
  (val) => { emailReminders.value = !!val }
)

const setReminderOffset = (days, time = '08:30') => {
  const d = new Date()
  d.setSeconds(0, 0)
  d.setDate(d.getDate() + days)
  const [hh, mm] = time.split(':')
  d.setHours(Number(hh), Number(mm), 0, 0)

  const pad = (n) => String(n).padStart(2, '0')
  followUpModal.value.reminderSendAt =
    `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`

  followUpErrors.reminderSendAt = ''
}

const removeReminder = async (reminderId) => {
  try {
    await axios.delete(`/reminders/${reminderId}`)
    await fetchUpcomingReminders()
  } catch (e) {
    console.error('remove failed', e?.response?.data || e)
  }
}


const toggleReminder = async (reminderId, enabled) => {
  try {
    await axios.post(`/reminders/${reminderId}/toggle`, { enabled: !!enabled })
    await fetchUpcomingReminders()
  } catch (e) {
    console.error('toggle failed', e?.response?.data || e)
  }
}

const toggleEmailReminders = () => {
  if (saving.value) return
  saving.value = true

  const next = !emailReminders.value
  emailReminders.value = next // optimistic

  router.patch(
    '/settings/reminders',
    { email_reminders: next },
    {
      preserveScroll: true,
      preserveState: true,
      onFinish: () => { saving.value = false },
      onError: () => { saving.value = false }
    }
  )
}



const filters = ref({
  status: '',
  onlyOverdue: false,
  onlyToday: false,
  sort: 'newest',
  q: '',
  mode: 'all' // all | collaborations | sponsors
})

const clearStatus = () => { filters.value.status = '' }
const clearOverdue = () => { filters.value.onlyOverdue = false }
const clearToday = () => { filters.value.onlyToday = false }
const clearSort = () => { filters.value.sort = 'newest' }

const clearSearch = () => {
  filters.value.q = ''
  // kad nema q, mode nema smisla – vrati na all
  filters.value.mode = 'all'
}

const clearMode = () => {
  filters.value.mode = 'all'
}


const filteredCollaborations = computed(() => {
  let list = [...collaborations.value]

    // ✅ App search (q + mode)
  const q = (filters.value.q || '').trim().toLowerCase()
  const mode = filters.value.mode || 'all'

  if (q) {
    // ako je mode=sponsors, filtriramo po sponsor.name (i opcionalno website ako želiš)
    if (mode === 'sponsors') {
      list = list.filter(c =>
        (c.sponsor?.name || '').toLowerCase().includes(q)
      )
    } else {
      // collaborations ili all: title OR sponsor.name
      list = list.filter(c =>
        (c.title || '').toLowerCase().includes(q) ||
        (c.sponsor?.name || '').toLowerCase().includes(q)
      )
    }
  }

  if (filters.value.status) {
    list = list.filter(c => c.status === filters.value.status)
  }

  if (filters.value.onlyOverdue) {
    list = list.filter(c => isOverdue(c))
  }

  if (filters.value.onlyToday) {
  const t = todayStr()
  list = list.filter(c => isOpenStatus(c) && c.follow_up_date === t)
}


  if (filters.value.sort === 'followup') {
    list.sort((a, b) => {
      const ad = a.follow_up_date ? new Date(a.follow_up_date + 'T00:00:00') : new Date('9999-12-31')
      const bd = b.follow_up_date ? new Date(b.follow_up_date + 'T00:00:00') : new Date('9999-12-31')
      return ad - bd
    })
  } else {
    // newest
    list.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
  }

  return list
})

const statuses = ['idea','contacted','replied','negotiating','active','finished','lost']

const statusBtnClass = (s) => {
  return [
    s === 'idea' ? 'bg-gray-700 text-gray-200 hover:bg-gray-600' :
    s === 'contacted' ? 'bg-blue-700 text-white hover:bg-blue-600' :
    s === 'replied' ? 'bg-indigo-700 text-white hover:bg-indigo-600' :
    s === 'negotiating' ? 'bg-yellow-700 text-white hover:bg-yellow-600' :
    s === 'active' ? 'bg-green-700 text-white hover:bg-green-600' :
    s === 'finished' ? 'bg-purple-700 text-white hover:bg-purple-600' :
    s === 'lost' ? 'bg-red-700 text-white hover:bg-red-600' :
    'bg-gray-500 text-white'
  ]
}

const statusBadgeClass = (s) => {
  return (
    s === 'idea' ? 'bg-gray-700 text-gray-200' :
    s === 'contacted' ? 'bg-blue-700 text-white' :
    s === 'replied' ? 'bg-indigo-700 text-white' :
    s === 'negotiating' ? 'bg-yellow-700 text-white' :
    s === 'active' ? 'bg-green-700 text-white' :
    s === 'finished' ? 'bg-purple-700 text-white' :
    s === 'lost' ? 'bg-red-700 text-white' :
    'bg-gray-500 text-white'
  )
}


const sponsors = ref([])
const newSponsor = ref({
  name: '',
  email: '',
  website: ''
})
const collaborations = ref([])
const newCollab = ref({
  sponsor_id: '',
  title: '',
  status: 'idea'
})


async function fetchSponsors() {
  const { data } = await axios.get('/sponsors')
  sponsors.value = data.sort((a, b) => Number(b.id) - Number(a.id))
}


async function fetchCollaborations() {
  const { data } = await axios.get('/collaborations')
  collaborations.value = data
}

const addSponsor = async () => {
  await axios.post('/sponsors', newSponsor.value)
  newSponsor.value = { name: '', email: '', website: '' }
  await fetchSponsors()
}


const addCollaboration = async () => {
  if (!newCollab.value.sponsor_id || !newCollab.value.title) return

  await axios.post('/collaborations', newCollab.value)
  await fetchCollaborations()

  newCollab.value = {
    sponsor_id: '',
    title: '',
    status: 'idea'
  }
}


async function updateStatus(id, status) {
  try {
    await axios.patch(`/collaborations/${id}/status`, { status })
    await fetchCollaborations()
  } catch (e) {
    console.error('status patch failed', e?.response?.data || e)
  }
}


const isOverdue = (collab) => {
  if (!collab.follow_up_date) return false
  if (collab.status === 'finished' || collab.status === 'lost') return false

  // collab.follow_up_date je string tipa '2026-02-04'
  const today = new Date()
  today.setHours(0,0,0,0)

  const due = new Date(collab.follow_up_date + 'T00:00:00')
  return due < today
}

const isToday = (collab) => {
  if (!collab.follow_up_date) return false
  if (collab.status === 'finished' || collab.status === 'lost') return false
  return collab.follow_up_date === todayStr()
}

const deleteCollaboration = async (id) => {
  await axios.delete(`/collaborations/${id}`)
  await fetchCollaborations()
}

const deleteSponsor = async (id) => {
  await axios.delete(`/sponsors/${id}`)
  await fetchSponsors()
  await fetchCollaborations()
}


const confirmModal = ref({
  open: false,
  title: '',
  message: '',
  action: null,
})

const openConfirm = ({ title, message, action }) => {
  confirmModal.value = { open: true, title, message, action }
}

const closeConfirm = () => {
  confirmModal.value.open = false
  confirmModal.value.action = null
}

const confirmYes = async () => {
  if (typeof confirmModal.value.action === 'function') {
    await confirmModal.value.action()
  }
  closeConfirm()
}

const todayStr = () => {
  const d = new Date()
  d.setHours(0, 0, 0, 0)
  return d.toISOString().slice(0, 10) // "YYYY-MM-DD"
}

const isOpenStatus = (c) => c.status !== 'finished' && c.status !== 'lost'

const totalCollaborations = computed(() => collaborations.value.length)

const activeCount = computed(() =>
  collaborations.value.filter(c => c.status === 'active').length
)

const followUpsTodayCount = computed(() => {
  const t = todayStr()
  return collaborations.value.filter(c =>
    isOpenStatus(c) && c.follow_up_date && c.follow_up_date === t
  ).length
})

const overdueCount = computed(() => {
  const t = todayStr()
  return collaborations.value.filter(c =>
    isOpenStatus(c) && c.follow_up_date && c.follow_up_date < t
  ).length
})

// Klik akcije za kartice
const showAll = () => {
  filters.value.q = ''
filters.value.mode = 'all'
  resetFilters()
  scrollToCollaborations()
}

const showOverdue = () => {
  filters.value.status = ''
  filters.value.onlyOverdue = true
  filters.value.onlyToday = false
  filters.value.sort = 'followup'
  scrollToCollaborations()
}

const showToday = () => {
  filters.value.status = ''
  filters.value.onlyOverdue = false
  filters.value.onlyToday = true
  filters.value.sort = 'followup'
  scrollToCollaborations()
}

const showActive = () => {
  filters.value.status = 'active'
  filters.value.onlyOverdue = false
  filters.value.onlyToday = false
  filters.value.sort = 'newest'
  scrollToCollaborations()
}

const scrollToCollaborations = async () => {
  await nextTick()
  await nextTick()
  requestAnimationFrame(() => {
    const el = document.getElementById('collaborations-section')
    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' })
  })
}

const hasActiveFilters = computed(() => {
  return !!filters.value.status ||
    filters.value.onlyOverdue ||
    filters.value.onlyToday ||
    (filters.value.sort && filters.value.sort !== 'newest') ||
    !!filters.value.q ||
    (filters.value.mode && filters.value.mode !== 'all')
})



const resetFilters = () => {
  filters.value.status = ''
  filters.value.onlyOverdue = false
  filters.value.onlyToday = false
  filters.value.sort = 'newest'
  filters.value.q = ''
filters.value.mode = 'all'
}

const filterBySponsor = async (name) => {
  // postavi search da traži po sponsor imenu
  filters.value.q = name
  filters.value.mode = 'sponsors'

  // opcionalno: resetuj ostale filtere da user odmah vidi rezultate
  filters.value.status = ''
  filters.value.onlyOverdue = false
  filters.value.onlyToday = false
  filters.value.sort = 'newest'

  await scrollToCollaborations()
}

const emailModal = ref({
  open: false,
  to: '',
  subject: '',
  body: '',
  templateKey: 'intro',
  collab: null,     // čuvamo trenutni collab
  sponsor: null,    // čuvamo trenutnog sponsora
})

const openEmail = (collab) => {
  const sponsor = collab?.sponsor || {}

  emailModal.value.open = true
  emailModal.value.collab = collab
  emailModal.value.sponsor = sponsor

  emailModal.value.to = sponsor.email || ''

  // učitaj zadnji template ako postoji
  const saved = localStorage.getItem('emailTemplateKey')
  emailModal.value.templateKey = saved || 'intro'

  // primijeni template (popuni subject/body)
  applyEmailTemplate(emailModal.value.templateKey)
}

const closeEmail = () => {
  emailModal.value.open = false
  emailModal.value.to = ''
  emailModal.value.subject = ''
  emailModal.value.body = ''
  emailModal.value.collab = null
  emailModal.value.sponsor = null
}


const sendMailto = () => {
  const to = emailModal.value.to
  const subject = encodeURIComponent(emailModal.value.subject || '')
  const body = encodeURIComponent(emailModal.value.body || '')
  window.location.href = `mailto:${to}?subject=${subject}&body=${body}`
}

const fill = (tpl, vars) =>
  tpl.replace(/\{\{(\w+)\}\}/g, (_, k) => (vars[k] ?? ''))

  const USER_TPL_KEY = 'emailUserTemplates'

const userTemplates = ref([])

const loadUserTemplates = () => {
  try {
    userTemplates.value = JSON.parse(localStorage.getItem(USER_TPL_KEY) || '[]')
  } catch {
    userTemplates.value = []
  }
}

const persistUserTemplates = () => {
  localStorage.setItem(USER_TPL_KEY, JSON.stringify(userTemplates.value))
}

const isSelectedUserTemplate = computed(() =>
  userTemplates.value.some(t => t.key === emailModal.value.templateKey)
)


const builtinTemplates = [
  {
    key: 'intro',
    label: 'Intro / First contact',
    subject: 'Collaboration: {{title}}',
    body:
`Hi {{sponsorName}},

I’m reaching out regarding a potential collaboration: "{{title}}".
Would you be open to a quick chat this week?

Best regards,
{{userName}}`
  },
  {
    key: 'followup',
    label: 'Follow-up (reminder)',
    subject: 'Quick follow-up: {{title}}',
    body:
`Hi {{sponsorName}},

Just following up on my last message about "{{title}}".
If you’re interested, I can send a short plan + next steps.

Best regards,
{{userName}}`
  },
  {
    key: 'final',
    label: 'Final check-in',
    subject: 'Final check-in: {{title}}',
    body:
`Hi {{sponsorName}},

Last quick check-in about "{{title}}".
No worries if now isn’t a good time — just let me know and I’ll close it on my side.

Best regards,
{{userName}}`
  },
  {
    key: 'thankyou',
    label: 'Thank you / After agreement',
    subject: 'Thanks — {{title}}',
    body:
`Hi {{sponsorName}},

Thanks for the time and the positive reply about "{{title}}".
I’ll send the next steps and we can confirm the timeline.

Best regards,
{{userName}}`
  }
]

const allTemplates = computed(() => [...builtinTemplates, ...userTemplates.value])

const applyEmailTemplate = (key) => {
  const tpl = allTemplates.value.find(t => t.key === key) || allTemplates.value[0]
  if (!tpl) return

  const sponsor = emailModal.value.sponsor || {}
  const collab = emailModal.value.collab || {}

  const vars = {
    sponsorName: sponsor.name || '',
    userName: page.props.auth?.user?.name || '',
    title: collab.title || ''
  }

  emailModal.value.subject = fill(tpl.subject, vars)
  emailModal.value.body = fill(tpl.body, vars)

  localStorage.setItem('emailTemplateKey', key)
}

const saveTplModal = ref({
  open: false,
  label: ''
})

const openSaveTemplate = () => {
  saveTplModal.value.open = true
  saveTplModal.value.label = ''
}

const closeSaveTemplate = () => {
  saveTplModal.value.open = false
  saveTplModal.value.label = ''
}

const slugKey = (label) =>
  'user_' + label.toLowerCase().trim().replace(/\s+/g, '_').replace(/[^a-z0-9_]/g, '')

const saveCurrentAsTemplate = () => {
  const label = (saveTplModal.value.label || '').trim()
  if (!label) return

  const key = slugKey(label)

  // overwrite ako postoji isti key
  const next = userTemplates.value.filter(t => t.key !== key)
  next.unshift({
    key,
    label,
    subject: emailModal.value.subject || '',
    body: emailModal.value.body || ''
  })

  userTemplates.value = next
  persistUserTemplates()

  emailModal.value.templateKey = key
  localStorage.setItem('emailTemplateKey', key)

  closeSaveTemplate()
}

const deleteSelectedTemplate = () => {
  const key = emailModal.value.templateKey
  userTemplates.value = userTemplates.value.filter(t => t.key !== key)
  persistUserTemplates()

  // vrati na built-in default
  emailModal.value.templateKey = builtinTemplates[0]?.key || 'intro'
  localStorage.setItem('emailTemplateKey', emailModal.value.templateKey)
  applyEmailTemplate(emailModal.value.templateKey)
}

const followUpModal = ref({
  open: false,
  id: null,
  title: '',
  date: '',
  reminderEnabled: false,
  reminderSendAt: '', // "YYYY-MM-DDTHH:mm"
})


const openFollowUp = (collab) => {
  followUpModal.value.open = true
  followUpModal.value.id = collab.id
  followUpModal.value.title = collab.title || ''
  followUpModal.value.date = collab.follow_up_date || todayStr()

  followUpModal.value.reminderEnabled = !!collab.reminder_enabled
  followUpModal.value.reminderSendAt = collab.reminder_send_at
    ? collab.reminder_send_at.slice(0, 16).replace(' ', 'T')
    : ''
  followUpErrors.reminderSendAt = ''  
}


const closeFollowUp = () => {
  followUpModal.value.open = false
  followUpModal.value.id = null
  followUpModal.value.title = ''
  followUpModal.value.date = ''
  followUpModal.value.reminderEnabled = false
  followUpModal.value.reminderSendAt = ''
  followUpErrors.reminderSendAt = ''
}

const addDays = (days) => {
  const d = new Date()
  d.setHours(0, 0, 0, 0)
  d.setDate(d.getDate() + days)
  return d.toISOString().slice(0, 10)
}

const setFollowUpOffset = (days) => {
  followUpModal.value.date = addDays(days)
}

const clearFollowUpDate = () => {
  followUpModal.value.date = ''
}

const quickFollowUp = async (collab, type) => {
  const date = type === 'today' ? addDays(0) : addDays(1)
  await updateFollowUp(collab.id, date)
}

const saveFollowUp = async () => {
  followUpErrors.reminderSendAt = ''

  if (followUpModal.value.reminderEnabled && !followUpModal.value.reminderSendAt) {
    followUpErrors.reminderSendAt = 'Pick reminder date & time.'
    return
  }

  await updateFollowUp(followUpModal.value.id, followUpModal.value.date)
  closeFollowUp()
}




const updateFollowUp = async (id, follow_up_date) => {
  try {
    await axios.patch(`/collaborations/${id}/follow-up`, {
  follow_up_date,
  reminder_enabled: !!followUpModal.value.reminderEnabled,
  reminder_send_at: followUpModal.value.reminderEnabled
    ? followUpModal.value.reminderSendAt
    : null,
})


    // 1) refresh collaborations table
    await fetchCollaborations()

    // 2) refresh "Upcoming scheduled" reminders list
    await fetchUpcomingReminders()
  } catch (e) {
    console.error('follow-up failed:', e?.response?.data || e)
  }
}



const sortedSponsors = computed(() => {
  return [...sponsors.value].sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
})

const formatSponsorDate = (date) => {
  if (!date) return ''

  return new Date(date).toLocaleDateString('en-GB', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}

// --- Reminders (global + upcoming) ---
const reminderTime = ref(page.props.auth?.user?.reminder_time ?? '08:30')
const reminderDaysBefore = ref(page.props.auth?.user?.reminder_days_before ?? 0)

const upcomingReminders = ref([])
const remindersLoading = ref(false)
const remindersSaving = ref(false)
const rebuildLoading = ref(false)

const fetchUpcomingReminders = async () => {
  remindersLoading.value = true
  try {
    const { data } = await axios.get('/reminders/upcoming')

    upcomingReminders.value = data.sort((a, b) => {
      const ax = a.expired ? 1 : 0
      const bx = b.expired ? 1 : 0
      if (ax !== bx) return ax - bx

      const ad = a.send_at ? new Date(a.send_at.replace(' ','T')) : 0
      const bd = b.send_at ? new Date(b.send_at.replace(' ','T')) : 0

      return ad - bd
    })
  } finally {
    remindersLoading.value = false
  }
}


const saveReminderSettings = async () => {
  remindersSaving.value = true
  try {
    await axios.patch('/settings/reminders', {
      reminders_enabled: !!remindersEnabled.value,
      reminder_time: reminderTime.value,
      reminder_days_before: Number(reminderDaysBefore.value),
    })
    await fetchUpcomingReminders()
  } catch (e) {
    console.error('settings/reminders failed', e?.response?.data || e)
  } finally {
    remindersSaving.value = false
  }
}

const rebuildReminders = async () => {
  rebuildLoading.value = true
  try {
    await axios.post('/reminders/rebuild')
    await fetchUpcomingReminders()
  } catch (e) {
    console.error('reminders/rebuild failed', e?.response?.data || e)
  } finally {
    rebuildLoading.value = false
  }
}


const fmtDate = (ymd) => {
  if (!ymd) return ''
  const d = new Date(ymd + 'T00:00:00')
  return new Intl.DateTimeFormat('en-GB', { day:'2-digit', month:'short' }).format(d) // "10 Feb"
}

const fmtDateTime = (dt) => {
  if (!dt) return ''
  const d = new Date(dt.replace(' ', 'T'))
  return new Intl.DateTimeFormat('en-GB', {
    day:'2-digit', month:'short', hour:'2-digit', minute:'2-digit'
  }).format(d) // "11 Feb, 08:30"
}

const followUpValid = computed(() => {
  if (!followUpModal.value.reminderEnabled) return true
  return !!followUpModal.value.reminderSendAt
})


onMounted(async () => {
  loading.value = true
  loadError.value = ''
  const u = page.props.auth?.user
  if (u) {
    remindersEnabled.value = !!u.reminders_enabled
    dailyDigestEnabled.value = !!u.daily_digest_enabled
    dailyDigestTime.value = u.daily_digest_time || '08:30'
  }

  try {
    await fetchUpcomingReminders()
    await fetchSponsors()
    await fetchCollaborations()
    await syncSearchFromUrl()
    await loadUserTemplates()
  } catch (e) {
    console.error(e)
    loadError.value = 'Failed to load data. Please refresh.'
  } finally {
    loading.value = false
  }
})

watch(
  () => page.url,
  async () => {
    await syncSearchFromUrl()
  }
)


</script>