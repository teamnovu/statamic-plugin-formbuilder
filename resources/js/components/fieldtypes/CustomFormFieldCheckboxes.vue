<!-- Form Builder control panel fieldtype. -->
<template>
  <div class="form-data-display">
    <h3 class="text-base mb-2 custom-form-field-text">
      {{ config.label?.find(element => element.handle === locale)?.value ?? config.label?.find(element => element.handle === 'en')?.value ?? config.label?.find(element => element.handle === 'de')?.value ?? config.display }}:
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
          v-if="Array.isArray(toArray(value)) && toArray(value).length > 0"
          class="cff-text text-sm text-gray-800"
        >
          <ul class="space-y-1.5 list-none">
            <li
              v-for="(value, index) in options"
              :key="index"
              class="gap-2 flex items-center"
            >
              <!-- if present show green checkmark -->
              <svg
                v-if="valueIsPresent(value.key)"
                class="w-4 h-4 text-green-600 shrink-0"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  fill-rule="evenodd"
                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                  clip-rule="evenodd"
                />
              </svg>

              <!-- if not present show red x -->
              <svg
                v-else
                class="w-4 h-4 text-red-600 shrink-0"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <path
                  fill-rule="evenodd"
                  d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                  clip-rule="evenodd"
                />
              </svg>

              <span v-html="value.text.find(element => element.handle === locale)?.value ?? value.text.find(element => element.handle === 'en')?.value ?? value.text.find(element => element.handle === 'de')?.value ?? value.key" />
            </li>
          </ul>
        </div>
        <div
          v-else-if="value"
          class="cff-text text-sm text-gray-800"
        >
          {{ formattedData }}
        </div>
        <div
          v-else
          class="cff-text-muted text-sm text-gray-500"
        >
          {{ t('empty') }}
        </div>
      </div>
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
      locale: this.meta?.locale ?? [],
      options: this.meta?.options ?? [],
      test: this.meta?.test,
      config: this.config,
    }
  },

  computed: {
    formattedData() {
      if (!this.value) {
        return this.t('empty')
      }

      // Handle arrays (checkboxes typically return arrays)
      if (Array.isArray(this.value)) {
        if (this.value.length === 0) {
          return this.t('noOptionsSelected')
        }
        return this.value.join(', ')
      }

      // Handle single string value
      if (typeof this.value === 'string') {
        return this.value
      }

      // Handle objects (fallback)
      if (typeof this.value === 'object') {
        return JSON.stringify(this.value, null, 2)
      }

      return String(this.value)
    },

    dataToCopy() {
      if (!this.value) {
        return ''
      }

      // Handle arrays (checkboxes/select multiple)
      if (Array.isArray(this.value)) {
        return this.value.join(', ')
      }

      // Handle single string value
      if (typeof this.value === 'string') {
        return this.value
      }

      // Handle objects (fallback)
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
    valueIsPresent(value) {
      return this.toArray(this.value).includes(value)
    },
    toArray(value) {
      if (Array.isArray(value)) {
        return value
      }
      if (!value) {
        return false
      }
      // splt by comma
      return value.split(',')
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
.dark .cff-data-box .cff-text-muted {
    color: var(--color-gray-400);
}
</style>
