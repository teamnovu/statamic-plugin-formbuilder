<template>
    <div class="form-data-display" :style="props.value ? 'opacity: 1' : 'opacity: 0.4'">
        <h3 class="text-base mb-2 custom-form-field-text">
            {{ displayLabel }}:
        </h3>
        <div class="flex items-center gap-2 h-fit justify-between mb-2">
            <div class="bg-gray-50 grow border border-gray-200 rounded px-4 py-2">
                <pre class="text-sm whitespace-pre-wrap wrap-break-word font-mono">{{ formattedData }}</pre>
            </div>

            <button
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
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed, onBeforeUnmount, ref } from 'vue';
import { Fieldtype } from '@statamic/cms';

const props = defineProps(Fieldtype.props);

const copied = ref(false);
const copyTimeout = ref(null);

const locale = computed(() => props.meta?.locale);

const formattedData = computed(() => {
    if (!props.value) {
        return 'The user left this field empty';
    }

    if (typeof props.value === 'string') {
        return props.value;
    }

    if (typeof props.value === 'object') {
        return JSON.stringify(props.value, null, 2);
    }

    return String(props.value);
});

const dataToCopy = computed(() => {
    if (!props.value) {
        return '';
    }

    if (typeof props.value === 'string') {
        return props.value;
    }

    if (typeof props.value === 'object') {
        return JSON.stringify(props.value, null, 2);
    }

    return String(props.value);
});

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

async function copyToClipboard() {
    if (!navigator.clipboard?.writeText) {
        return;
    }

    try {
        await navigator.clipboard.writeText(dataToCopy.value);
        copied.value = true;

        if (copyTimeout.value) {
            clearTimeout(copyTimeout.value);
        }

        copyTimeout.value = setTimeout(() => {
            copied.value = false;
        }, 2000);
    } catch (error) {
        console.error('Failed to copy to clipboard:', error);
    }
}

onBeforeUnmount(() => {
    if (copyTimeout.value) {
        clearTimeout(copyTimeout.value);
    }
});
</script>

<style>
.form-group:has(.custom-form-field-text) .field-inner {
    display: none;
}
</style>
