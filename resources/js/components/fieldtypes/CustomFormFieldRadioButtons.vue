<!-- Form Builder control panel fieldtype. -->
<template>
  <div class="form-data-display">
    <h3 class="text-base mb-2 custom-form-field-text">
      {{ config.label?.find(element => element.handle === locale)?.value ?? config.label?.find(element => element.handle === 'en')?.value ?? config.label?.find(element => element.handle === 'de')?.value ?? config.display }}:
    </h3>
    <div class="gap-2 mb-2 flex h-fit items-center justify-between">
      <div
        class="
          cff-data-box bg-gray-50 border-gray-200 rounded-sm px-4 py-2 grow
          border
        "
      >
        <div
          v-if="options.length > 0"
          class="cff-text text-sm text-gray-800"
        >
          <ul class="space-y-1.5 list-none">
            <li
              v-for="(option, index) in options"
              :key="index"
              class="gap-2 flex items-center"
            >
              <!-- selected: filled circle -->
              <svg
                v-if="option.key === value"
                class="w-4 h-4 text-green-600 shrink-0"
                fill="currentColor"
                viewBox="0 0 20 20"
              >
                <circle
                  cx="10"
                  cy="10"
                  r="7"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                />
                <circle
                  cx="10"
                  cy="10"
                  r="4"
                />
              </svg>

              <!-- not selected: empty circle -->
              <svg
                v-else
                class="w-4 h-4 text-gray-400 shrink-0"
                fill="none"
                viewBox="0 0 20 20"
              >
                <circle
                  cx="10"
                  cy="10"
                  r="7"
                  stroke="currentColor"
                  stroke-width="2"
                />
              </svg>

              <span v-html="option.text.find(element => element.handle === locale)?.value ?? option.text.find(element => element.handle === 'en')?.value ?? option.text.find(element => element.handle === 'de')?.value ?? option.key" />
            </li>
          </ul>
        </div>
        <div
          v-else-if="value"
          class="cff-text text-sm text-gray-800"
        >
          {{ value }}
        </div>
        <div
          v-else
          class="cff-text-muted text-sm text-gray-500"
        >
          -
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Fieldtype } from '@statamic/cms'

export default {
  mixins: [Fieldtype],

  data() {
    return {
      copied: false,
      copyTimeout: null,
      locale: this.meta?.locale ?? [],
      options: this.meta?.options ?? [],
      config: this.config,
    }
  },

  computed: {
    dataToCopy() {
      if (!this.value) {
        return ''
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
