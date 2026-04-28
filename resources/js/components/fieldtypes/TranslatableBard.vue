<template>
    <div class="pb-4">
        <div class="flex gap-4 flex-col">
            <div v-for="site in sitesList" :key="site?.handle">
                <label
                    :for="`field_${props.handle}-${site?.handle}`"
                    class="publish-field-label mb-1"
                >
               {{ site?.name }}  {{ valueForSite(site?.handle) }}
                </label>

                <bard-fieldtype
                    :id="`field_${props.handle}-${site?.handle}`"
                    :handle="`${props.handle}__${site?.handle}`"
                    :config="bardConfig"
                    :meta="props.meta"
                    :value="valueForSite(site?.handle)"
                    :read-only="isReadOnly"
                    @update:value="onBardUpdate(site?.handle, $event)"
                    @update:meta="emit('update:meta', $event)"
                    @focus="emit('focus')"
                    @blur="emit('blur')"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { Fieldtype } from '@statamic/cms';

const emit = defineEmits(Fieldtype.emits);
const props = defineProps(Fieldtype.props);

const { isReadOnly, update, updateDebounced } = Fieldtype.use(emit, props);

const ENABLED_BUTTONS = ['h3', 'bold', 'italic', 'anchor'];

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

const bardConfig = computed(() => ({
    ...(props.config ?? {}),
    buttons: ENABLED_BUTTONS,
    toolbar_mode: 'fixed',
    sets: [],
    fullscreen: false,
    inline: true,
    save_html: true,
    word_count: false,
    character_limit: 0,
    reading_time: false,
}));

function normalizeTranslatableValue(rawValue) {
    if (rawValue == null) return [];

    const parsedValue =
        typeof rawValue === 'string'
            ? (() => {
                  // Sometimes the stored value can come back as a JSON string (eg. in YAML).
                  try {
                      return JSON.parse(rawValue);
                  } catch {
                      return null;
                  }
              })()
            : rawValue;

    if (parsedValue == null) return [];

    // Desired shape: [{ handle: string, value: any }]
    if (Array.isArray(parsedValue)) {
        return parsedValue
            .filter((entry) => entry && typeof entry === 'object')
            .map((entry) => {
                const handle = typeof entry.handle === 'string' ? entry.handle : null;
                if (!handle) return null;

                return {
                    handle,
                    value: entry.value ?? null,
                };
            })
            .filter(Boolean);
    }

    // Alternate shape: { [handle]: value }
    if (parsedValue && typeof parsedValue === 'object') {
        return Object.entries(parsedValue)
            .map(([handle, value]) => ({
                handle: String(handle),
                value,
            }))
            .filter((entry) => entry.handle.length > 0);
    }

    return [];
}

const normalizedValue = computed(() => normalizeTranslatableValue(props.value));

// Keep a local draft so edits across multiple locales don't clobber each other
// when Statamic updates `props.value` asynchronously (eg. debounce).
const draftValue = ref(normalizedValue.value);

watch(
    () => props.value,
    (next) => {
        draftValue.value = normalizeTranslatableValue(next);
    },
);

function stripHtmlToText(html) {
    if (typeof html !== 'string') return '';

    return html
        .replace(/<br\s*\/?>/gi, '\n')
        .replace(/<\/p\s*>/gi, '\n')
        .replace(/<[^>]*>/g, '')
        .replace(/&nbsp;/g, ' ')
        .trim();
}

function normalizeBardCpValue(rawValue) {
    if (rawValue == null) return [];

    if (Array.isArray(rawValue)) return rawValue;

    if (typeof rawValue === 'string') {
        // Sometimes YAML/JSON can roundtrip as a string.
        try {
            return normalizeBardCpValue(JSON.parse(rawValue));
        } catch {
            const text = stripHtmlToText(rawValue);

            if (!text) return [];

            return [
                {
                    type: 'paragraph',
                    content: [{ type: 'text', text }],
                },
            ];
        }
    }

    if (typeof rawValue === 'object') {
        // Guard against invalid legacy shapes like { type: 'doc', content: '<p>...</p>' }.
        if (rawValue.type === 'doc') {
            if (Array.isArray(rawValue.content)) return rawValue.content;

            if (typeof rawValue.content === 'string') {
                const text = stripHtmlToText(rawValue.content);

                return text
                    ? [
                          {
                              type: 'paragraph',
                              content: [{ type: 'text', text }],
                          },
                      ]
                    : [];
            }

            return [];
        }

        return [];
    }

    return [];
}

function valueForSite(siteHandle) {
   
    if (!siteHandle) return [];

    const siteValue = draftValue.value.find((entry) => entry?.handle === siteHandle);
  
    return normalizeBardCpValue(siteValue?.value);
}

function commitValue(nextValue) {
    if (props.config?.debounce) {
        updateDebounced(nextValue);
        return;
    }

    update(nextValue);
}

function onBardUpdate(siteHandle, bardValue) {
    const base = draftValue.value;
    const hasEntry = base.some((entry) => entry?.handle === siteHandle);

    const nextValue = hasEntry
        ? base.map((entry) =>
              entry?.handle === siteHandle
                  ? { ...entry, value: bardValue }
                  : entry,
          )
        : [
              ...base,
              {
                  handle: siteHandle,
                  value: bardValue,
              },
          ];

    draftValue.value = nextValue;
    commitValue(nextValue);
}
</script>
