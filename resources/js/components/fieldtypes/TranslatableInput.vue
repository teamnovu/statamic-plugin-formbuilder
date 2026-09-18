<!-- Form Builder control panel fieldtype. -->
<template>
  <div class="pb-4">
    <div class="gap-4 flex flex-col">
      <div
        v-for="site in sitesList"
        :key="site?.handle"
      >
        <div class="mb-1 flex items-center justify-between gap-3">
          <label
            :for="`field_${props.handle}-${site?.handle}`"
            class="publish-field-label mb-0"
          >
            {{ site?.name }}:
          </label>

          <Button
            v-if="showTranslateButton(site?.handle)"
            type="button"
            size="xs"
            variant="ghost"
            class="shrink-0"
            :disabled="isTranslateDisabled(site?.handle)"
            :text="translateButtonText(site?.handle)"
            @click="translateSite(site?.handle)"
          />
        </div>

        <Input
          :id="`field_${props.handle}-${site?.handle}`"
          :name="`${name}[${site?.handle}]`"
          :model-value="valueForSite(site?.handle)"
          :read-only="isReadOnly"
          @update:model-value="onInput(site?.handle, $event)"
          @focus="$emit('focus')"
          @blur="$emit('blur')"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Fieldtype } from '@statamic/cms'
import { Button, Input } from '@statamic/cms/ui'
import { translateText } from './oneClickTranslation.js'

const emit = defineEmits(Fieldtype.emits)
const props = defineProps(Fieldtype.props)
const { name, isReadOnly, update, updateDebounced } = Fieldtype.use(emit, props)

const translatingSites = ref({})

const sitesList = computed(() => {
  const sites = props.meta?.sites

  if (Array.isArray(sites)) {
    return sites
  }

  if (sites && typeof sites === 'object') {
    return Object.values(sites)
  }

  return []
})

const oneClickTranslationEnabled = computed(() => props.meta?.oneClickTranslation === true)

const defaultSiteHandle = computed(() => props.meta?.defaultSite ?? null)

const translationLabels = computed(() => props.meta?.oneClickTranslationLabels ?? {})

const normalizedValue = computed(() => (Array.isArray(props.value) ? props.value : []))

function valueForSite(siteHandle) {
  const siteValue = normalizedValue.value.find(entry => entry?.handle === siteHandle)
  return siteValue?.value ?? ''
}

function showTranslateButton(siteHandle) {
  return oneClickTranslationEnabled.value
    && defaultSiteHandle.value
    && siteHandle !== defaultSiteHandle.value
}

function isTranslateDisabled(siteHandle) {
  return isReadOnly.value
    || !String(valueForSite(defaultSiteHandle.value)).trim()
    || translatingSites.value[siteHandle] === true
}

function translateButtonText(siteHandle) {
  if (translatingSites.value[siteHandle]) {
    return translationLabels.value.translating ?? 'Translating…'
  }

  return translationLabels.value.translate ?? 'Translate'
}

function commitValue(nextValue) {
  if (props.config?.debounce) {
    updateDebounced(nextValue)
    return
  }

  update(nextValue)
}

function onInput(siteHandle, inputValue) {
  const nextValue = normalizedValue.value.map((entry) => {
    if (entry?.handle !== siteHandle) {
      return entry
    }

    return {
      ...entry,
      value: inputValue,
    }
  })

  const hasSiteValue = nextValue.some(entry => entry?.handle === siteHandle)

  commitValue(
    hasSiteValue
      ? nextValue
      : [
          ...nextValue,
          {
            handle: siteHandle,
            value: inputValue,
          },
        ],
  )
}

async function translateSite(siteHandle) {
  if (!showTranslateButton(siteHandle) || isTranslateDisabled(siteHandle)) {
    return
  }

  translatingSites.value = {
    ...translatingSites.value,
    [siteHandle]: true,
  }

  try {
    const translated = await translateText(
      valueForSite(defaultSiteHandle.value),
      siteHandle,
      translationLabels.value,
    )

    if (translated !== null) {
      onInput(siteHandle, translated)
    }
  } finally {
    translatingSites.value = {
      ...translatingSites.value,
      [siteHandle]: false,
    }
  }
}
</script>
