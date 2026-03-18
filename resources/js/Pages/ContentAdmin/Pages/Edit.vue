<!-- resources/js/Pages/ContentAdmin/Pages/Edit.vue -->
<template>
  <MainLayout>
    <div class="max-w-5xl mx-auto px-4 py-8">
      <!-- Header -->
      <div class="mb-8 p-6 bg-slate-800/40 rounded-2xl border border-slate-700">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
          <div>
            <h1 class="text-3xl font-bold text-white mb-2">Edit Page</h1>
            <p class="text-gray-400">
              Editing: <span class="text-white font-medium">{{ page.title }}</span>
            </p>
          </div>

          <div class="flex flex-wrap gap-3">
            <Link
              href="/content-admin/pages"
              class="px-4 py-2.5 text-gray-300 border border-slate-700 rounded-xl hover:bg-slate-800 transition-colors"
            >
              Back to Pages
            </Link>

            <button
              type="button"
              @click="submitForm"
              :disabled="processing"
              class="px-5 py-2.5 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition-colors disabled:opacity-50"
            >
              {{ processing ? 'Saving...' : 'Save Changes' }}
            </button>
          </div>
        </div>
      </div>

      <!-- Flash -->
      <div
        v-if="$page.props.flash.success"
        class="mb-6 p-4 bg-green-900/30 text-green-400 rounded-xl border border-green-800"
      >
        {{ $page.props.flash.success }}
      </div>

      <form @submit.prevent="submitForm" class="space-y-6">
        <!-- Title -->
        <div class="bg-slate-800/50 rounded-2xl border border-slate-700 p-6">
          <label class="block text-sm font-medium text-gray-300 mb-2">Page Title *</label>
          <input
            v-model="form.title"
            type="text"
            required
            class="w-full px-4 py-3 bg-slate-900 border border-slate-700 rounded-xl text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          />
        </div>

        <!-- Content kind + topic -->
        <div class="bg-slate-800/50 rounded-2xl border border-slate-700 p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Content kind</label>
              <select
                v-model="form.type"
                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-gray-300"
              >
                <option value="page">Page</option>
                <option value="blog">Blog post</option>
              </select>
            </div>

            <div v-if="form.type === 'blog'">
              <label class="block text-sm font-medium text-gray-300 mb-2">Blog hub (topic)</label>
              <select
                v-model="form.topic"
                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-gray-300"
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

        <!-- Category + Layout -->
        <div class="bg-slate-800/50 rounded-2xl border border-slate-700 p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Category</label>
              <select
                v-model="form.page_type"
                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-gray-300"
              >
                <option value="post">Post</option>
                <option value="news">News</option>
                <option value="gaming">Gaming</option>
                <option value="horoscope">Horoscope</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Layout</label>
              <select
                v-model="form.layout"
                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-gray-300"
              >
                <option value="minimal">Minimal</option>
                <option value="classic">Classic</option>
                <option value="magazine">Magazine</option>
                <option value="hero">Hero</option>
                <option value="editorial">Editorial</option>
                <option value="story">Story</option>
                <option value="docs">Docs</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Slug + URL -->
        <div class="bg-slate-800/50 rounded-2xl border border-slate-700 p-6">
          <label class="block text-sm font-medium text-gray-300 mb-2">URL Slug *</label>

          <div class="flex items-center mb-3">
            <span class="text-gray-500 mr-2 shrink-0">{{ baseUrl }}</span>
            <span class="text-gray-500 mr-2 shrink-0">{{ livePrefix }}</span>
            <input
              v-model="form.slug"
              type="text"
              required
              class="flex-1 px-4 py-3 bg-slate-900 border border-slate-700 rounded-xl text-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
          </div>

          <div class="mt-4 p-4 bg-slate-900/50 rounded-xl border border-slate-700">
            <p class="text-sm font-medium text-gray-300 mb-2">Full URL:</p>
            <div class="flex items-center">
              <input
                type="text"
                readonly
                :value="fullUrl"
                class="flex-1 bg-slate-800 text-gray-300 text-sm px-4 py-2 rounded-l-xl border border-r-0 border-slate-700 focus:outline-none"
              />
              <button
                type="button"
                @click="copyUrl"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-r-xl border border-blue-700 transition-colors"
              >
                Copy URL
              </button>
            </div>
          </div>
        </div>

        <!-- Featured image -->
        <div class="bg-slate-800/50 rounded-2xl border border-slate-700 p-6">
          <label class="block text-sm font-medium text-gray-300 mb-2">Featured Image</label>

          <div v-if="imagePreview" class="mb-4">
            <div class="relative inline-block">
              <img :src="imagePreview" alt="Preview" class="max-h-64 rounded-xl shadow-lg" />
              <button
                type="button"
                @click="removeImage"
                class="absolute top-2 right-2 bg-red-600 text-white p-2 rounded-full hover:bg-red-700 transition-colors"
                title="Remove image"
              >
                ✕
              </button>
            </div>
          </div>

          <div class="space-y-4">
            <div class="border-2 border-dashed border-slate-700 rounded-xl p-8 text-center hover:border-blue-500 transition-colors">
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
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
              >
                Select Image
              </button>
            </div>

            <div class="flex">
              <input
                type="text"
                v-model="imageUrl"
                placeholder="Enter image URL (optional)"
                class="flex-1 px-4 py-2 bg-slate-900 border border-slate-700 text-gray-300 rounded-l-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
              <button
                type="button"
                @click="useImageUrl"
                class="px-4 py-2 bg-slate-700 text-gray-300 hover:bg-slate-600 rounded-r-xl transition-colors"
              >
                Use URL
              </button>
            </div>
          </div>

          <div class="mt-4">
            <label class="block text-sm font-medium text-gray-300 mb-2">Image Caption</label>
            <input
              v-model="form.image_caption"
              type="text"
              class="w-full px-4 py-2 bg-slate-900 border border-slate-700 text-gray-300 rounded-xl"
              placeholder="Optional caption for the image"
            />
          </div>
        </div>

        <!-- Content blocks -->
        <div class="bg-slate-800/50 rounded-2xl border border-slate-700 p-6">
          <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
            <h3 class="text-lg font-semibold text-gray-100">Content blocks</h3>

            <div class="flex gap-2 flex-wrap">
              <button
                type="button"
                @click="addAdBlock"
                class="px-3 py-2 bg-amber-600/90 hover:bg-amber-600 text-white rounded-lg text-sm"
              >
                + Ad
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

              <button
                type="button"
                @click="addCtaBlock"
                class="px-3 py-2 rounded-lg text-sm font-medium bg-emerald-600/90 hover:bg-emerald-600 text-white"
              >
                + CTA
              </button>
            </div>
          </div>

          <div class="space-y-4">
            <div
              v-for="(block, i) in form.blocks"
              :key="i"
              class="p-4 rounded-2xl border border-slate-700 bg-slate-900/40"
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

              <!-- TEXT -->
              <div v-if="block.type === 'text'" class="space-y-3">
                <div class="flex flex-wrap gap-2 items-center">
                  <select
                    :value="''"
                    @change="applyTextStyle(i, $event)"
                    class="bg-slate-800 border border-slate-700 text-gray-300 text-xs px-3 py-2 rounded-lg"
                  >
                    <option value="">Text Style</option>
                    <option value="paragraph">Paragraph</option>
                    <option value="lead">Lead Paragraph</option>
                    <option value="small">Small Text</option>
                    <option value="quote">Quote</option>
                    <option value="highlight">Highlight Box</option>
                  </select>

                  <select
                    :value="''"
                    @change="applyHeading(i, $event)"
                    class="bg-slate-800 border border-slate-700 text-gray-300 text-xs px-3 py-2 rounded-lg"
                  >
                    <option value="">Headings</option>
                    <option value="h1">H1</option>
                    <option value="h2">H2</option>
                    <option value="h3">H3</option>
                  </select>

                  <select
                    :value="''"
                    @change="applyColor(i, $event)"
                    class="bg-slate-800 border border-slate-700 text-gray-300 text-xs px-3 py-2 rounded-lg"
                  >
                    <option value="">Text Color</option>
                    <option value="text-slate-700 dark:text-slate-300">Default</option>
                    <option value="text-white">White</option>
                    <option value="text-slate-200">Light</option>
                    <option value="text-sky-300">Sky</option>
                    <option value="text-emerald-300">Green</option>
                    <option value="text-fuchsia-300">Pink</option>
                    <option value="text-red-400">Red</option>
                    <option value="text-yellow-300">Yellow</option>
                  </select>

                  <button
                    type="button"
                    @click="wrapSelection(i, 'strong')"
                    class="px-3 py-2 bg-slate-800 border border-slate-700 rounded text-xs text-gray-200"
                  >
                    Bold
                  </button>

                  <button
                    type="button"
                    @click="wrapSelection(i, 'em')"
                    class="px-3 py-2 bg-slate-800 border border-slate-700 rounded text-xs text-gray-200"
                  >
                    Italic
                  </button>

                  <button
                    type="button"
                    @click="insertLink(i)"
                    class="px-3 py-2 bg-slate-800 border border-slate-700 rounded text-xs text-gray-200"
                  >
                    Link
                  </button>

                  <button
                    type="button"
                    @click="insertBreak(i)"
                    class="px-3 py-2 bg-slate-800 border border-slate-700 rounded text-xs text-gray-200"
                  >
                    BR
                  </button>

                  <button
                    type="button"
                    @click="insertHashtag(i)"
                    class="px-3 py-2 bg-slate-800 border border-slate-700 rounded text-xs text-gray-200"
                  >
                    #Tag
                  </button>
                </div>

                <textarea
                  :ref="(el) => setTextAreaRef(el, i)"
                  v-model="block.content"
                  rows="8"
                  class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-gray-300 leading-7 focus:outline-none focus:ring-2 focus:ring-slate-400"
                  placeholder="Write text…"
                />
              </div>

              <!-- CTA -->
              <div v-else-if="block.type === 'cta'" class="space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <div>
                    <label class="block text-xs text-gray-400 mb-1">Title (optional)</label>
                    <input
                      v-model="block.title"
                      type="text"
                      class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 text-gray-300"
                      placeholder="Download Ping Tool"
                    />
                  </div>

                  <div>
                    <label class="block text-xs text-gray-400 mb-1">Button label</label>
                    <input
                      v-model="block.label"
                      type="text"
                      class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 text-gray-300"
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
                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 text-gray-300"
                    placeholder="https://norevia.com/downloads/norevia-ping-tool"
                  />
                </div>

                <div>
                  <label class="block text-xs text-gray-400 mb-1">Text (optional)</label>
                  <textarea
                    v-model="block.text"
                    rows="3"
                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 text-gray-300"
                    placeholder="Short pitch above the button..."
                  />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <div>
                    <label class="block text-xs text-gray-400 mb-1">Variant</label>
                    <select
                      v-model="block.variant"
                      class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 text-gray-300"
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
                      class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 text-gray-300"
                      placeholder="Windows 10/11 • No install • No tracking"
                    />
                  </div>
                </div>

                <div class="pt-3 border-t border-slate-700">
                  <div class="text-xs text-gray-400 mb-2">Preview</div>

                  <div class="rounded-xl border border-slate-700 bg-slate-900/40 p-4">
                    <div v-if="block.title" class="text-white font-semibold">{{ block.title }}</div>
                    <div v-if="block.text" class="mt-1 text-sm text-slate-300 whitespace-pre-line break-words">
                      {{ block.text }}
                    </div>

                    <div class="mt-3">
                      <span
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold"
                        :class="ctaPreviewClass(block.variant)"
                      >
                        {{ block.label || 'Download now' }} →
                      </span>
                    </div>

                    <div v-if="block.note" class="mt-2 text-xs text-slate-400">{{ block.note }}</div>
                  </div>
                </div>
              </div>

              <!-- AD -->
              <div v-else-if="block.type === 'ad'" class="space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <div>
                    <label class="block text-xs text-gray-400 mb-1">Kind</label>
                    <select
                      v-model="block.kind"
                      class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 text-gray-300"
                    >
                      <option value="adsense">AdSense</option>
                      <option value="affiliate">Affiliate</option>
                      <option value="html">Custom HTML</option>
                    </select>
                  </div>

                  <div>
                    <label class="block text-xs text-gray-400 mb-1">Label (optional)</label>
                    <input
                      v-model="block.note"
                      type="text"
                      class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 text-gray-300"
                      placeholder="Sponsored"
                    />
                  </div>
                </div>

                <div v-if="block.kind === 'adsense'">
                  <label class="block text-xs text-gray-400 mb-1">AdSense slot ID *</label>
                  <input
                    v-model="block.adsense_slot"
                    type="text"
                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 text-gray-300"
                    placeholder="e.g. 1234567890"
                  />
                </div>

                <div v-else>
                  <label class="block text-xs text-gray-400 mb-1">Paste banner HTML</label>
                  <textarea
                    v-model="block.html"
                    rows="5"
                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 text-gray-300"
                    placeholder='<a href="https://..." target="_blank" rel="nofollow noopener"><img src="..." /></a>'
                  />
                </div>

                <div class="pt-3 border-t border-slate-700">
                  <div class="text-xs text-slate-400 mb-2">Preview</div>
                  <div class="rounded-xl border border-slate-700 bg-slate-900/40 p-4">
                    <div v-if="block.note" class="text-xs text-slate-400 mb-2">{{ block.note }}</div>
                    <div v-if="block.kind === 'adsense'" class="text-slate-300 text-sm">
                      AdSense slot: <span class="font-semibold text-white">{{ block.adsense_slot || '—' }}</span>
                    </div>
                    <div v-else class="text-slate-300 text-sm break-words">
                      HTML banner will render on public page.
                    </div>
                  </div>
                </div>
              </div>

              <!-- IMAGE -->
              <div v-else-if="block.type === 'image'" class="space-y-3">
                <div v-if="block.src" class="mb-2">
                  <img :src="block.src" class="max-h-64 rounded-xl" />
                </div>

                <div class="border-2 border-dashed border-slate-700 rounded-xl p-4 text-center">
                  <p class="text-gray-400 mb-2 text-sm">Upload image (max 150MB)</p>

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
                    class="flex-1 bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 text-gray-300"
                  />
                </div>

                <input
                  v-model="block.caption"
                  type="text"
                  placeholder="Caption (optional)"
                  class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2 text-gray-300"
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
        </div>

        <!-- SEO -->
        <div class="bg-slate-800/50 rounded-2xl border border-slate-700 p-6">
          <h3 class="text-lg font-medium text-gray-100 mb-4">SEO Settings</h3>

          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Meta Title</label>
              <input
                v-model="form.meta_title"
                type="text"
                class="w-full px-4 py-2 bg-slate-900 border border-slate-700 text-gray-300 rounded-xl"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-300 mb-2">Meta Description</label>
              <textarea
                v-model="form.meta_description"
                rows="3"
                class="w-full px-4 py-2 bg-slate-900 border border-slate-700 text-gray-300 rounded-xl"
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Footer actions -->
        <div class="bg-slate-800/40 rounded-2xl border border-slate-700 p-6">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                <select
                  v-model="form.status"
                  class="px-4 py-2.5 bg-slate-900 border border-slate-700 text-gray-300 rounded-xl"
                >
                  <option value="draft">Draft</option>
                  <option value="published">Published</option>
                  <option value="archived">Archived</option>
                </select>
              </div>

              <div class="flex space-x-3">
                <button
                  type="button"
                  @click="saveDraft"
                  class="px-4 py-2 border border-slate-700 text-gray-300 rounded-xl hover:bg-slate-800 transition-colors"
                >
                  Save as Draft
                </button>

                <button
                  v-if="form.status !== 'published'"
                  type="button"
                  @click="publishNow"
                  class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors"
                >
                  Publish Now
                </button>
              </div>
            </div>

            <div class="flex flex-wrap gap-3">
              <a
                :href="liveUrl"
                target="_blank"
                class="px-4 py-2.5 bg-green-600 text-white rounded-xl font-medium hover:bg-green-700 transition-colors"
              >
                View Live
              </a>

              <button
                type="button"
                @click="confirmDelete"
                class="px-4 py-2.5 bg-red-600 text-white rounded-xl font-medium hover:bg-red-700 transition-colors"
              >
                Delete Page
              </button>

              <button
                type="submit"
                :disabled="processing"
                class="px-5 py-2.5 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition-colors disabled:opacity-50"
              >
                {{ processing ? 'Saving...' : 'Save Changes' }}
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>

    <!-- Delete modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center">
      <div class="fixed inset-0 bg-black/80 backdrop-blur-sm" @click="closeDeleteModal"></div>

      <div class="relative bg-slate-800 border-2 border-slate-700 rounded-2xl max-w-md w-full p-8 mx-4 shadow-2xl">
        <div class="text-center mb-6">
          <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-red-900/40 mb-4">
            <span class="text-red-400 text-2xl">🗑</span>
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
            class="flex-1 px-5 py-3 bg-slate-700 hover:bg-slate-600 text-gray-300 hover:text-white rounded-xl text-lg font-medium transition-colors"
          >
            Cancel
          </button>
          <button
            @click="deletePage"
            class="flex-1 px-5 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl text-lg font-medium transition-colors"
          >
            Delete
          </button>
        </div>
      </div>
    </div>

    <!-- Toast -->
    <div v-if="showToast" class="fixed top-6 right-6 z-50 animate-toast-in">
      <div
        :class="[
          'px-5 py-4 rounded-xl shadow-2xl border backdrop-blur-sm',
          toastType === 'success' ? 'bg-green-900/90 border-green-700 text-green-100' :
          toastType === 'error' ? 'bg-red-900/90 border-red-700 text-red-100' :
          'bg-blue-900/90 border-blue-700 text-blue-100'
        ]"
      >
        <div class="flex items-center gap-3">
          <span class="font-medium">{{ toastMessage }}</span>
        </div>
      </div>
    </div>
  </MainLayout>
</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import { ref, computed, watch, nextTick } from 'vue'
import axios from 'axios'

const props = defineProps({
  page: { type: Object, required: true },
})

const processing = ref(false)
const imagePreview = ref(props.page.featured_image || '')
const imageUrl = ref('')
const showDeleteModal = ref(false)

const showToast = ref(false)
const toastMessage = ref('')
const toastType = ref('success')
let toastTimeout = null

const blockFileInputs = ref({})
const textAreaRefs = ref({})

const initialBlocks = (() => {
  const b = props.page.blocks

  if (Array.isArray(b) && b.length) {
    return JSON.parse(JSON.stringify(b))
  }

  if (typeof b === 'string' && b.trim()) {
    try {
      return JSON.parse(b)
    } catch {}
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
  blocks: initialBlocks,
  page_type: props.page.page_type ?? 'post',
  layout: props.page.layout ?? 'minimal',
  type: props.page.type ?? 'page',
  topic: props.page.topic ?? null,
})

watch(() => form.type, (v) => {
  if (v === 'blog') {
    if (!form.topic) form.topic = 'cs2'
    if (form.page_type === 'post') form.page_type = 'gaming'
  } else {
    form.topic = null
  }
})

const baseUrl = computed(() => window.location.origin)

const livePrefix = computed(() => {
  if (form.type === 'blog' && form.topic) return `/blog/${form.topic}/`
  return '/pages/'
})

const liveUrl = computed(() => {
  if (form.type === 'blog' && form.topic) return `/blog/${form.topic}/${form.slug}`
  return `/pages/${form.slug}`
})

const fullUrl = computed(() => `${baseUrl.value}${liveUrl.value}`)

const showNotification = (message, type = 'success') => {
  toastMessage.value = message
  toastType.value = type
  showToast.value = true

  clearTimeout(toastTimeout)
  toastTimeout = setTimeout(() => {
    showToast.value = false
  }, 3000)
}

const copyUrl = () => {
  navigator.clipboard.writeText(fullUrl.value)
    .then(() => showNotification('URL copied to clipboard!', 'success'))
    .catch(() => showNotification('Failed to copy URL', 'error'))
}

const setTextAreaRef = (el, i) => {
  if (el) textAreaRefs.value[i] = el
}

const getTA = (i) => textAreaRefs.value[i] || null

const setBlockFileRef = (el, i) => {
  if (el) blockFileInputs.value[i] = el
}

const openBlockFilePicker = (i) => {
  blockFileInputs.value[i]?.click()
}

const addTextBlock = () => form.blocks.push({ type: 'text', content: '' })
const addImageBlock = () => form.blocks.push({ type: 'image', src: '', caption: '' })

const addCtaBlock = () => form.blocks.push({
  type: 'cta',
  title: 'Download Ping Tool',
  text: 'Test your real ping and packet loss in seconds.',
  label: 'Download now',
  url: 'https://norevia.com/downloads/norevia-ping-tool',
  variant: 'primary',
  note: 'Windows 10/11 • No install • No tracking',
})

const addAdBlock = () => form.blocks.push({
  type: 'ad',
  kind: 'affiliate',
  note: 'Sponsored',
  adsense_slot: '',
  html: '',
})

const removeBlock = (i) => form.blocks.splice(i, 1)

const moveBlock = (i, dir) => {
  const j = i + dir
  if (j < 0 || j >= form.blocks.length) return
  const tmp = form.blocks[i]
  form.blocks[i] = form.blocks[j]
  form.blocks[j] = tmp
}

const normalizeTextContent = (value) => {
  return String(value || '')
    .replace(/\r\n/g, '\n')
    .replace(/\r/g, '\n')
    .replace(/\n{3,}/g, '\n\n')
    .trim()
}

const prepareBlocksForSubmit = () => {
  form.blocks = form.blocks.map((block) => {
    if (block.type !== 'text') return block
    return {
      ...block,
      content: normalizeTextContent(block.content),
    }
  })
}

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
  nextTick(() => ta.focus())
}

const wrapSelectionWithCustom = (i, before, after) => {
  const ta = getTA(i)
  if (!ta) return

  const start = ta.selectionStart
  const end = ta.selectionEnd
  const text = form.blocks[i].content || ''
  const selected = text.substring(start, end)
  if (!selected) return

  form.blocks[i].content = text.substring(0, start) + before + selected + after + text.substring(end)
  nextTick(() => ta.focus())
}

const applyColor = (i, event) => {
  const className = event.target.value
  if (!className) return

  wrapSelectionWithCustom(i, `<span class="${className}">`, `</span>`)
  nextTick(() => {
    event.target.value = ''
  })
}

const applyHeading = (i, event) => {
  const tag = event.target.value
  if (!tag) return

  wrapSelectionWithCustom(i, `<${tag}>`, `</${tag}>`)
  nextTick(() => {
    event.target.value = ''
  })
}

const applyTextStyle = (i, event) => {
  const style = event.target.value
  if (!style) return

  if (style === 'paragraph') {
    wrapSelectionWithCustom(i, `<p>`, `</p>`)
  } else if (style === 'lead') {
    wrapSelectionWithCustom(i, `<p class="lead">`, `</p>`)
  } else if (style === 'small') {
    wrapSelectionWithCustom(i, `<p class="small-text">`, `</p>`)
  } else if (style === 'quote') {
    wrapSelectionWithCustom(i, `<blockquote>`, `</blockquote>`)
  } else if (style === 'highlight') {
    wrapSelectionWithCustom(i, `<div class="highlight-box">`, `</div>`)
  }

  nextTick(() => {
    event.target.value = ''
  })
}

const insertBreak = (i) => {
  const ta = getTA(i)
  if (!ta) {
    form.blocks[i].content = (form.blocks[i].content || '') + '\n'
    return
  }

  const start = ta.selectionStart
  const end = ta.selectionEnd
  const text = form.blocks[i].content || ''
  const br = '\n'

  form.blocks[i].content = text.substring(0, start) + br + text.substring(end)
  nextTick(() => ta.focus())
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
  nextTick(() => ta.focus())
}

const insertHashtag = (i) => {
  const tag = prompt('Enter hashtag (without #):')
  if (!tag) return
  form.blocks[i].content = (form.blocks[i].content || '') + ` #${tag}`
}

const ctaPreviewClass = (variant) => {
  if (variant === 'secondary') {
    return 'border border-slate-600 bg-slate-800 text-white'
  }
  if (variant === 'outline') {
    return 'border border-emerald-500/60 text-emerald-300 bg-transparent'
  }
  return 'bg-gradient-to-r from-emerald-600 to-sky-600 text-white'
}

const handleImageUpload = (event) => {
  const file = event.target.files[0]
  if (!file) return

  if (file.size > 150 * 1024 * 1024) {
    showNotification('File size must be less than 150MB', 'error')
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
  imagePreview.value = ''
  showNotification('Image removed', 'success')
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

const uploadImage = async (input) => {
  const formData = new FormData()

  if (input instanceof File) {
    formData.append('image', input, input.name)
  } else if (typeof input === 'string' && input.startsWith('data:image')) {
    const blob = dataURLtoBlob(input)
    formData.append('image', blob, 'uploaded-image.jpg')
  } else if (typeof input === 'string' && (input.startsWith('http://') || input.startsWith('https://'))) {
    return input
  } else {
    throw new Error('uploadImage: unsupported input type')
  }

  const response = await axios.post('/upload-image', formData, {
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json',
    },
  })

  return response.data.url
}

const handleBlockImageUpload = async (event, i) => {
  const file = event.target.files?.[0]
  if (!file) return

  if (file.size > 150 * 1024 * 1024) {
    showNotification('File size must be less than 150MB', 'error')
    event.target.value = ''
    return
  }

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
      showNotification('Block image upload failed (preview kept)', 'error')
    } finally {
      event.target.value = ''
    }
  }
  reader.readAsDataURL(file)
}

function normalizeUrl(input) {
  const s = String(input || '').trim()
  if (!s) return ''
  const cleaned = s.replace(/^[^\w]+/g, '').trim()
  if (cleaned.startsWith('/')) return `https://norevia.com${cleaned}`
  if (!/^https?:\/\//i.test(cleaned)) return `https://${cleaned}`
  return cleaned
}

function onCtaUrlBlur(block) {
  block.url = normalizeUrl(block.url)
}

const syncContentFromBlocks = () => {
  const textParts = (form.blocks || [])
    .filter(b => b.type === 'text' && (b.content || '').trim())
    .map(b => normalizeTextContent(b.content))
  form.content = textParts.join('\n\n')
}

const saveDraft = () => {
  form.status = 'draft'
  submitForm()
}

const publishNow = () => {
  form.status = 'published'
  submitForm()
}

const submitForm = async () => {
  processing.value = true

  prepareBlocksForSubmit()

  if (form.featured_image && String(form.featured_image).startsWith('data:image')) {
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

  for (const b of (form.blocks || [])) {
    if (b?.type === 'image' && b?.src && String(b.src).startsWith('data:image')) {
      try {
        showNotification('Uploading block image…', 'info')
        b.src = await uploadImage(b.src)
      } catch (e) {
        console.error(e)
        showNotification('A block image failed to upload', 'error')
      }
    }
  }

  syncContentFromBlocks()

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

const confirmDelete = () => {
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
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
</script>

<style scoped>
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

.backdrop-blur-sm {
  backdrop-filter: blur(4px);
}
</style>