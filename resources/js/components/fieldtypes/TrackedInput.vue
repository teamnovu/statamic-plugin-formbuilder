<template>
    <div class="relative w-full">
        <span v-if="isDirty" class="absolute novu-right-3 novu-mt-[0.4rem] novu-pointer-events-none novu-text-[rgb(67,169,255)]" aria-label="has changed">•</span>
        <Input
            v-bind="$attrs"
            :model-value="trackedValue"
            copyable
            :name="name"
            @update:model-value="onInput"
        />
    </div>
</template>

<script setup>
import { computed, getCurrentInstance, ref, watch } from 'vue';
import { Input } from '@statamic/cms/ui';

defineOptions({
    inheritAttrs: false,
});

const props = defineProps({
    name: {
        type: String,
        default: '',
    },
    modelValue: {
        type: String,
        default: undefined,
    },
    value: {
        type: String,
        default: undefined,
    },
});

const emit = defineEmits(['update:modelValue', 'input']);

const instance = getCurrentInstance();

const sourceValue = computed(() => {
    if (props.modelValue !== undefined) {
        return props.modelValue;
    }

    return props.value ?? '';
});

const trackedValue = ref(sourceValue.value);
const baselineValue = ref(sourceValue.value);

const isDirty = computed(() => {
    if (!trackedValue.value && !baselineValue.value) {
        return false;
    }

    return trackedValue.value !== baselineValue.value;
});

function onInput(value) {
    const nextValue = value ?? '';

    trackedValue.value = nextValue;
    emit('update:modelValue', nextValue);
    emit('input', nextValue);
}

watch(sourceValue, (nextValue) => {
    if (nextValue === baselineValue.value && nextValue === trackedValue.value) {
        return;
    }

    baselineValue.value = nextValue;
    trackedValue.value = nextValue;
});

watch(isDirty, (dirty) => {
    const dirtyState = instance?.proxy?.$dirty;

    if (!dirtyState) {
        return;
    }

    if (dirty) {
        dirtyState.add(props.name);
        return;
    }

    dirtyState.remove(props.name);
});
</script>
