<template>
    <div class="display-text-field-cp">
        <h3 v-if="resolvedTitle" class="text-base font-semibold mb-1">{{ resolvedTitle }}</h3>
        <div v-if="resolvedText" class="text-sm text-gray-600" v-html="resolvedText"></div>
    </div>
</template>

<script>
// @tiptap/core is not installed — Statamic bundles it internally but doesn't expose it.
// We only need bold, italic, and link, so a minimal converter avoids the dependency.
function tiptapToHtml(nodes) {
    return (nodes ?? []).map(node => {
        const inner = (node.content ?? []).map(leaf => {
            let text = leaf.text ?? ''
            ;(leaf.marks ?? []).forEach(({ type, attrs }) => {
                if (type === 'bold') text = `<strong>${text}</strong>`
                else if (type === 'italic') text = `<em>${text}</em>`
                else if (type === 'link') text = `<a href="${attrs?.href}">${text}</a>`
            })
            return text
        }).join('')
        return `<p>${inner}</p>`
    }).join('')
}

export default {
    mixins: [Fieldtype],

    data() {
        return {
            locale: this.meta?.locale,
        }
    },

    computed: {
        resolvedTitle() {
            return this.resolve(this.config.title)
        },

        resolvedText() {
            const nodes = this.resolve(this.config.text)
            if (!Array.isArray(nodes)) return null
            return tiptapToHtml(nodes)
        },
    },

    methods: {
        resolve(translatableArray) {
            if (!translatableArray) return null
            return (
                translatableArray.find(e => e.handle === this.locale)?.value
                ?? translatableArray.find(e => e.handle === 'en')?.value
                ?? translatableArray.find(e => e.handle === 'de')?.value
                ?? null
            )
        },
    },
}
</script>

<style>
.form-group:has(.display-text-field-cp) .field-inner {
    display: none;
}
</style>