<template>
    <div class="form-data-display custom-form-field-text" :style="hasFiles ? 'opacity: 1' : 'opacity: 0.4'">
        <h3 class="text-base mb-2 custom-form-field-file-upload">
            {{ displayLabel }}:
        </h3>

        <div class="flex items-center gap-2 h-fit justify-between mb-2">
            <div class="bg-gray-100 grow rounded px-4 py-2">
                <div v-if="hasFiles" class="space-y-3">
                    <div v-for="file in files" :key="file.path" class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-medium wrap-break-word">{{ file.fileName }}</p>
                            <p class="text-xs text-gray-600 break-all">{{ file.path }}</p>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            
                            
                            <ButtonGroup v-if="file.fileName !== 'null' && (file.path !== 'null' )" >
                                <Button  v-if="file.publicUrl"
                                :href="file.publicUrl" size="base" variant="primary"  text="Base" >{{__('formbuilder::form.open')}}</Button>  

                                <Button  v-if="file.cpAssetUrl"
                                :href="file.cpAssetUrl" size="base" variant="default"  text="Base" >{{__('formbuilder::form.open_in_cp')}}</Button> 
                                <Button  v-if="file.cpBrowseUrl"
                                :href="file.cpBrowseUrl" size="base" variant="default"  text="Base" >{{__('formbuilder::form.view_folder')}}</Button> 
                            </ButtonGroup>
                        </div>
                    </div>
                </div>
                <div v-else>
                   {{__('formbuilder::form.no_files')}}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Fieldtype } from '@statamic/cms';
import { Button, ButtonGroup } from '@statamic/cms/ui';
const props = defineProps(Fieldtype.props);

const locale = computed(() => props.meta?.locale);

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

const cpBaseRoute = computed(() => {
    const cpRoute = props.meta?.cp_route ?? 'cp';
    const trimmedRoute = String(cpRoute).replace(/^\/+|\/+$/g, '');
    return `/${trimmedRoute}`;
});

const assetBaseUrl = computed(() => {
    if (!props.meta?.asset_base_url) {
        return null;
    }

    return String(props.meta.asset_base_url).replace(/\/+$/g, '');
});

const assetContainer = computed(() => props.config?.container ?? 'assets');

const files = computed(() => {
    return normalizeValueArray(props.value)
        .map((item) => buildFileEntry(item))
        .filter(Boolean);
});

const hasFiles = computed(() => files.value.length > 0);

function normalizeValueArray(value) {
    if (!value) {
        return [];
    }

    if (Array.isArray(value)) {
        return value.filter(Boolean);
    }

    if (typeof value === 'object' && value.path) {
        return [value.path];
    }

    return [value];
}

function buildFileEntry(rawPath) {
    const filePath = extractPath(rawPath);

    if (!filePath) {
        return null;
    }

    const cleanedPath = String(filePath).replace(/^\/+/, '');
    const { fileName, folderPath } = splitPath(cleanedPath);
    const encodedPath = encodePath(cleanedPath);

    return {
        path: cleanedPath,
        fileName,
        folderPath,
        publicUrl: assetBaseUrl.value ? `${assetBaseUrl.value}/${cleanedPath}` : null,
        cpAssetUrl: `${cpBaseRoute.value}/assets/browse/${assetContainer.value}/${encodedPath}/edit`,
        cpBrowseUrl: `${cpBaseRoute.value}/assets/browse/${assetContainer.value}${folderPath ? `/${encodePath(folderPath)}` : ''}`,
    };
}

function extractPath(entry) {
    if (!entry) {
        return '';
    }

    if (typeof entry === 'string') {
        return entry;
    }

    if (typeof entry === 'object' && entry.path) {
        return entry.path;
    }

    return String(entry);
}

function splitPath(path) {
    const segments = path.split('/').filter(Boolean);
    const fileName = segments.length > 0 ? segments[segments.length - 1] : path;
    const folderPath = segments.slice(0, -1).join('/');

    return {
        fileName,
        folderPath,
    };
}

function encodePath(path) {
    return path
        .split('/')
        .filter(Boolean)
        .map((segment) => encodeURIComponent(segment))
        .join('/');
}
</script>

<style>
.form-group:has(.custom-form-field-text)  div[data-ui-field-text] label {
    display: none;
}
</style>
