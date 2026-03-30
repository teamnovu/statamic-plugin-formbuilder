<template>
    <div class="form-data-display">
        <h3 class="text-base mb-2 custom-form-field-text">{{ displayLabel }}:</h3>
        <div class="flex items-center gap-2 h-fit justify-between mb-2">
            <div class="bg-gray-50 grow border border-gray-200 rounded px-4 py-2">
                <div v-if="options.length > 0" class="text-sm text-gray-800">
                    <ul class="list-none space-y-1.5">
                        <li v-for="option in options" :key="option.key" class="flex items-center gap-2">
                            <svg v-if="option.key === selectedValue" class="w-4 h-4 text-green-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <circle cx="10" cy="10" r="7" fill="none" stroke="currentColor" stroke-width="2" />
                                <circle cx="10" cy="10" r="4" />
                            </svg>

                            <svg v-else class="w-4 h-4 text-gray-400 shrink-0" fill="none" viewBox="0 0 20 20">
                                <circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="2" />
                            </svg>

                            <span>{{ optionLabel(option) }}</span>
                        </li>
                    </ul>
                </div>
                <div v-else-if="selectedValue" class="text-sm text-gray-800">
                    {{ selectedValue }}
                </div>
                <div v-else>
                    -
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
const selectedValue = computed(() => (props.value ? String(props.value) : ''));

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
.form-group:has(.custom-form-field-text)  div[data-ui-field-text] label {
    display: none;
}
</style>
