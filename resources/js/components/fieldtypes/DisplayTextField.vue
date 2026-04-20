<template>
    <div class="display-text-field-cp text-gray-600 border-t pt-4 border-gray-200">
        <h3 v-if="resolvedTitle" class="text-base font-semibold mb-1">{{ resolvedTitle }}</h3>
        <div v-if="resolvedText" class="text-sm  pb-4" v-html="resolvedText"></div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Fieldtype } from '@statamic/cms';

const props = defineProps(Fieldtype.props);

type TranslatableValue = Array<{ handle?: string; value?: string | null | undefined }>;

const locale = computed(() => props.meta?.locale);

function resolveTranslatableValue(value: unknown): string | null {
    if (!Array.isArray(value)) {
        return typeof value === 'string' ? value : null;
    }

    const values = value as TranslatableValue;
    const activeLocale = locale.value;

    return (
        values.find((element) => element?.handle === activeLocale)?.value
        ?? values.find((element) => element?.handle === 'en')?.value
        ?? values.find((element) => element?.handle === 'de')?.value
        ?? null
    ) ?? null;
}

const resolvedTitle = computed(() => resolveTranslatableValue(props.config?.title));
const resolvedText = computed(() => resolveTranslatableValue(props.config?.text));
</script>

<style>
.display_text-fieldtype:has(.display-text-field-cp) div[data-ui-field-text] {
    display: none;
}
</style>