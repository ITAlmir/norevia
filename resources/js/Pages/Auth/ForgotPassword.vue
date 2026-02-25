<!-- resources/js/Pages/Auth/ForgotPassword.vue -->
<template>
  <GuestLayout>
    <Head title="Forgot Password" />

    <div class="mb-4 text-sm text-gray-300">
      Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.
    </div>

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
        />
        <div v-if="form.errors.email" class="mt-2 text-sm text-red-400">
          {{ form.errors.email }}
        </div>
      </div>

      <div class="flex items-center justify-end mt-4">
        <button
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
          :disabled="form.processing"
        >
          Email Password Reset Link
        </button>
      </div>
    </form>
  </GuestLayout>
</template>

<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import GuestLayout from '@/Layouts/GuestLayout.vue'

defineProps({
  status: String,
})

const form = useForm({
  email: '',
})

const submit = () => {
  form.post('/forgot-password')
}
</script>