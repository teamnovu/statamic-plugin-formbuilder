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
                <bard-fieldtype
                    :key="nestedHandle(site?.handle)"
                    :handle="site?.handle"
                    :value="valueForSite(site?.handle)"
                    :config="props.config"
                    :meta="props.meta"
                    :name-prefix="props.name"
                    :field-path-prefix="props.handle"
                    @input="onInput(site?.handle, $event)"
                />
       

             
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Fieldtype } from '@statamic/cms';


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

function valueForSite(siteHandle: string) {
    const siteValue = normalizedValue.value.find((entry) => entry?.handle === siteHandle);
    return siteValue?.value ?? '';
}

function nestedHandle(siteHandle: string) {
    return `${props.handle}-${siteHandle}`;
}

function commitValue(nextValue) {
    if (props.config?.debounce) {
        updateDebounced(nextValue);
        return;
    }

    update(nextValue);
}

function onInput(siteHandle: string, inputValue: string ) {
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
