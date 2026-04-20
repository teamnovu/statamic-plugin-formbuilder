<template>
    <div class="pb-4">
        <div class="flex gap-4 flex-col">
            <div v-for="site in sitesList" :key="site?.handle">
                <label
                    :for="`field_${props.handle}-${site?.handle}`"
                    class="publish-field-label mb-1"
                >
                    {{ site?.name }}:
                </label>

                <bard-fieldtype
                    :id="`field_${props.handle}-${site?.handle}`"
                    :handle="`${props.handle}__${site?.handle}`"
                    :config="bardConfig"
                    :meta="props.meta"
                    :value="valueForSite(site?.handle)"
                    :read-only="isReadOnly"
                    @update:value="onBardUpdate(site?.handle, $event)"
                    @update:meta="emit('update:meta', $event)"
                    @focus="emit('focus')"
                    @blur="emit('blur')"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Fieldtype } from '@statamic/cms';

const emit = defineEmits(Fieldtype.emits);
const props = defineProps(Fieldtype.props);
const { isReadOnly, update, updateDebounced } = Fieldtype.use(emit, props);

const ENABLED_BUTTONS = ['h3', 'bold', 'italic', 'anchor'];

const sitesList = computed(() => {
    const sites = props.meta?.sites;

    if (Array.isArray(sites)) {
        return sites;
    }

    if (sites && typeof sites === 'object') {
        return Object.values(sites);
    }

    return [];
});

const bardConfig = computed(() => ({
    ...(props.config ?? {}),
    buttons: ENABLED_BUTTONS,
    toolbar_mode: 'fixed',
    sets: [],
    fullscreen: false,
    inline: false,
    save_html: false,
    word_count: false,
    character_limit: 0,
    reading_time: false,
}));

const normalizedValue = computed(() => (Array.isArray(props.value) ? props.value : []));

function valueForSite(siteHandle) {
    const siteValue = normalizedValue.value.find((entry) => entry?.handle === siteHandle);
    return Array.isArray(siteValue?.value) ? siteValue.value : [];
}

function commitValue(nextValue) {
    if (props.config?.debounce) {
        updateDebounced(nextValue);
        return;
    }

    update(nextValue);
}

function onBardUpdate(siteHandle, bardValue) {
    const base = normalizedValue.value;
    const hasEntry = base.some((entry) => entry?.handle === siteHandle);

    const nextValue = hasEntry
        ? base.map((entry) =>
              entry?.handle === siteHandle
                  ? { ...entry, value: bardValue }
                  : entry,
          )
        : [
              ...base,
              {
                  handle: siteHandle,
                  value: bardValue,
              },
          ];

    commitValue(nextValue);
}
</script>
