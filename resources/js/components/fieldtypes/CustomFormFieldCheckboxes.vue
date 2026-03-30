<template>
    <div class="form-data-display">
        <h3 class="text-base mb-2 custom-form-field-text">{{ displayLabel }}:</h3>
        <div class="flex items-center gap-2 h-fit justify-between mb-2">
            <div class="bg-gray-50 grow border border-gray-200 rounded px-4 py-2">
                <div v-if="selectedValues.length > 0" class="text-sm text-gray-800">
                    <ul class="list-none space-y-1.5">
                        <li v-for="option in options" :key="option.key" class="flex items-center gap-2">
                            <svg v-if="valueIsPresent(option.key)" class="w-4 h-4 text-green-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>

                            <svg v-else class="w-4 h-4 text-red-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>

                            <span>{{ optionLabel(option) }}</span>
                        </li>
                    </ul>
                </div>
                <div v-else-if="props.value" class="text-sm text-gray-800">
                    {{ formattedData }}
                </div>
                <div v-else>
                    The user left this field empty
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Fieldtype } from '@statamic/cms';

const props = defineProps(Fieldtype.props);

const locale = computed(() => props.meta?.locale);
const options = computed(() => (Array.isArray(props.meta?.options) ? props.meta.options : []));

const displayLabel = computed(() => {
    const labels = Array.isArray(props.config?.label) ? props.config.label : [];

    return (
        labels.find((element) => element?.handle === locale.value)?.value
        ?? labels.find((element) => element?.handle === 'en')?.value
        ?? labels.find((element) => element?.handle === 'de')?.value
        ?? props.config?.display
        ?? props.handle
    );
});

const selectedValues = computed(() => toArray(props.value));

const formattedData = computed(() => {
    if (!props.value) {
        return 'The user left this field empty';
    }

    if (Array.isArray(props.value)) {
        return props.value.length > 0 ? props.value.join(', ') : 'No options selected';
    }

    if (typeof props.value === 'string') {
        return props.value;
    }

    if (typeof props.value === 'object') {
        return JSON.stringify(props.value, null, 2);
    }

    return String(props.value);
});

function valueIsPresent(value) {
    return selectedValues.value.includes(value);
}

function toArray(value) {
    if (Array.isArray(value)) {
        return value;
    }

    if (!value) {
        return [];
    }

    if (typeof value === 'string') {
        return value
            .split(',')
            .map((entry) => entry.trim())
            .filter(Boolean);
    }

    return [String(value)];
}

function optionLabel(option) {
    const labels = Array.isArray(option?.text) ? option.text : [];

    return (
        labels.find((element) => element?.handle === locale.value)?.value
        ?? labels.find((element) => element?.handle === 'en')?.value
        ?? labels.find((element) => element?.handle === 'de')?.value
        ?? option?.key
    );
}
</script>
<style>
.form-group:has(.custom-form-field-text) .field-inner {
    display: none;
}
</style>
