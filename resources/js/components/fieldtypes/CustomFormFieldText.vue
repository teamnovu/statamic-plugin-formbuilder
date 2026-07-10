<!-- Form Builder control panel fieldtype. -->
<template>
  <div
    class="form-data-display"
    :style="value ? 'opacity: 1' : 'opacity: 0.4'"
  >
    <h3 class="text-base mb-2 custom-form-field-text">
      {{
        config.label?.find(element => element.handle === locale)?.value
          ?? config.label?.find(element => element.handle === 'en')?.value
          ?? config.label?.find(element => element.handle === 'de')?.value ?? config.display
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
        <pre
          class="
            cff-text text-sm text-gray-800 font-mono wrap-break-word
            whitespace-pre-wrap
          "
        >{{ formattedData }}</pre>
      </div>

      <!-- COPY BUTTON -->
      <button
        type="button"
        class="
          cff-copy-btn px-3 py-1.5 text-xs font-medium text-gray-700 bg-white
          border-gray-300 rounded-sm
          hover:bg-gray-50
          focus:ring-blue-500
          h-full border transition-colors
          focus:ring-2 focus:ring-offset-1 focus:outline-none
        "
        :class="{ 'cff-copy-btn--copied bg-green-50 border-green-300 text-green-700': copied }"
        @click="copyToClipboard"
      >
        <span
          v-if="!copied"
          class="gap-1.5 flex items-center"
        >
          <svg
            class="w-4 h-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"
            />
          </svg>
          Copy
        </span>
        <span
          v-else
          class="gap-1.5 flex items-center"
        >
          <svg
            class="w-4 h-4"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M5 13l4 4L19 7"
            />
          </svg>
          Copied!
        </span>
      </button>
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
      DATA: this.meta,
      locale: this.meta?.locale,
      test: this.meta?.test,
      config: this.config,
    }
  },

  computed: {

    formattedData() {
      if (!this.value) {
        return this.t('empty')
      }

      if (typeof this.value === 'string') {
        return this.value
      }

      if (typeof this.value === 'object') {
        return JSON.stringify(this.value, null, 2)
      }

      return String(this.value)
    },

    dataToCopy() {
      if (!this.value) {
        return ''
      }

      if (typeof this.value === 'string') {
        return this.value
      }

      if (typeof this.value === 'object') {
        return JSON.stringify(this.value, null, 2)
      }

      return String(this.value)
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
    async copyToClipboard() {
      try {
        if (navigator.clipboard && navigator.clipboard.writeText) {
          await navigator.clipboard.writeText(this.dataToCopy)
        } else {
          // Fallback for older browsers
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
.form-group:has(.custom-form-field-text) .field-inner {

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
.dark .cff-copy-btn {
    color: var(--color-gray-200);
    background-color: var(--color-gray-850);
    border-color: var(--color-gray-700);
}
.dark .cff-copy-btn:hover {
    background-color: var(--color-gray-700);
}
.dark .cff-copy-btn--copied {
    color: var(--color-green-300);
    background-color: var(--color-green-900);
    border-color: var(--color-green-700);
}
</style>
