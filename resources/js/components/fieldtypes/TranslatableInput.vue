<template>
    <div class="pb-4">
        <div class="flex gap-4 flex-col">
            <div v-for="site in sitesList" :key="site?.handle">
                <label
                    :for="`${site?.handle}`"
                    class="publish-field-label mb-1"
                >
                    {{ site?.name }}:
                </label>

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
import { computed } from 'vue';
import { Fieldtype } from '@statamic/cms';
import { Input } from '@statamic/cms/ui';

const emit = defineEmits(Fieldtype.emits);
const props = defineProps(Fieldtype.props);
const { name, isReadOnly, update, updateDebounced } = Fieldtype.use(emit, props);

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

const normalizedValue = computed(() => (Array.isArray(props.value) ? props.value : []));

function valueForSite(siteHandle) {
    const siteValue = normalizedValue.value.find((entry) => entry?.handle === siteHandle);
    return siteValue?.value ?? '';
}

function commitValue(nextValue) {
    if (props.config?.debounce) {
        updateDebounced(nextValue);
        return;
    }

    update(nextValue);
}

function onInput(siteHandle, inputValue) {
    const nextValue = normalizedValue.value.map((entry) => {
        if (entry?.handle !== siteHandle) {
            return entry;
        }

        return {
            ...entry,
            value: inputValue,
        };
    });

    const hasSiteValue = nextValue.some((entry) => entry?.handle === siteHandle);

    commitValue(
        hasSiteValue
            ? nextValue
            : [
                ...nextValue,
                {
                    handle: siteHandle,
                    value: inputValue,
                },
            ]
    );
}
</script>
