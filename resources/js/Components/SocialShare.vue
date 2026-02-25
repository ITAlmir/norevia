<!-- resources/js/Components/SocialShare.vue -->
<template>
  <div class="flex space-x-2">
    <!-- Facebook Share -->
    <a :href="`https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`" 
       target="_blank"
       class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm">
      Facebook
    </a>
    
    <!-- Twitter Share -->
    <a :href="`https://twitter.com/intent/tweet?url=${encodedUrl}&text=${encodedTitle}`" 
       target="_blank"
       class="px-3 py-1 bg-sky-500 hover:bg-sky-600 text-white rounded text-sm">
      Twitter
    </a>
    
    <!-- Copy URL -->
    <button @click="copyUrl" 
            class="px-3 py-1 bg-slate-600 hover:bg-slate-700 text-white rounded text-sm">
      Copy Link
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  url: {
    type: String,
    required: true
  },
  title: {
    type: String,
    default: 'Check this out!'
  }
})

const encodedUrl = computed(() => encodeURIComponent(props.url))
const encodedTitle = computed(() => encodeURIComponent(props.title))

const copyUrl = async () => {
  try {
    await navigator.clipboard.writeText(props.url)
    alert('URL copied to clipboard!')
  } catch (err) {
    console.error('Failed to copy:', err)
  }
}
</script>