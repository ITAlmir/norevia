<!-- resources/js/Pages/ContentAdmin/Pages/Create.vue -->
<template>
  <MainLayout>
    <div class="max-w-4xl mx-auto px-4 py-8">
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-white">Create New Page</h1>
        <p class="text-gray-400 mt-2">Create a new content page</p>
      </div>

<p class="text-gray-500 text-sm mt-1">
  Layout utiče na prikaz na public stranici (naslov, širina, tipografija, “related”).
</p>

      <!-- Form -->
      <div class="bg-slate-800/50 rounded-xl border border-slate-700 p-6">
        <form @submit.prevent="submitForm">
          <!-- Dropdown za type -->
      <label class="block text-gray-300 text-sm font-medium mb-2">Category</label>
<select v-model="form.page_type" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-gray-300">
  <option value="post">Post</option>
  <option value="news">News</option>
  <option value="gaming">Gaming</option>
  <option value="horoscope">Horoscope</option>
</select>
<label class="block text-gray-300 text-sm font-medium mb-2 mt-4">Content kind</label>
<select v-model="form.type" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-gray-300">
  <option value="page">Page</option>
  <option value="blog">Blog post</option>
</select>

<div v-if="form.type === 'blog'" class="mt-4">
  <label class="block text-gray-300 text-sm font-medium mb-2">Blog hub (topic)</label>
  <select v-model="form.topic" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-gray-300">
    <option value="cs2">CS2</option>
    <option value="pc-optimization">PC Optimization</option>
    <option value="creator-tools">Creator Tools</option>
    <option value="gaming">Gaming</option>
  </select>

  <p class="mt-2 text-xs text-slate-400">
    This controls where the post appears: /blog/{topic}/...
  </p>
</div>

<!-- Dropdown za layout -->
<label class="block text-gray-300 text-sm font-medium mb-2">Layout</label>
<select v-model="form.layout" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-gray-300">
  <option value="minimal">Minimal</option>
  <option value="classic">Classic</option>
  <option value="magazine">Magazine</option>
  <option value="hero">Hero</option>
</select>
          <!-- Title -->
          <div class="mb-6">
            <label class="block text-gray-300 text-sm font-medium mb-2">Title *</label>
            <input
              v-model="form.title"
              type="text"
              required
              class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Enter page title"
            >
          </div>

          <!-- Slug -->
          <div class="mb-6">
            <label class="block text-gray-300 text-sm font-medium mb-2">Slug (URL)</label>
            <input
              v-model="form.slug"
              type="text"
              class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="page-url-slug"
            >
            <p class="text-gray-500 text-sm mt-1">Leave empty to auto-generate from title</p>
          </div>

<details class="mb-6">
  <summary class="cursor-pointer text-gray-300 font-medium">
    Featured / Social image (optional)
  </summary>

  <!-- Featured Image Upload -->
          <div class="mb-6">
            <label class="block text-gray-300 text-sm font-medium mb-2">
              Featured Image
            </label>
            
            <!-- Image Preview -->
            <div v-if="imagePreview" class="mb-4">
              <img :src="imagePreview" alt="Preview" class="max-h-64 rounded-lg">
            </div>
            
            <!-- Upload Options -->
            <div class="space-y-4">
              <!-- File Upload -->
              <div class="border-2 border-dashed border-slate-700 rounded-lg p-6 text-center">
                <svg class="w-12 h-12 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="text-gray-400 mb-2">Upload an image (max 2MB)</p>
                <input
                  type="file"
                  ref="fileInput"
                  @change="handleImageUpload"
                  accept="image/*"
                  class="hidden"
                />
                <button
                  type="button"
                  @click="$refs.fileInput.click()"
                  class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                >
                  Select Image
                </button>
              </div>
              
              <!-- OR URL Input -->
              <div class="flex">
                <input
                  type="text"
                  v-model="imageUrl"
                  placeholder="Or enter image URL"
                  class="flex-1 px-4 py-2 bg-slate-900 border border-slate-700 text-gray-300 rounded-l-lg focus:outline-none"
                />
                <button
                  type="button"
                  @click="useImageUrl"
                  class="px-4 py-2 bg-slate-700 text-gray-300 hover:bg-slate-600 rounded-r-lg"
                >
                  Use URL
                </button>
              </div>
            </div>
            
            <!-- Image Caption -->
            <div class="mt-4">
              <label class="block text-gray-300 text-sm font-medium mb-2">
                Image Caption (optional)
              </label>
              <input
                type="text"
                v-model="form.image_caption"
                class="w-full px-4 py-2 bg-slate-900 border border-slate-700 text-gray-300 rounded-lg"
                placeholder="Caption for the image"
              />
            </div>
          </div>
</details>
          
          <!-- Blocks -->
<div class="bg-slate-800/50 rounded-xl border border-slate-700 p-6 mb-6">
  <div class="flex items-center justify-between mb-4">
    <h3 class="text-lg font-semibold text-gray-100">Content blocks</h3>
    <div class="flex gap-2">
      <button
        type="button"
        @click="addTextBlock"
        class="px-3 py-2 bg-slate-700 hover:bg-slate-600 text-gray-200 rounded-lg text-sm"
      >
        + Text
      </button>
      <button
        type="button"
        @click="addImageBlock"
        class="px-3 py-2 bg-slate-700 hover:bg-slate-600 text-gray-200 rounded-lg text-sm"
      >
        + Image
      </button>
      <button
  type="button"
  @click="addCtaBlock"
  class="px-3 py-2 rounded-lg text-sm font-medium
         bg-emerald-600/90 hover:bg-emerald-600 text-white
         shadow-sm hover:shadow-md transition-all duration-200"
>
  + CTA
</button>
    </div>
  </div>

  <div class="space-y-4">
    <div
      v-for="(block, i) in form.blocks"
      :key="i"
      class="p-4 rounded-xl border border-slate-700 bg-slate-900/40"
    >
      <!-- Block header -->
      <div class="flex items-center justify-between mb-3">
        <div class="text-sm text-gray-300 font-medium">
          Block #{{ i + 1 }} • <span class="text-gray-400">{{ block.type }}</span>
        </div>

        <div class="flex gap-2">
          <button
            type="button"
            @click="moveBlock(i, -1)"
            :disabled="i === 0"
            class="px-2 py-1 bg-slate-800 border border-slate-700 rounded text-xs text-gray-300 disabled:opacity-40"
          >
            ↑
          </button>
          <button
            type="button"
            @click="moveBlock(i, 1)"
            :disabled="i === form.blocks.length - 1"
            class="px-2 py-1 bg-slate-800 border border-slate-700 rounded text-xs text-gray-300 disabled:opacity-40"
          >
            ↓
          </button>
          <button
            type="button"
            @click="removeBlock(i)"
            class="px-2 py-1 bg-red-600 hover:bg-red-700 rounded text-xs text-white"
          >
            Delete
          </button>
        </div>
      </div>

      <!-- TEXT BLOCK -->
      <div v-if="block.type === 'text'" class="space-y-3">

  <!-- Formatting Toolbar -->
  <div class="flex flex-wrap gap-2 items-center">

    <!-- Color dropdown -->
    <select
      @change="applyColor(i, $event.target.value)"
      class="bg-slate-800 border border-slate-700 text-gray-300 text-xs px-3 py-1 rounded-lg"
    >
      <option value="">Text color</option>
      <option value="text-white">White</option>
      <option value="text-slate-200">Light</option>
      <option value="text-sky-300">Sky</option>
      <option value="text-emerald-300">Green</option>
      <option value="text-fuchsia-300">Pink</option>
      <option value="text-red-400">Red</option>
      <option value="text-yellow-300">Yellow</option>
    </select>

    <!-- Bold -->
    <button type="button"
      @click="wrapSelection(i, 'strong')"
      class="px-2 py-1 bg-slate-800 border border-slate-700 rounded text-xs">
      Bold
    </button>

    <!-- Link -->
    <button type="button"
      @click="insertLink(i)"
      class="px-2 py-1 bg-slate-800 border border-slate-700 rounded text-xs">
      Link
    </button>
    <!-- BR New Line -->
    <button type="button"
  @click="insertBreak(i)"
  class="px-2 py-1 bg-slate-800 border border-slate-700 rounded text-xs">
  BR
</button>
    <!-- Hashtag -->
    <button type="button"
      @click="insertHashtag(i)"
      class="px-2 py-1 bg-slate-800 border border-slate-700 rounded text-xs">
      #Tag
    </button>

  </div>

  <textarea
  :ref="(el) => setTextAreaRef(el, i)"
  v-model="block.content"
  rows="6"
  class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-gray-300"
  placeholder="Write text…"
/>

</div>

      <!-- IMAGE BLOCK -->
      <div v-else-if="block.type === 'image'" class="space-y-3">
        <div v-if="block.src" class="mb-2">
          <img :src="block.src" class="max-h-64 rounded-lg" />
        </div>

        <!-- Upload -->
        <div class="border-2 border-dashed border-slate-700 rounded-lg p-4 text-center">
          <p class="text-gray-400 mb-2 text-sm">Upload image</p>

          <input
            type="file"
            accept="image/*"
            class="hidden"
            :ref="(el) => setBlockFileRef(el, i)"
            @change="(e) => handleBlockImageUpload(e, i)"
          />

          <button
            type="button"
            class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-gray-200 rounded-lg text-sm"
            @click="openBlockFilePicker(i)"
          >
            Select Image
          </button>
        </div>

        <!-- OR URL -->
        <div class="flex gap-2">
          <input
            v-model="block.src"
            type="text"
            placeholder="Or paste image URL"
            class="flex-1 bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-gray-300"
          />
        </div>

        <input
          v-model="block.caption"
          type="text"
          placeholder="Caption (optional)"
          class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-gray-300"
        />

        <button
          v-if="block.src"
          type="button"
          class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm"
          @click="block.src=''; block.caption=''"
        >
          Remove image
        </button>
      </div>

      <!-- CTA BLOCK -->
<div v-else-if="block.type === 'cta'" class="space-y-3">
  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
    <div>
      <label class="block text-xs text-gray-400 mb-1">Title (optional)</label>
      <input
        v-model="block.title"
        type="text"
        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-gray-300"
        placeholder="Download Ping Tool"
      />
    </div>

    <div>
      <label class="block text-xs text-gray-400 mb-1">Button label</label>
      <input
        v-model="block.label"
        type="text"
        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-gray-300"
        placeholder="Download now"
      />
    </div>
  </div>

  <div>
    <label class="block text-xs text-gray-400 mb-1">URL *</label>
    <input
  v-model="block.url"
  type="text"
  @blur="onCtaUrlBlur(block)"
  class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-gray-300"
  placeholder="https://norevia.app/downloads/..."
/>
    <p class="text-xs text-slate-500 mt-1">
      Tip: Use UTM for socials (utm_source=...).
    </p>
  </div>

  <div>
    <label class="block text-xs text-gray-400 mb-1">Text (optional)</label>
    <textarea
      v-model="block.text"
      rows="3"
      class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-gray-300"
      placeholder="Short pitch above the button..."
    />
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
    <div>
      <label class="block text-xs text-gray-400 mb-1">Variant</label>
      <select
        v-model="block.variant"
        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-gray-300"
      >
        <option value="primary">Primary</option>
        <option value="secondary">Secondary</option>
        <option value="outline">Outline</option>
      </select>
    </div>

    <div>
      <label class="block text-xs text-gray-400 mb-1">Note (optional)</label>
      <input
        v-model="block.note"
        type="text"
        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-gray-300"
        placeholder="Windows 10/11 • No install • No tracking"
      />
    </div>
  </div>

  <!-- Mini preview -->
  <div class="pt-3 border-t border-slate-700">
    <div class="text-xs text-slate-400 mb-2">Preview</div>

    <div class="rounded-xl border border-slate-700 bg-slate-900/40 p-4">
      <div v-if="block.title" class="text-white font-semibold">{{ block.title }}</div>
      <div v-if="block.text" class="mt-1 text-sm text-slate-300 whitespace-pre-line break-words">{{ block.text }}</div>

      <div class="mt-3">
        <span
          class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold"
          :class="ctaPreviewClass(block.variant)"
        >
          {{ block.label || 'Open' }} →
        </span>
      </div>

      <div v-if="block.note" class="mt-2 text-xs text-slate-400">{{ block.note }}</div>
    </div>
  </div>
</div>
      <!-- Unknown block type fallback (optional) -->
      <div v-else class="text-sm text-gray-400">
        Unknown block type: {{ block.type }}
      </div>
    </div>
  </div>
</div>

          <!-- SEO Meta -->
          <div class="mb-6">
            <label class="block text-gray-300 text-sm font-medium mb-2">
              SEO Meta Title (optional)
            </label>
            <input
              type="text"
              v-model="form.meta_title"
              class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-gray-300"
              placeholder="Meta title for search engines"
            />
          </div>

          <div class="mb-6">
            <label class="block text-gray-300 text-sm font-medium mb-2">
              SEO Meta Description (optional)
            </label>
            <textarea
              v-model="form.meta_description"
              rows="3"
              class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-gray-300"
              placeholder="Meta description for search engines"
            ></textarea>
          </div>

          <!-- Status -->
          <div class="mb-6">
            <label class="block text-gray-300 text-sm font-medium mb-2">Status</label>
            <select
              v-model="form.status"
              class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="draft">Draft</option>
              <option value="published">Published</option>
            </select>
          </div>

          <!-- Buttons -->
          <div class="flex justify-end space-x-4">
            <a
              href="/content-admin/pages"
              class="px-6 py-3 border border-slate-600 text-gray-300 rounded-lg font-medium hover:bg-slate-700"
            >
              Cancel
            </a>
            <button
              type="submit"
              :disabled="loading"
              class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium disabled:opacity-50"
            >
              <span v-if="loading">Creating...</span>
              <span v-else>Create Page</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
import { useForm } from '@inertiajs/vue3'
import axios from 'axios'
import { ref, watch, onMounted } from 'vue'

onMounted(() => {
  if (form.type === 'blog' && !form.topic) {
    form.topic = props.prefill?.topic ?? 'cs2'
  }
})

const props = defineProps({
  prefill: { type: Object, default: () => ({}) },
})

const form = useForm({
  title: '',
  slug: '',

  // ✅ single source of truth
  status: props.prefill?.status ?? 'draft',

  // category + layout
  page_type: 'post',
  layout: 'minimal',

  // blog routing
  type: props.prefill?.type ?? 'page',
  topic: props.prefill?.topic ?? null,

  // fallback content (nullable)
  content: '',

  // media + seo
  featured_image: '',
  image_caption: '',
  meta_title: '',
  meta_description: '',

  blocks: [{ type: 'text', content: '' }],
})


const loading = ref(false)
const imagePreview = ref('')
const imageUrl = ref('')
const blockFileInputs = ref({})

const setBlockFileRef = (el, i) => {
  if (el) blockFileInputs.value[i] = el
}

const openBlockFilePicker = (i) => {
  blockFileInputs.value[i]?.click()
}


watch(() => form.type, (v) => {
  if (v === 'blog') {
    if (!form.topic) form.topic = props.prefill?.topic ?? 'cs2'
    // keep category meaningful for blog posts
    if (form.page_type === 'post') form.page_type = 'gaming'
  } else {
    form.topic = null
  }
})

const ensureBlocksArray = () => {
  if (!Array.isArray(form.blocks)) form.blocks = []
}

const addCtaBlock = () => {
  ensureBlocksArray()
  form.blocks.push({
    type: 'cta',
    title: 'Download Ping Tool',
    text: 'Test your real ping and packet loss in seconds.',
    label: 'Download now',
    url: 'https://norevia.app/downloads/norevia-ping-tool',
    variant: 'primary', // primary | secondary | outline
    note: 'Windows 10/11 • No install • No tracking',
  })
}

function normalizeUrl(input) {
  const s = String(input || '').trim()
  if (!s) return ''

  // ukloni leading emoji/strelice i slične stvari ako user zalijepi iz posta
  const cleaned = s.replace(/^[^\w]+/g, '').trim()

  // ako je relative (/downloads/...) pretvori u full
  if (cleaned.startsWith('/')) return `https://norevia.app${cleaned}`

  // ako nema schema, dodaj https
  if (!/^https?:\/\//i.test(cleaned)) return `https://${cleaned}`

  return cleaned
}

function onCtaUrlBlur(block) {
  block.url = normalizeUrl(block.url)
}

const ctaPreviewClass = (variant) => {
  if (variant === 'secondary') return 'border border-slate-600 bg-slate-800 text-white'
  if (variant === 'outline') return 'border border-emerald-500/60 text-emerald-300 bg-transparent'
  return 'bg-gradient-to-r from-emerald-600 to-sky-600 text-white'
}

const handleBlockImageUpload = async (event, i) => {
  const file = event.target.files?.[0]
  if (!file) return

  if (file.size > 150 * 1024 * 1024) {
    alert('File size must be less than 150MB')
    event.target.value = ''
    return
  }

  // preview
  const reader = new FileReader()
  reader.onload = async (e) => {
    form.blocks[i].src = e.target.result

    try {
      const url = await uploadImage(file)   // <-- BITNO
      form.blocks[i].src = url
    } catch (err) {
      console.error(err?.response?.data || err)
      alert('Upload failed, ostaje preview.')
    } finally {
      event.target.value = ''
    }
  }

  reader.readAsDataURL(file)
}


// ✅ BLOCK FUNKCIJE (MORAJU BITI OVDE)
const addTextBlock = () => form.blocks.push({ type: 'text', content: '' })
const addImageBlock = () => form.blocks.push({ type: 'image', src: '', caption: '' })

const removeBlock = (i) => form.blocks.splice(i, 1)

const moveBlock = (i, dir) => {
  const j = i + dir
  if (j < 0 || j >= form.blocks.length) return
  const tmp = form.blocks[i]
  form.blocks[i] = form.blocks[j]
  form.blocks[j] = tmp
}

// ✅ FEATURED IMAGE upload (kako si imao)
const handleImageUpload = (event) => {
  const file = event.target.files[0]
  if (!file) return

  if (file.size > 150 * 1024 * 1024) {
    alert('File size must be less than 150MB')
    return
  }

  const reader = new FileReader()
  reader.onload = (e) => {
    imagePreview.value = e.target.result
    form.featured_image = e.target.result
    event.target.value = ''
  }
  reader.readAsDataURL(file)
}

const useImageUrl = () => {
  if (imageUrl.value) {
    form.featured_image = imageUrl.value
    imagePreview.value = imageUrl.value
    imageUrl.value = ''
  }
}

// ✅ validacija: mora postojati bar neki blok sa sadržajem
const hasAnyBlockContent = () => {
  return form.blocks.some(b => {
    if (b.type === 'text') return (b.content || '').trim().length > 0
    if (b.type === 'image') return (b.src || '').trim().length > 0
    if (b.type === 'cta') return (b.url || '').trim().length > 0
    return false
  })
}

// ✅ fallback: ako želiš, prije submit-a napuni content iz text blokova
const syncContentFromBlocks = () => {
  const textParts = form.blocks
    .filter(b => b.type === 'text' && (b.content || '').trim())
    .map(b => b.content.trim())
  form.content = textParts.join("\n\n")
}

const submitForm = async () => {
  loading.value = true

  // Auto slug
  if (!form.slug && form.title) {
    form.slug = form.title.toLowerCase()
      .replace(/[^\w\s-]/g, '')
      .replace(/\s+/g, '-')
      .replace(/--+/g, '-')
      .trim()
  }

  // ✅ provjera blokova (umjesto required content)
  if (!hasAnyBlockContent()) {
    loading.value = false
    alert('Dodaj bar neki tekst ili sliku u blokove prije snimanja.')
    return
  }

  // ✅ fallback content (nije obavezno, ali korisno)
  syncContentFromBlocks()

  // Upload featured image
  if (form.featured_image && form.featured_image.startsWith('data:image')) {
    try {
      const uploadedUrl = await uploadImage(form.featured_image)
      form.featured_image = uploadedUrl
    } catch (error) {
      console.error('Image upload failed:', error)
      alert('Image upload failed. Page will be saved without image.')
      form.featured_image = ''
    }
  }

  // Submit
  form.post('/content-admin/pages', {
    preserveScroll: true,
    onFinish: () => { loading.value = false },
    onError: (errors) => {
  alert('Error creating page:\n' + JSON.stringify(errors, null, 2))
}
  })
}

const uploadImage = async (file) => {
  const formData = new FormData()
  formData.append('image', file, file.name)

  const response = await axios.post('/upload-image', formData, {
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json',
    },
  })

  return response.data.url
}

const dataURLtoBlob = (dataURL) => {
  const arr = dataURL.split(',')
  const mime = arr[0].match(/:(.*?);/)[1]
  const bstr = atob(arr[1])
  let n = bstr.length
  const u8arr = new Uint8Array(n)
  while (n--) u8arr[n] = bstr.charCodeAt(n)
  return new Blob([u8arr], { type: mime })
}

const getTA = (i) => textAreaRefs.value[i]

const wrapSelection = (i, tag) => {
  const ta = getTA(i)
  if (!ta) return

  const start = ta.selectionStart
  const end = ta.selectionEnd
  const text = form.blocks[i].content || ''
  const selected = text.substring(start, end)
  if (!selected) return

  const wrapped = `<${tag}>${selected}</${tag}>`
  form.blocks[i].content = text.substring(0, start) + wrapped + text.substring(end)
}

const applyColor = (i, className) => {
  if (!className) return
  const ta = getTA(i)
  if (!ta) return

  const start = ta.selectionStart
  const end = ta.selectionEnd
  const text = form.blocks[i].content || ''
  const selected = text.substring(start, end)
  if (!selected) return

  const wrapped = `<span class="${className}">${selected}</span>`
  form.blocks[i].content = text.substring(0, start) + wrapped + text.substring(end)
}

const insertLink = (i) => {
  let url = prompt('Enter URL:')
  if (!url) return

  if (!/^https?:\/\//i.test(url)) url = 'https://' + url

  const ta = getTA(i)
  if (!ta) return

  const start = ta.selectionStart
  const end = ta.selectionEnd
  const text = form.blocks[i].content || ''
  const selected = text.substring(start, end) || url

  const link = `<a href="${url}" class="text-sky-300 underline underline-offset-2 hover:text-sky-200 break-words" target="_blank" rel="nofollow noopener noreferrer">${selected}</a>`
  form.blocks[i].content = text.substring(0, start) + link + text.substring(end)
}

const insertHashtag = (index) => {
  const tag = prompt('Enter hashtag (without #):')
  if (!tag) return

  form.blocks[index].content += ` #${tag}`
}

const insertBreak = (i) => {
  const ta = getTA(i)
  if (!ta) {
    form.blocks[i].content = (form.blocks[i].content || '') + '<br>'
    return
  }

  const start = ta.selectionStart
  const end = ta.selectionEnd
  const text = form.blocks[i].content || ''
  const br = '<br>'

  form.blocks[i].content = text.substring(0, start) + br + text.substring(end)
}
const textAreaRefs = ref({})

const setTextAreaRef = (el, i) => {
  if (el) textAreaRefs.value[i] = el
}
</script>
