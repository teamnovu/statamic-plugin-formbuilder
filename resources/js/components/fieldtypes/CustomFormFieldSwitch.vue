<template>
    <div class="form-data-display">
        <h3 class="text-base mb-2 custom-form-field-text">{{ displayLabel }}:</h3>
     
        <Switch :name="displayLabel" v-model="selectedValue" disabled readonly />
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Fieldtype } from '@statamic/cms';
import { Switch } from '@statamic/cms/ui';


const props = defineProps(Fieldtype.props);

const locale = computed(() => props.meta?.locale);
const selectedValue = computed(() => (props.value ? Boolean(props.value) : false));

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
