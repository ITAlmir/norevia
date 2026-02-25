import { ref, computed } from 'vue'

const KEY = 'norevia_consent_v1'

// consent state
const consent = ref(load() || {
  decided: false,
  necessary: true,
  analytics: false,
  marketing: false,
  ts: null,
})

// UI state (global)
const settingsOpen = ref(false)

export function useConsent() {
  const decided = computed(() => !!consent.value.decided)

  const setAll = (mode) => {
    consent.value = {
      decided: true,
      necessary: true,
      analytics: mode === 'accept',
      marketing: mode === 'accept',
      ts: Date.now(),
    }
    save(consent.value)
  }

  const setCustom = ({ analytics, marketing }) => {
    consent.value = {
      decided: true,
      necessary: true,
      analytics: !!analytics,
      marketing: !!marketing,
      ts: Date.now(),
    }
    save(consent.value)
  }

  return { consent, decided, setAll, setCustom }
}

// ✅ global UI helpers
export function useConsentUI() {
  const open = () => { settingsOpen.value = true }
  const close = () => { settingsOpen.value = false }
  return { settingsOpen, open, close }
}

function load() {
  try {
    const raw = localStorage.getItem(KEY)
    if (!raw) return null
    const obj = JSON.parse(raw)
    if (!obj || typeof obj !== 'object') return null
    return {
      decided: !!obj.decided,
      necessary: true,
      analytics: !!obj.analytics,
      marketing: !!obj.marketing,
      ts: obj.ts || null,
    }
  } catch {
    return null
  }
}

function save(obj) {
  try {
    localStorage.setItem(KEY, JSON.stringify(obj))
  } catch {}
}
