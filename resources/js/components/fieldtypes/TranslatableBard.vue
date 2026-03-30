<template>
    <div class="pb-4 w-full">
        <div class="flex gap-4 flex-col bard-fieldtype">
            <div
                v-for="site in sitesList"
                :key="site?.handle"
                class="space-y-1"
            >
                <label
                    :for="fieldIdFor(site)"
                    class="publish-field-label mb-1"
                >
                    {{ site?.name }}:
                </label>

                <bard-fieldtype
                    :key="nestedHandle(site)"
                    :handle="nestedHandle(site)"
                    :value="valueForSite(site?.handle)"
                    :config="bardConfigFor(site)"
                    :meta="bardMetaFor(site)"
                    :name-prefix="namePrefixFor(site)"
                    :field-path-prefix="fieldPathPrefixFor(site)"
                    :meta-path-prefix="metaPathPrefixFor(site)"
                    @input="onInput(site?.handle, $event)"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Fieldtype } from '@statamic/cms';


const emit = defineEmits(Fieldtype.emits);
const props = defineProps(Fieldtype.props);
const { name, update, updateDebounced } = Fieldtype.use(emit, props);

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
const baseBardConfig = computed(() => props.meta?.bardConfig ?? {});
const baseBardMeta = computed(() => props.meta?.bardMeta ?? {});

function cloneValue(value) {
    if (typeof structuredClone === 'function') {
        return structuredClone(value);
    }

    return JSON.parse(JSON.stringify(value));
}

function valueForSite(siteHandle) {
    const entry = normalizedValue.value.find((item) => item?.handle === siteHandle);
    return entry?.value ?? [];
}

function commitValue(nextValue) {
    if (props.config?.debounce) {
        updateDebounced(nextValue);
        return;
    }

    update(nextValue);
}

function onInput(siteHandle, bardValue) {
    const nextValue = normalizedValue.value.map((entry) => {
        if (entry?.handle !== siteHandle) {
            return entry;
        }

        return {
            ...entry,
            value: bardValue,
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
                    value: bardValue,
                },
            ]
    );
}

function nestedHandle(site) {
    return `${props.handle}-${site?.handle}`;
}

function bardConfigFor(site) {
    const config = baseBardConfig.value;
    const base = config?.[site?.handle] ?? config ?? {};

    return cloneValue({
        ...base,
        handle: nestedHandle(site),
        display: base.display || props.config?.display || props.handle,
    });
}

function bardMetaFor(site) {
    const meta = baseBardMeta.value;
    const base = meta?.[site?.handle] ?? meta ?? {};

    return cloneValue(base);
}

function namePrefixFor(site) {
    return `${name.value}[${site?.handle}]`;
}

function fieldPathPrefixFor(site) {
    if (props.fieldPathPrefix) {
        return `${props.fieldPathPrefix}.${site?.handle}`;
    }

    return `${props.handle}.${site?.handle}`;
}

function metaPathPrefixFor(site) {
    if (props.metaPathPrefix) {
        return `${props.metaPathPrefix}.${site?.handle}`;
    }

    return `${props.handle}.${site?.handle}`;
}

function fieldIdFor(site) {
    return `field_${nestedHandle(site)}`;
}
</script>
