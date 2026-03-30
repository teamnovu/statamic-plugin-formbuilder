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
                            <a
                                v-if="file.publicUrl"
                                class="px-2 py-1 text-xs font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition-colors"
                                :href="file.publicUrl"
                                target="_blank"
                                rel="noopener"
                            >
                                Open
                            </a>
                            <a
                                class="px-2 py-1 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition-colors"
                                :href="file.cpAssetUrl"
                                target="_blank"
                                rel="noopener"
                            >
                                Open in CP
                            </a>
                            <a
                                class="px-2 py-1 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 transition-colors"
                                :href="file.cpBrowseUrl"
                                target="_blank"
                                rel="noopener"
                            >
                                View Folder
                            </a>
                        </div>
                    </div>
                </div>
                <div v-else>
                    The user left this field empty
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Fieldtype } from '@statamic/cms';

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
