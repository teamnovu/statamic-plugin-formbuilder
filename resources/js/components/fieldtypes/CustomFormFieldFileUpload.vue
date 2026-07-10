<!-- Form Builder control panel fieldtype. -->
<template>
  <div
    class="form-data-display"
    :style="hasFiles ? 'opacity: 1' : 'opacity: 0.4'"
  >
    <h3 class="text-base mb-2 custom-form-field-file-upload">
      {{
        config.label?.find(element => element.handle === locale)?.value
          ?? config.label?.find(element => element.handle === 'en')?.value
          ?? config.label?.find(element => element.handle === 'de')?.value
          ?? config.display
      }}:
    </h3>

    <div class="gap-2 mb-2 flex h-fit items-center justify-between">
      <!-- DATA DISPLAY -->
      <div
        class="
          cff-data-box bg-gray-50 border-gray-200 rounded-sm px-4 py-2 grow
          border
        "
      >
        <div
          v-if="hasFiles"
          class="space-y-3"
        >
          <div
            v-for="file in files"
            :key="file.path"
            class="gap-3 flex items-start justify-between"
          >
            <div class="min-w-0">
              <p class="cff-text text-sm font-medium wrap-break-word">
                {{ file.fileName }}
              </p>
              <!-- <p v-if="file.publicUrl" class="text-xs text-blue-700 break-all">
                                {{ file.publicUrl }}
                            </p> -->
              <p
                class="cff-text-muted text-xs text-gray-600 break-all"
              >
                {{ file.path }}
              </p>
            </div>
            <div class="gap-1.5 flex shrink-0 items-center">
              <a
                v-if="file.publicUrl"
                class="
                  cff-link-blue px-2 py-1 text-xs font-medium text-blue-700
                  bg-blue-50 border-blue-200 rounded-sm
                  hover:bg-blue-100
                  focus:ring-blue-500
                  border transition-colors
                  focus:ring-2 focus:ring-offset-1 focus:outline-none
                "
                :href="file.publicUrl"
                target="_blank"
                rel="noopener"
              >
                Open
              </a>
              <a
                class="
                  cff-btn px-2 py-1 text-xs font-medium text-gray-700 bg-white
                  border-gray-300 rounded-sm
                  hover:bg-gray-50
                  focus:ring-blue-500
                  border transition-colors
                  focus:ring-2 focus:ring-offset-1 focus:outline-none
                "
                :href="file.cpAssetUrl"
                target="_blank"
                rel="noopener"
              >
                Open in CP
              </a>
              <a
                class="
                  cff-btn px-2 py-1 text-xs font-medium text-gray-700 bg-white
                  border-gray-300 rounded-sm
                  hover:bg-gray-50
                  focus:ring-blue-500
                  border transition-colors
                  focus:ring-2 focus:ring-offset-1 focus:outline-none
                "
                :href="file.cpBrowseUrl"
                target="_blank"
                rel="noopener"
              >
                View Folder
              </a>
            </div>
          </div>
        </div>
        <div
          v-else
          class="cff-text-muted text-sm text-gray-500"
        >
          {{ t('empty') }}
        </div>
      </div>

      <!-- COPY BUTTON -->
      <!-- <button
                @click="copyToClipboard"
                type="button"
                class="px-3 py-1.5 h-full text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition-colors"
                :class="{ 'bg-green-50 border-green-300 text-green-700': copied }"
            >
                <span v-if="!copied" class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    Copy
                </span>
                <span v-else class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Copied!
                </span>
            </button> -->
    </div>
  </div>
</template>

<script>
import { Fieldtype } from '@statamic/cms'
import { trans } from './translations'

export default {
  mixins: [Fieldtype],

  data() {
    return {
      copied: false,
      copyTimeout: null,
      locale: this.meta?.locale,
      config: this.config,
    }
  },

  computed: {
    cpBaseRoute() {
      const cpRoute = this.meta?.cp_route ?? 'cp'
      const trimmed = String(cpRoute).replace(/^\/+|\/+$/g, '')
      return `/${trimmed}`
    },

    assetBaseUrl() {
      if (!this.meta?.asset_base_url) {
        return null
      }

      return String(this.meta.asset_base_url).replace(/\/+$/g, '')
    },

    assetContainer() {
      return this.config?.container ?? 'assets'
    },

    files() {
      return this.normalizeValueArray(this.value).map(item => this.buildFileEntry(item))
        .filter(Boolean)
    },

    hasFiles() {
      return this.files.length > 0
    },

    dataToCopy() {
      if (!this.hasFiles) {
        return ''
      }

      return this.files.map(file => file.path).join('\n')
    },
  },

  beforeUnmount() {
    if (this.copyTimeout) {
      clearTimeout(this.copyTimeout)
    }
  },

  methods: {
    t(key) {
      return trans(key, this.locale)
    },
    normalizeValueArray(value) {
      if (!value) {
        return []
      }

      if (Array.isArray(value)) {
        return value.filter(Boolean)
      }

      if (typeof value === 'object' && value.path) {
        return [value.path]
      }

      return [value]
    },

    buildFileEntry(raw) {
      const path = this.extractPath(raw)

      if (!path) {
        return null
      }

      const cleanedPath = String(path).replace(/^\/+/, '')
      const { fileName, folderPath } = this.splitPath(cleanedPath)
      const encodedPath = this.encodePath(cleanedPath)

      return {
        path: cleanedPath,
        fileName,
        folderPath,
        publicUrl: this.assetBaseUrl ? `${this.assetBaseUrl}/${cleanedPath}` : null,
        cpAssetUrl: `${this.cpBaseRoute}/assets/browse/${this.assetContainer}/${encodedPath}/edit`,
        cpBrowseUrl: `${this.cpBaseRoute}/assets/browse/${this.assetContainer}${folderPath ? `/${this.encodePath(folderPath)}` : ''}`,
      }
    },

    extractPath(entry) {
      if (!entry) {
        return ''
      }

      if (typeof entry === 'string') {
        return entry
      }

      if (typeof entry === 'object' && entry.path) {
        return entry.path
      }

      return String(entry)
    },

    splitPath(path) {
      const segments = path.split('/').filter(Boolean)
      const fileName = segments.length > 0 ? segments[segments.length - 1] : path
      const folderPath = segments.slice(0, -1).join('/')

      return {
        fileName,
        folderPath,
      }
    },

    encodePath(path) {
      return path
        .split('/')
        .filter(Boolean)
        .map(segment => encodeURIComponent(segment))
        .join('/')
    },

    async copyToClipboard() {
      try {
        if (navigator.clipboard && navigator.clipboard.writeText) {
          await navigator.clipboard.writeText(this.dataToCopy)
        } else {
          const textArea = document.createElement('textarea')
          textArea.value = this.dataToCopy
          textArea.style.position = 'fixed'
          textArea.style.left = '-999999px'
          document.body.appendChild(textArea)
          textArea.select()
          document.execCommand('copy')
          document.body.removeChild(textArea)
        }

        this.copied = true

        if (this.copyTimeout) {
          clearTimeout(this.copyTimeout)
        }

        this.copyTimeout = setTimeout(() => {
          this.copied = false
        }, 2000)
      } catch (error) {
        console.error('Failed to copy to clipboard:', error)
      }
    },
  },
}
</script>

<style>
.form-group:has(.custom-form-field-file-upload) .field-inner {
    display: none;
}

/* Dark mode: Statamic toggles a `.dark` class on an ancestor. The CP build
   has no Tailwind pipeline for our components, so dark: utilities never get
   generated — these explicit rules are what actually apply. Use Statamic's
   own theme variables so the surface matches native components exactly.
   Note: Statamic exposes --color-gray-850 (not -800) as the dark surface step. */
.dark .cff-data-box {
    background-color: var(--color-gray-850);
    border-color: var(--color-gray-700);
}
.dark .cff-data-box .cff-text {
    color: var(--color-gray-200);
}
.dark .cff-data-box .cff-text-muted {
    color: var(--color-gray-400);
}
.dark .cff-btn {
    color: var(--color-gray-200);
    background-color: var(--color-gray-850);
    border-color: var(--color-gray-700);
}
.dark .cff-btn:hover {
    background-color: var(--color-gray-700);
}
.dark .cff-link-blue {
    color: var(--color-blue-300);
    background-color: var(--color-blue-900);
    border-color: var(--color-blue-700);
}
.dark .cff-link-blue:hover {
    background-color: var(--color-blue-800);
}
</style>
