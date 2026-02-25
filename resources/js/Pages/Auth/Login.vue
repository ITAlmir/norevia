<!-- resources/js/Pages/Auth/Login.vue -->
<template>
  <GuestLayout>
    <Head title="Log in" />

    <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
      {{ status }}
    </div>

    <form @submit.prevent="submit">
      <div>
        <label for="email" class="block text-sm font-medium text-gray-300">Email</label>
        <input
          id="email"
          type="email"
          class="mt-1 block w-full px-3 py-2 bg-slate-700 border border-slate-600 rounded-md text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
          v-model="form.email"
          required
          autofocus
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
          autocomplete="current-password"
        />
        <div v-if="form.errors.password" class="mt-2 text-sm text-red-400">
          {{ form.errors.password }}
        </div>
      </div>

      <div class="flex items-center justify-between mt-4">
        <label class="flex items-center">
          <input
            type="checkbox"
            class="rounded bg-slate-700 border-slate-600 text-blue-500 focus:ring-blue-500"
            name="remember"
            v-model="form.remember"
          />
          <span class="ml-2 text-sm text-gray-300">Remember me</span>
        </label>

        <a
          v-if="canResetPassword"
          :href="getRoute('password.request')"
          class="text-sm text-blue-400 hover:text-blue-300"
        >
          Forgot your password?
        </a>
      </div>

      <div class="flex items-center justify-end mt-4">
        <a
          :href="getRoute('register')"
          class="text-sm text-gray-300 hover:text-gray-100 mr-4"
        >
          Don't have an account?
        </a>

        <button
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
          :disabled="form.processing"
        >
          Log in
        </button>
      </div>
    </form>
  </GuestLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'

defineProps({
  canResetPassword: Boolean,
  status: String,
})

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

// Helper funkcija umjesto route()
const getRoute = (name, params = {}) => {
  if (window.route && typeof window.route === 'function') {
    return window.route(name, params);
  }
  
  const routes = {
    'login': '/login',
    'register': '/register',
    'password.request': '/forgot-password',
    'logout': '/logout',
    'dashboard': '/dashboard',
  };
  
  return routes[name] || '/';
}

const submit = () => {
  form.post(getRoute('login'), {
    onFinish: () => form.reset('password'),
  })
}
</script>