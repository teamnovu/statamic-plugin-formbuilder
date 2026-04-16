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
                    @input="onInput(site?.handle, $event)"
                />
            </div>
        </div>
    </div>
</template>

<script>
export default {
    mixins: [Fieldtype],
    props: {
        name: {
            type: String,
            default: '',
        },
        value: {
            type: Array,
            default: () => [],
        },
        path: {
            type: Array,
            default: () => [],
        },
    },
    computed: {
        sitesList() {
            const sites = this.meta?.sites;
            if (Array.isArray(sites)) {
                return sites;
            }
            if (sites && typeof sites === 'object') {
                return Object.values(sites);
            }
            return [];
        },
        normalizedValue() {
            return Array.isArray(this.value) ? this.value : [];
        },
        baseBardConfig() {
            return this.meta?.bardConfig || {};
        },
        baseBardMeta() {
            return this.meta?.bardMeta || {};
        },
    },
    methods: {
        valueForSite(handle) {
            const entry = this.normalizedValue.find((item) => item?.handle === handle);
            return entry?.value ?? [];
        },
        onInput(handle, bardValue) {
            const next = this.sitesList.reduce((acc, site) => {
                if (site?.handle === handle) {
                    acc.push({ handle, value: bardValue });
                    return acc;
                }

                const existing = this.normalizedValue.find((item) => item?.handle === site?.handle);
                if (existing) {
                    acc.push(existing);
                }

                return acc;
            }, []);

            this.commitValue(next);
        },
        commitValue(nextValue) {
            if (this.config?.debounce) {
                this.updateDebounced(nextValue);
                return;
            }

            this.update(nextValue);
        },
        bardConfigFor(site) {
            const base = this.baseBardConfig?.[site?.handle] || this.baseBardConfig || {};

            return JSON.parse(JSON.stringify({
                ...base,
                handle: this.nestedHandle(site),
                display: base.display || this.config?.display || this.handle,
            }));
        },
        bardMetaFor(site) {
            const meta = this.baseBardMeta?.[site?.handle] || this.baseBardMeta || {};

            return JSON.parse(JSON.stringify(meta));
        },
        nestedHandle(site) {
            return `${this.handle}-${site?.handle}`;
        },
        namePrefixFor(site) {
            if (this.name) {
                return `${this.name}[${site?.handle}]`;
            }

            return `${this.handle}[${site?.handle}]`;
        },
        fieldPathPrefixFor(site) {
            if (this.fieldPathPrefix) {
                return `${this.fieldPathPrefix}.${site?.handle}`;
            }

            return `${this.handle}.${site?.handle}`;
        },
        fieldIdFor(site) {
            return `field_${this.nestedHandle(site)}`;
        },
    },
};
</script>
