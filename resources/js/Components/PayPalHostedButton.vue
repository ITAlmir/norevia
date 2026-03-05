<script setup>
import { onMounted, nextTick } from 'vue'

const props = defineProps({
  hostedButtonId: { type: String, required: true },
  containerId: { type: String, required: true },
})

function sleep(ms) {
  return new Promise(r => setTimeout(r, ms))
}

async function waitForPayPal(ms = 8000) {
  const start = Date.now()
  while (Date.now() - start < ms) {
    if (window.paypal?.HostedButtons) return true
    await sleep(80)
  }
  return false
}

onMounted(async () => {
  await nextTick()

  const ok = await waitForPayPal()
  if (!ok) return

  const selector = `#${props.containerId}`
  const el = document.querySelector(selector)
  if (!el) return

  // clear previous render (Inertia navigation)
  el.innerHTML = ''

  window.paypal.HostedButtons({
    hostedButtonId: props.hostedButtonId,
  }).render(selector)
})
</script>

<template>
  <div class="paypal-wrap max-w-full overflow-x-auto">
    <div :id="containerId" class="max-w-full"></div>
  </div>
</template>

<style scoped>
.paypal-wrap :deep(*) {
  writing-mode: horizontal-tb !important;
  letter-spacing: normal !important;
  word-break: normal !important;
  white-space: normal !important;
  text-transform: none !important;
}
</style>