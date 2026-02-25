<!-- resources/js/Pages/ContentAdmin/Pages/Edit.vue -->
<template>
  <MainLayout>
    <div class="max-w-4xl mx-auto px-4 py-8">
      <!-- HEADER SA HIGHLIGHT -->
      <div class="mb-8 p-6 bg-slate-800/40 rounded-xl border border-slate-700">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-white mb-2">Edit Page</h1>
            <p class="text-gray-400">
              Editing: <span class="text-white font-medium">{{ page.title }}</span>
            </p>
          </div>
          
          <!-- ACTION BUTTONS -->
          <div class="flex space-x-3">
            <!-- BACK BUTTON -->
            <Link 
              href="/content-admin/pages" 
              class="px-4 py-2.5 text-gray-400 hover:text-white border border-slate-700 rounded-lg hover:bg-slate-800 transition-colors flex items-center"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
              </svg>
              Back to Pages
            </Link>
            
            <!-- SAVE CHANGES BUTTON - GORNJI -->
            <button
              type="button"
              @click="submitForm"
              :disabled="processing"
              class="px-5 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors hover:scale-105 disabled:opacity-50 flex items-center"
            >
              <svg v-if="processing" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
              </svg>
              {{ processing ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </div>
      </div>

      <!-- SUCCESS MESSAGE -->
      <div v-if="$page.props.flash.success" class="mb-6 p-4 bg-green-900/30 text-green-400 rounded-lg border border-green-800">
        {{ $page.props.flash.success }}
      </div>

      <!-- FORM -->
      <form @submit.prevent="submitForm" class="space-y-6">
        <!-- TITLE -->
        <div class="bg-slate-800/50 rounded-xl border border-slate-700 p-6">
          <label for="title" class="block text-sm font-medium text-gray-300 mb-2">
            Page Title *
          </label>
          <input
            type="text"
            id="title"
            v-model="form.title"
            required
            class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-lg text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          />
        </div>
        <!-- Content kind + Blog hub -->
         <div class="bg-slate-800/50 rounded-xl border border-slate-700 p-6">
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium text-gray-300 mb-2">Content kind</label>
      <select
        v-model="form.type"
        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-gray-300"
      >
        <option value="page">Page</option>
        <option value="blog">Blog post</option>
      </select>
    </div>

    <div v-if="form.type === 'blog'">
      <label class="block text-sm font-medium text-gray-300 mb-2">Blog hub (topic)</label>
      <select
        v-model="form.topic"
        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-gray-300"
      >
        <option value="cs2">CS2</option>
        <option value="pc-optimization">PC Optimization</option>
        <option value="creator-tools">Creator Tools</option>
        <option value="gaming">Gaming</option>
      </select>
      <p class="mt-2 text-xs text-slate-400">Controls URL: /blog/{topic}/{slug}</p>
    </div>
  </div>
</div>

        <!-- SLUG & URL -->
        <div class="bg-slate-800/50 rounded-xl border border-slate-700 p-6">
          <label for="slug" class="block text-sm font-medium text-gray-300 mb-2">
            URL Slug *
          </label>
          <div class="flex items-center mb-3">
            <span class="text-gray-500 mr-2">{{ baseUrl }}/pages/</span>
            <input
              type="text"
              id="slug"
              v-model="form.slug"
              required
              class="flex-1 px-4 py-3 bg-slate-900 border border-slate-700 rounded-lg text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
          
          <!-- COPY URL SECTION -->
          <div class="mt-4 p-4 bg-slate-900/50 rounded-lg border border-slate-700">
            <p class="text-sm font-medium text-gray-300 mb-2">Full URL:</p>
            <div class="flex items-center">
              <input
                type="text"
                readonly
                :value="fullUrl"
                class="flex-1 bg-slate-800 text-gray-300 text-sm px-4 py-2 rounded-l-lg border border-r-0 border-slate-700 focus:outline-none"
              />
              <button
                type="button"
                @click="copyUrl"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-r-lg border border-blue-700 transition-colors"
              >
                Copy URL
              </button>
            </div>
          </div>
        </div>

        <!-- FEATURED IMAGE -->
        <div class="bg-slate-800/50 rounded-xl border border-slate-700 p-6">
          <label class="block text-sm font-medium text-gray-300 mb-2">
            Featured Image
          </label>
          
          <!-- IMAGE PREVIEW -->
          <div v-if="imagePreview" class="mb-4">
            <div class="relative inline-block">
              <img :src="imagePreview" alt="Preview" class="max-h-64 rounded-lg shadow-lg">
              <button
                type="button"
                @click="removeImage"
                class="absolute top-2 right-2 bg-red-600 text-white p-2 rounded-full hover:bg-red-700 transition-colors"
                title="Remove image"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>
          
          <!-- IMAGE UPLOAD -->
          <div class="space-y-4">
            <div class="border-2 border-dashed border-slate-700 rounded-lg p-8 text-center hover:border-blue-500 transition-colors">
              <svg class="w-12 h-12 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <p class="text-gray-400 mb-2">Change featured image</p>
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
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
              >
                Select Image
              </button>
            </div>
            
            <!-- OR URL INPUT -->
            <div class="flex">
              <input
                type="text"
                v-model="imageUrl"
                placeholder="Enter image URL (optional)"
                class="flex-1 px-4 py-2 bg-slate-900 border border-slate-700 text-gray-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
              <button
                type="button"
                @click="useImageUrl"
                class="px-4 py-2 bg-slate-700 text-gray-300 hover:bg-slate-600 rounded-r-lg transition-colors"
              >
                Use URL
              </button>
            </div>
          </div>
          
          <!-- IMAGE CAPTION -->
          <div class="mt-4">
            <label for="image_caption" class="block text-sm font-medium text-gray-300 mb-2">
              Image Caption
            </label>
            <input
              type="text"
              id="image_caption"
              v-model="form.image_caption"
              class="w-full px-4 py-2 bg-slate-900 border border-slate-700 text-gray-300 rounded-lg"
              placeholder="Optional caption for the image"
            />
          </div>
        </div>

        <!-- CONTENT BLOCKS -->
<div class="bg-slate-800/50 rounded-xl border border-slate-700 p-6">
  <div class="flex items-center justify-between mb-4">
    <h3 class="text-lg font-semibold text-gray-100">Content blocks</h3>
    <div class="flex gap-2">
      <button
  type="button"
  @click="addCtaBlock"
  class="px-3 py-2 rounded-lg text-sm font-medium bg-emerald-600/90 hover:bg-emerald-600 text-white"
>
  + CTA
</button>

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
    </div>
  </div>

  <div class="space-y-4">
    <div
      v-for="(block, i) in form.blocks"
      :key="i"
      class="p-4 rounded-xl border border-slate-700 bg-slate-900/40"
    >
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
      class="px-2 py-1 bg-slate-800 border border-slate-700 rounded text-xs text-gray-200">
      Bold
    </button>

    <!-- Link -->
    <button type="button"
      @click="insertLink(i)"
      class="px-2 py-1 bg-slate-800 border border-slate-700 rounded text-xs text-gray-200">
      Link
    </button>

    <!-- Hashtag -->
    <button type="button"
      @click="insertHashtag(i)"
      class="px-2 py-1 bg-slate-800 border border-slate-700 rounded text-xs text-gray-200">
      #Tag
    </button>

    <!-- BR -->
    <button type="button"
      @click="insertBreak(i)"
      class="px-2 py-1 bg-slate-800 border border-slate-700 rounded text-xs text-gray-200">
      BR
    </button>

  </div>

  <textarea
    v-model="block.content"
    rows="6"
    class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-3 text-gray-300 focus:outline-none focus:ring-2 focus:ring-slate-400"
    placeholder="Write text… (HTML allowed)"
  />
</div>

      <!-- CTA BLOCK -->
<div v-else-if="block.type === 'cta'" class="space-y-3">
  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
    <div>
      <label class="block text-xs text-gray-400 mb-1">Title (optional)</label>
      <input v-model="block.title" type="text"
        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-gray-300"
        placeholder="Download Ping Tool" />
    </div>

    <div>
      <label class="block text-xs text-gray-400 mb-1">Button label</label>
      <input v-model="block.label" type="text"
        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-gray-300"
        placeholder="Download now" />
    </div>
  </div>

  <div>
    <label class="block text-xs text-gray-400 mb-1">URL *</label>
    <input v-model="block.url" type="text"
      class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-gray-300"
      placeholder="https://norevia.com/downloads/norevia-ping-tool" />
  </div>

  <div>
    <label class="block text-xs text-gray-400 mb-1">Text (optional)</label>
    <textarea v-model="block.text" rows="3"
      class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-gray-300"
      placeholder="Short pitch above the button..." />
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
    <div>
      <label class="block text-xs text-gray-400 mb-1">Variant</label>
      <select v-model="block.variant"
        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-gray-300">
        <option value="primary">Primary</option>
        <option value="secondary">Secondary</option>
        <option value="outline">Outline</option>
      </select>
    </div>

    <div class="mt-3">
  <div class="text-xs text-gray-400 mb-2">Preview</div>

  <button
    type="button"
    :class="ctaPreviewClass(block.variant)"
    class="px-5 py-2.5 rounded-xl font-semibold transition"
  >
    {{ block.label || 'Download now' }}
  </button>
</div>

    <div>
      <label class="block text-xs text-gray-400 mb-1">Note (optional)</label>
      <input v-model="block.note" type="text"
        class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-gray-300"
        placeholder="Windows 10/11 • No install • No tracking" />
    </div>
  </div>
</div>

      <!-- IMAGE BLOCK -->
      <div v-else-if="block.type === 'image'" class="space-y-3">
        <div v-if="block.src" class="mb-2">
          <img :src="block.src" class="max-h-64 rounded-lg" />
        </div>

        <div class="border-2 border-dashed border-slate-700 rounded-lg p-4 text-center">
          <p class="text-gray-400 mb-2 text-sm">Upload image (max 2MB)</p>

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

      <div v-else class="text-sm text-gray-400">
        Unknown block type: {{ block.type }}
      </div>
    </div>
  </div>

  <p class="mt-3 text-xs text-slate-400">
    Note: Page <code>content</code> is auto-generated from text blocks for excerpt/search.
  </p>

  <div class="mt-3 text-xs text-gray-500 flex justify-between">
    <div>
      Tip: Text will wrap automatically
    </div>
    <div>
      Lines: {{ (form.content.match(/\n/g) || []).length + 1 }}
    </div>
  </div>
</div>

        <!-- SEO SETTINGS -->
        <div class="bg-slate-800/50 rounded-xl border border-slate-700 p-6">
          <h3 class="text-lg font-medium text-gray-100 mb-4">SEO Settings</h3>
          <div class="space-y-4">
            <div>
              <label for="meta_title" class="block text-sm font-medium text-gray-300 mb-2">
                Meta Title
              </label>
              <input
                type="text"
                id="meta_title"
                v-model="form.meta_title"
                class="w-full px-4 py-2 bg-slate-900 border border-slate-700 text-gray-300 rounded-lg"
              />
            </div>
            
            <div>
              <label for="meta_description" class="block text-sm font-medium text-gray-300 mb-2">
                Meta Description
              </label>
              <textarea
                id="meta_description"
                v-model="form.meta_description"
                rows="3"
                class="w-full px-4 py-2 bg-slate-900 border border-slate-700 text-gray-300 rounded-lg"
              ></textarea>
            </div>
          </div>
        </div>

        <!-- STATUS & ACTIONS FOOTER -->
        <div class="bg-slate-800/40 rounded-xl border border-slate-700 p-6">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="space-y-4">
              <!-- STATUS SELECT -->
              <div>
                <label for="status" class="block text-sm font-medium text-gray-300 mb-2">
                  Status
                </label>
                <select
                  id="status"
                  v-model="form.status"
                  class="px-4 py-2.5 bg-slate-900 border border-slate-700 text-gray-300 rounded-lg"
                >
                  <option value="draft">Draft</option>
                  <option value="published">Published</option>
                  <option value="archived">Archived</option>
                </select>
              </div>
              
              <!-- QUICK ACTIONS -->
              <div class="flex space-x-3">
                <button
                  type="button"
                  @click="saveDraft"
                  class="px-4 py-2 border border-slate-700 text-gray-300 rounded-lg hover:bg-slate-800 transition-colors"
                >
                  Save as Draft
                </button>
                <button
                  type="button"
                  @click="publishNow"
                  class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                  v-if="form.status !== 'published'"
                >
                  Publish Now
                </button>
              </div>
            </div>
            
            <!-- ACTION BUTTONS - DONJI -->
            <div class="flex space-x-3">
              <!-- VIEW LIVE BUTTON -->
              <a
                :href="liveUrl"
                target="_blank"
                class="px-4 py-2.5 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors hover:scale-105 flex items-center"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                View Live
              </a>
              
              <!-- DELETE BUTTON -->
              <button
                type="button"
                @click="confirmDelete"
                class="px-4 py-2.5 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors hover:scale-105 flex items-center"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Delete Page
              </button>
              
              <!-- SAVE CHANGES BUTTON - DONJI -->
              <button
                type="submit"
                :disabled="processing"
                class="px-5 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors hover:scale-105 disabled:opacity-50 flex items-center"
              >
                <svg v-if="processing" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                </svg>
                {{ processing ? 'Saving...' : 'Save Changes' }}
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>

    <!-- DELETE CONFIRMATION MODAL -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="fixed inset-0 bg-black/80 backdrop-blur-sm" @click="closeDeleteModal"></div>
      
      <div class="relative bg-slate-800 border-2 border-slate-700 rounded-2xl max-w-md w-full p-8 mx-4 shadow-2xl">
        <div class="text-center mb-6">
          <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-red-900/40 mb-4">
            <svg class="h-7 w-7 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
          </div>
          
          <h3 class="text-2xl font-bold text-white mb-2">Delete Page</h3>
          <p class="text-gray-300">
            Delete "<span class="text-white font-semibold">{{ page.title }}</span>"?
          </p>
          <p class="text-red-400 text-sm mt-2">
            This action cannot be undone
          </p>
        </div>
        
        <div class="flex gap-4">
          <button 
            @click="closeDeleteModal"
            class="flex-1 px-5 py-3 bg-slate-700 hover:bg-slate-600 text-gray-300 hover:text-white rounded-xl text-lg font-medium transition-colors hover:scale-105"
          >
            Cancel
          </button>
          <button 
            @click="deletePage"
            class="flex-1 px-5 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl text-lg font-medium transition-colors hover:scale-105"
          >
            Delete
          </button>
        </div>
      </div>
    </div>

    <!-- TOAST NOTIFICATION -->
    <div v-if="showToast" class="fixed top-6 right-6 z-50 animate-toast-in">
      <div :class="[
        'px-5 py-4 rounded-xl shadow-2xl border backdrop-blur-sm',
        toastType === 'success' ? 'bg-green-900/90 border-green-700 text-green-100' :
        toastType === 'error' ? 'bg-red-900/90 border-red-700 text-red-100' :
        'bg-blue-900/90 border-blue-700 text-blue-100'
      ]">
        <div class="flex items-center gap-3">
          <svg v-if="toastType === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
          <svg v-else-if="toastType === 'error'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
          <span class="font-medium">{{ toastMessage }}</span>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'
import axios from 'axios'


const props = defineProps({
  page: { type: Object, required: true },
})

// Auto-wrap function
const autoWrapText = (event) => {
  const textarea = event.target
  // Automatski resize
  textarea.style.height = 'auto'
  textarea.style.height = Math.min(textarea.scrollHeight, 800) + 'px'
}

// State variables
const processing = ref(false)
const imagePreview = ref(props.page.featured_image)
const imageUrl = ref('')
const showDeleteModal = ref(false)

// Toast state
const showToast = ref(false)
const toastMessage = ref('')
const toastType = ref('success')
let toastTimeout = null
// --- Blocks: file inputs ---
const blockFileInputs = ref({})

const setBlockFileRef = (el, i) => {
  if (el) blockFileInputs.value[i] = el
}

const openBlockFilePicker = (i) => {
  blockFileInputs.value[i]?.click()
}

// --- Blocks: helpers ---
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
const dataURLtoBlob = (dataURL) => {
  const arr = dataURL.split(',')
  const mime = arr[0].match(/:(.*?);/)[1]
  const bstr = atob(arr[1])
  let n = bstr.length
  const u8arr = new Uint8Array(n)
  while (n--) u8arr[n] = bstr.charCodeAt(n)
  return new Blob([u8arr], { type: mime })
}

const uploadImage = async (base64Image) => {
  const blob = dataURLtoBlob(base64Image)
  const formData = new FormData()
  formData.append('image', blob, 'uploaded-image.jpg')

  const response = await axios.post('/upload-image', formData, {
    headers: {
      'Content-Type': 'multipart/form-data',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    },
  })

  return response.data.url
}
const handleBlockImageUpload = async (event, i) => {
  const file = event.target.files?.[0]
  if (!file) return

  if (file.size > 2 * 1024 * 1024) {
    showNotification('File size must be less than 2MB', 'error')
    event.target.value = ''
    return
  }

  // instant preview base64
  const reader = new FileReader()
  reader.onload = async (e) => {
    const base64 = e.target.result
    form.blocks[i].src = base64

    try {
      showNotification('Uploading block image…', 'info')
      const url = await uploadImage(base64)
      form.blocks[i].src = url
      showNotification('Block image uploaded', 'success')
    } catch (err) {
      console.error(err)
      showNotification('Block image upload failed (kept preview)', 'error')
    } finally {
      event.target.value = ''
    }
  }
  reader.readAsDataURL(file)
}

// Form
const initialBlocks = (() => {
  const b = props.page.blocks

  if (Array.isArray(b) && b.length) {
    // ✅ deep clone da ne bude readonly Proxy
    return JSON.parse(JSON.stringify(b))
  }

  if (typeof b === 'string' && b.trim()) {
    try { return JSON.parse(b) } catch {}
  }

  return [{ type: 'text', content: props.page.content || '' }]
})()

const form = useForm({
  title: props.page.title ?? '',
  slug: props.page.slug ?? '',
  content: props.page.content ?? '',
  featured_image: props.page.featured_image ?? '',
  image_caption: props.page.image_caption ?? '',
  meta_title: props.page.meta_title ?? '',
  meta_description: props.page.meta_description ?? '',
  status: props.page.status ?? 'draft',

  // ✅ samo jednom i stabilno
  blocks: initialBlocks,

  page_type: props.page.page_type ?? 'post',
  layout: props.page.layout ?? 'minimal',
  type: props.page.type ?? 'page',
  topic: props.page.topic ?? null,
})

//Watch
watch(() => form.type, (v) => {
  if (v === 'blog') {
    if (!form.topic) form.topic = 'cs2'
    if (form.page_type === 'post') form.page_type = 'gaming'
  } else {
    form.topic = null
  }
})

//LiveUrl
const liveUrl = computed(() => {
  if (form.type === 'blog' && form.topic) return `/blog/${form.topic}/${form.slug}`
  return `/pages/${form.slug}`
})
// Computed
const baseUrl = computed(() => window.location.origin)
const fullUrl = computed(() => `${baseUrl.value}${liveUrl.value}`)

// Toast functions
const showNotification = (message, type = 'success') => {
  toastMessage.value = message
  toastType.value = type
  showToast.value = true
  
  clearTimeout(toastTimeout)
  toastTimeout = setTimeout(() => {
    showToast.value = false
  }, 3000)
}

// Copy URL function
const copyUrl = () => {
  navigator.clipboard.writeText(fullUrl.value)
    .then(() => {
      showNotification('URL copied to clipboard!', 'success')
    })
    .catch(err => {
      console.error('Failed to copy:', err)
      showNotification('Failed to copy URL', 'error')
    })
}

// Image functions
const handleImageUpload = (event) => {
  const file = event.target.files[0]
  if (!file) return
  
  if (file.size > 2 * 1024 * 1024) {
    showNotification('File size must be less than 2MB', 'error')
    return
  }
  
  const reader = new FileReader()
  reader.onload = (e) => {
    imagePreview.value = e.target.result
    form.featured_image = e.target.result
    event.target.value = ''
    showNotification('Image selected successfully', 'success')
  }
  reader.readAsDataURL(file)
}

const useImageUrl = () => {
  if (imageUrl.value) {
    form.featured_image = imageUrl.value
    imagePreview.value = imageUrl.value
    imageUrl.value = ''
    showNotification('Image URL applied successfully', 'success')
  }
}

const removeImage = () => {
  form.featured_image = ''
  imagePreview.value = null
  showNotification('Image removed', 'success')
}

// Form actions
const saveDraft = () => {
  form.status = 'draft'
  submitForm()
}

const publishNow = () => {
  form.status = 'published'
  submitForm()
}
  //syncBloks
  const syncContentFromBlocks = () => {
  const textParts = (form.blocks || [])
    .filter(b => b.type === 'text' && (b.content || '').trim())
    .map(b => b.content.trim())
  form.content = textParts.join("\n\n")
}

const submitForm = async () => {
  processing.value = true
  
  // Upload featured image if base64
if (form.featured_image && form.featured_image.startsWith('data:image')) {
  try {
    showNotification('Uploading featured image…', 'info')
    const url = await uploadImage(form.featured_image)
    form.featured_image = url
    imagePreview.value = url
    showNotification('Featured image uploaded', 'success')
  } catch (error) {
    console.error('Featured upload failed:', error)
    showNotification('Featured image upload failed', 'error')
  }
}

// Upload any block images still in base64
for (const b of (form.blocks || [])) {
  if (b?.type === 'image' && b?.src && String(b.src).startsWith('data:image')) {
    try {
      showNotification('Uploading block image…', 'info')
      b.src = await uploadImage(b.src)
    } catch (e) {
      console.error(e)
      showNotification('A block image failed to upload (kept base64 preview)', 'error')
    }
  }
}

  syncContentFromBlocks()

  // Update page
  form.put(`/content-admin/pages/${props.page.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      processing.value = false
      showNotification('Page updated successfully!', 'success')
    },
    onError: (errors) => {
      processing.value = false
      console.error('Update errors:', errors)
      showNotification('Error updating page. Please check the form.', 'error')
    }
  })
}

// Delete functions
const confirmDelete = () => {
  showDeleteModal.value = true
}

const deletePage = () => {
  router.delete(`/content-admin/pages/${props.page.id}`, {
    onSuccess: () => {
      showDeleteModal.value = false
      showNotification('Page deleted successfully', 'success')
    },
    onError: () => {
      showDeleteModal.value = false
      showNotification('Error deleting page', 'error')
    }
  })
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
}

const addCtaBlock = () => form.blocks.push({
  type: 'cta',
  title: 'Download Ping Tool',
  text: 'Test your real ping and packet loss in seconds.',
  label: 'Download now',
  url: 'https://norevia.com/downloads/norevia-ping-tool',
  variant: 'primary',
  note: 'Windows 10/11 • No install • No tracking',
})

const ctaPreviewClass = (variant) => {
  if (variant === 'secondary') {
    return 'bg-slate-800 text-white border border-slate-600 hover:bg-slate-700'
  }

  if (variant === 'outline') {
    return 'bg-transparent text-emerald-300 border border-emerald-500/60 hover:bg-emerald-500/10'
  }

  // primary
  return 'bg-gradient-to-r from-emerald-600 to-sky-600 text-white hover:opacity-95'
}

const getTextareas = () => Array.from(document.querySelectorAll('textarea'))

const wrapSelection = (blockIndex, tag) => {
  const ta = getTextareas()[blockIndex]
  if (!ta) return

  const start = ta.selectionStart
  const end = ta.selectionEnd
  const text = form.blocks[blockIndex].content || ''
  const selected = text.substring(start, end)

  if (!selected) return

  const wrapped = `<${tag}>${selected}</${tag}>`
  form.blocks[blockIndex].content = text.substring(0, start) + wrapped + text.substring(end)
}

const applyColor = (blockIndex, className) => {
  if (!className) return

  const ta = getTextareas()[blockIndex]
  if (!ta) return

  const start = ta.selectionStart
  const end = ta.selectionEnd
  const text = form.blocks[blockIndex].content || ''
  const selected = text.substring(start, end)

  if (!selected) return

  const wrapped = `<span class="${className}">${selected}</span>`
  form.blocks[blockIndex].content = text.substring(0, start) + wrapped + text.substring(end)
}

const insertBreak = (blockIndex) => {
  const ta = getTextareas()[blockIndex]
  if (!ta) return

  const start = ta.selectionStart
  const end = ta.selectionEnd
  const text = form.blocks[blockIndex].content || ''

  const br = '<br>'
  form.blocks[blockIndex].content = text.substring(0, start) + br + text.substring(end)
}

const insertLink = (blockIndex) => {
  let url = prompt('Enter URL:')
  if (!url) return

  // ✅ auto add https so it won't become /google.com
  if (!/^https?:\/\//i.test(url)) url = 'https://' + url

  const ta = getTextareas()[blockIndex]
  if (!ta) return

  const start = ta.selectionStart
  const end = ta.selectionEnd
  const text = form.blocks[blockIndex].content || ''
  const selected = text.substring(start, end) || url

  const link = `<a href="${url}" class="text-sky-300 underline underline-offset-2 hover:text-sky-200 break-words" target="_blank" rel="nofollow noopener noreferrer">${selected}</a>`
  form.blocks[blockIndex].content = text.substring(0, start) + link + text.substring(end)
}

const insertHashtag = (blockIndex) => {
  const tag = prompt('Enter hashtag (without #):')
  if (!tag) return
  form.blocks[blockIndex].content = (form.blocks[blockIndex].content || '') + ` #${tag}`
}
</script>

<style scoped>
/* Animations */
@keyframes toast-in {
  0% {
    opacity: 0;
    transform: translateX(100%) translateY(-20px);
  }
  100% {
    opacity: 1;
    transform: translateX(0) translateY(0);
  }
}

.animate-toast-in {
  animation: toast-in 0.3s ease-out forwards;
}

/* Spinner animation */
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

/* Smooth transitions */
button, a {
  transition: all 0.2s ease;
}

button:hover:not(:disabled), a:hover {
  transform: translateY(-2px);
}

/* Modal backdrop blur */
.backdrop-blur-sm {
  backdrop-filter: blur(4px);
}
</style>