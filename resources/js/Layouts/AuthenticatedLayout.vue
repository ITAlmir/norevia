<!-- resources/js/Layouts/AuthenticatedLayout.vue -->
<template>
  <MainLayout>
    <div class="py-8">
      <slot />
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from './MainLayout.vue'
</script>