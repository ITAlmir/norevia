<!-- resources/js/Pages/SuperAdmin/Users.vue -->
<template>
  <MainLayout>
    <div class="max-w-7xl mx-auto px-4 py-8">
            <div class="flex items-center justify-between mb-6">

      <h1 class="text-3xl font-bold text-yellow-500 mb-2">User Management</h1>
        <Link
          href="/dashboard"
          class="px-4 py-2 rounded-lg font-medium text-white
                 bg-gradient-to-r from-blue-700 to-slate-800
                 hover:from-blue-600 hover:to-slate-700"
        >
          <- Back To Dashboard
        </Link>
            </div>
      <!-- Users Table -->
      <div class="bg-slate-800/50 rounded-xl border border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-slate-700">
            <thead class="bg-slate-900/50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">User</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Joined</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
              <tr v-for="user in users.data" :key="user.id" class="hover:bg-slate-800/30">
                <td class="px-6 py-4">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 bg-slate-700 rounded-full flex items-center justify-center">
                      <span class="text-gray-300 font-medium">{{ user.name.charAt(0) }}</span>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-300">{{ user.name }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-300">{{ user.email }}</td>
                <td class="px-6 py-4">
                  <span :class="[
                    'px-2 py-1 text-xs rounded-full',
                    user.role === 'super_admin' ? 'bg-red-900/30 text-red-400' :
                    user.role === 'content_admin' ? 'bg-blue-900/30 text-blue-400' :
                    'bg-slate-700 text-gray-300'
                  ]">
                    {{ user.role }}
                  </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ formatDate(user.created_at) }}</td>
                <td class="px-6 py-4">
                  <div class="flex space-x-2">
                    <select 
                      v-model="user.role" 
                      @change="updateUserRole(user)"
                      class="bg-slate-800 border border-slate-700 text-gray-300 text-sm rounded focus:ring-blue-500 focus:border-blue-500 px-2 py-1"
                    >
                      <option value="user">User</option>
                      <option value="content_admin">Content Admin</option>
                      <option value="super_admin">Super Admin</option>
                    </select>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-slate-700">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-400">
              Showing {{ users.from }} to {{ users.to }} of {{ users.total }} users
            </div>
            <div class="flex space-x-2">
              <Link 
                v-for="link in users.links" 
                :key="link.label"
                :href="link.url || '#'"
                :class="[
                  'px-3 py-1 rounded-md text-sm',
                  link.active ? 'bg-blue-600 text-white' : 'bg-slate-700 text-gray-300 hover:bg-slate-600',
                  !link.url ? 'text-gray-500 cursor-not-allowed' : ''
                ]"
                v-html="link.label"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
import { Link } from '@inertiajs/vue3'
import axios from 'axios' // DODAJ OVO!

const props = defineProps({
  users: Object
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString()
}

const updateUserRole = async (user) => {
  try {
    // OVO JE ISPRAVLJEN URL - koristi '/super-admin/users/{id}/role'
    const response = await axios.post(`/super-admin/users/${user.id}/role`, {
      role: user.role
    })
    
    if (response.data.success) {
      // Možeš dodati notifikaciju ovdje
      console.log(`User role updated to ${user.role}`)
      // Ne treba refresh jer smo već promijenili user.role u v-model
    }
  } catch (error) {
    console.error('Error updating user role:', error)
    
    // Vrati na staru vrijednost
    window.location.reload()
    
    // Možeš dodati bolju notifikaciju
    alert('Error updating user role. Please try again.')
  }
}
</script>