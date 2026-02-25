<!-- resources/js/Pages/Auth/Register.vue -->
<template>
  <GuestLayout>
    <Head title="Register" />

    <form @submit.prevent="submit">
      <div>
        <label for="name" class="block text-sm font-medium text-gray-300">Name</label>
        <input
          id="name"
          type="text"
          class="mt-1 block w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-md text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
          v-model="form.name"
          required
          autofocus
          autocomplete="name"
        />
        <div v-if="form.errors.name" class="mt-2 text-sm text-red-400">
          {{ form.errors.name }}
        </div>
      </div>

      <div class="mt-4">
        <label for="email" class="block text-sm font-medium text-gray-300">Email</label>
        <input
          id="email"
          type="email"
          class="mt-1 block w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-md text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
          v-model="form.email"
          required
          autocomplete="username"
        />
        <div v-if="form.errors.email" class="mt-2 text-sm text-red-400">
          {{ form.errors.email }}
        </div>
      </div>

      <div class="mt-4">
        <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
        <input
          id="password"
          type="password"
          class="mt-1 block w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-md text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
          v-model="form.password"
          required
          autocomplete="new-password"
        />
        <div v-if="form.errors.password" class="mt-2 text-sm text-red-400">
          {{ form.errors.password }}
        </div>
      </div>

      <div class="mt-4">
        <label for="password_confirmation" class="block text-sm font-medium text-gray-300">Confirm Password</label>
        <input
          id="password_confirmation"
          type="password"
          class="mt-1 block w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-md text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
          v-model="form.password_confirmation"
          required
          autocomplete="new-password"
        />
        <div v-if="form.errors.password_confirmation" class="mt-2 text-sm text-red-400">
          {{ form.errors.password_confirmation }}
        </div>
      </div>

      <div class="flex items-center justify-end mt-4">
        <a
          :href="getRoute('login')"
          class="text-sm text-gray-300 hover:text-gray-100 mr-4"
        >
          Already registered?
        </a>

        <button
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
          :disabled="form.processing"
        >
          Register
        </button>
      </div>
    </form>
  </GuestLayout>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

// Helper funkcija umjesto route()
const getRoute = (name, params = {}) => {
  if (window.route && typeof window.route === 'function') {
    return window.route(name, params);
  }
  
  const routes = {
    'login': '/login',
    'register': '/register',
    'dashboard': '/dashboard',
  };
  
  return routes[name] || '/';
}

const submit = () => {
  form.post(getRoute('register'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  })
}
</script>