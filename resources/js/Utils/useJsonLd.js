import { watch, nextTick } from 'vue'
import { usePage } from '@inertiajs/vue3'

/**
 * JSON-LD injector for Inertia SPA.
 * - No <script> in Vue templates (Vite restriction)
 * - Upserts exactly one script tag in <head>
 * - Updates on url change + json change
 * - Does NOT remove on unmount (layout stays mounted anyway)
 */
export function useJsonLd(jsonLdRef, id = 'digitallab-jsonld') {
  const page = usePage()

  const upsert = async (val) => {
    await nextTick()

    const existing = document.querySelector(`script[data-jsonld="${id}"]`)

    if (!val) {
      if (existing && existing.parentNode) existing.parentNode.removeChild(existing)
      return
    }

    const json = JSON.stringify(val)

    let el = existing
    if (!el) {
      el = document.createElement('script')
      el.type = 'application/ld+json'
      el.setAttribute('data-jsonld', id)
      document.head.appendChild(el)
    }

    // always overwrite
    el.text = json
  }

  // update when json changes
  watch(jsonLdRef, (val) => {
    upsert(val)
  }, { deep: true, immediate: true })

  // update when Inertia url changes (SPA navigation)
  watch(() => page.url, () => {
    upsert(jsonLdRef.value)
  })
}
