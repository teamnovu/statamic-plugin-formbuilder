<template>
    <div class="form-data-display" :style="props.value ? 'opacity: 1' : 'opacity: 0.4'">
        <h3 class="text-base mb-2 custom-form-field-text">
            {{ displayLabel }}: 
        </h3>
        <Input :disabled="isEmpty" :copyable="!isEmpty" readonly :model-value="formattedData" />
     
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Fieldtype } from '@statamic/cms';
import { Input } from '@statamic/cms/ui';
const props = defineProps(Fieldtype.props);



const locale = computed(() => props.meta?.locale);
const isEmpty = computed(() => typeof props.value === 'undefined' || props.value === 'undefined' || props.value === null || props.value === '');


const formattedData = computed(() => {

    if (isEmpty.value) {
        return __('formbuilder::form.no_text');
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


</script>

<style>
.form-group:has(.custom-form-field-text)  div[data-ui-field-text] label {
    display: none;
}
</style>
