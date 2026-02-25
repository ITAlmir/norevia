<template>
  <MainLayout>
    <div class="max-w-7xl mx-auto px-4 py-8">
      <h1 class="text-3xl font-bold text-white mb-2">Super Admin Panel</h1>
      <p class="text-gray-400 mb-8">Manage users and system settings</p>

      <!-- Quick Actions -->
      <div class="bg-slate-800/50 rounded-xl p-6 border border-slate-700 mb-8">
        <h2 class="text-xl font-bold text-gray-100 mb-4">User Management</h2>
        <div class="flex flex-wrap gap-4">
          <a href="/super-admin/users" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
            Manage Users
          </a>
          <a href="/content-admin" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">
            Content Admin Panel
          </a>
          <a href="/dashboard" class="px-6 py-3 bg-slate-700 hover:bg-slate-600 text-gray-300 rounded-lg font-medium">
            Back to Dashboard
          </a>
        </div>
      </div>

      <!-- Recent Users -->
      <div class="bg-slate-800/50 rounded-xl p-6 border border-slate-700">
        <h2 class="text-xl font-bold text-gray-100 mb-4">Recent Users</h2>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-slate-700">
            <thead>
              <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Email</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Role</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Joined</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
              <tr v-for="user in users" :key="user.id" class="hover:bg-slate-800/50">
                <td class="px-4 py-3 text-sm text-gray-300">{{ user.name }}</td>
                <td class="px-4 py-3 text-sm text-gray-300">{{ user.email }}</td>
                <td class="px-4 py-3">
                  <span :class="[
                    'px-2 py-1 text-xs rounded-full',
                    user.role === 'super_admin' ? 'bg-red-900/30 text-red-400' :
                    user.role === 'content_admin' ? 'bg-blue-900/30 text-blue-400' :
                    'bg-slate-700 text-gray-300'
                  ]">
                    {{ user.role }}
                  </span>
                </td>
                <td class="px-4 py-3 text-sm text-gray-500">{{ formatDate(user.created_at) }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'

const props = defineProps({
  users: Array,
  pages: Array
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}
</script>