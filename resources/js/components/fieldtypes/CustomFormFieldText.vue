<template>
    <div class="form-data-display" :style="props.value ? 'opacity: 1' : 'opacity: 0.4'">
        <h3 class="text-base mb-2 custom-form-field-text">
            {{ displayLabel }}:
        </h3>
        <Input copyable readonly :model-value="formattedData" />
     
    </div>
</template>

<script setup>
import { computed, onBeforeUnmount, ref } from 'vue';
import { Fieldtype } from '@statamic/cms';
import { Input } from '@statamic/cms/ui';
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
.form-group:has(.custom-form-field-text)  div[data-ui-field-text] label {
    display: none;
}
</style>
