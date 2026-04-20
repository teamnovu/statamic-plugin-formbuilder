<template>
    <div class="form-data-display">
        <h3 class="text-base mb-2 custom-form-field-text">{{ displayLabel }}:</h3>
      
        
        <CheckboxGroup :name="displayLabel" :label="displayLabel" v-model="convertSelectedValue">
            <Checkbox :class="selectedValue.includes(option.key) ? 'bg-gray-100 py-2 rounded' : ''" class="px-2"  readonly v-for="option in options" :key="option.key" :label="optionLabel(option)" :value="option.key"  >
                <div class="[&_a]:font-bold [&_a]:text-blue-500 [&_a]:underline" v-html="optionLabel(option)"></div>
            </Checkbox>
        </CheckboxGroup>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Fieldtype } from '@statamic/cms';
import { CheckboxGroup, Checkbox } from '@statamic/cms/ui';

const convertSelectedValue = computed(() => (selectedValue.value ? selectedValue.value.split(',') : []));
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
