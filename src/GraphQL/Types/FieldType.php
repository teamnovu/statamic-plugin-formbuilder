<?php

namespace Teamnovu\Formbuilder\GraphQL\Types;

use Illuminate\Support\Arr;
use Statamic\Facades\Data;

/**
 * Extends Statamic's GraphQL Field type to resolve statamic:// links in field configs.
 *
 * Statamic stores internal links (e.g. to entries) as "statamic://entry::uuid" in field configs
 * (e.g. in translatable_bard values inside input_checkboxes options). These are never resolved
 * by Statamic's default GraphQL pipeline because field configs are returned as raw scalar arrays.
 *
 * This type overrides the "config" resolver to recursively replace any statamic:// reference
 * with the actual URL before the config is returned. All other field types are unaffected
 * since the replacement only triggers on strings containing statamic://.
 *
 * Registered via the addon's TypeRegistrar, which extends Statamic's registrar so our
 * type is added after Statamic's own type registration (which would otherwise overwrite ours).
 * Only applies to fields in form blueprints (FormType / SectionType use this Field type).
 */
class FieldType extends \Statamic\GraphQL\Types\FieldType
{
    public function fields(): array
    {
        $fields = parent::fields();

        $fields['config']['resolve'] = function ($field) {
            $keys = $field->fieldtype()->configFields()->all()->keys()->all();
            $config = Arr::only($field->config(), $keys);

            return $this->resolveStatamicLinks($config);
        };

        return $fields;
    }

    /**
     * Recursively walks a config value and replaces any "statamic://type::id" string
     * with the resolved URL. Non-matching strings and non-string values pass through unchanged.
     */
    private function resolveStatamicLinks(mixed $value): mixed
    {
        if (is_string($value)) {
            return preg_replace_callback(
                '/statamic:\/\/([^"\'<>\s]+)/',
                fn ($m) => Data::find($m[1])?->url() ?? $m[0],
                $value
            );
        }

        if (is_array($value)) {
            return array_map(fn ($v) => $this->resolveStatamicLinks($v), $value);
        }

        return $value;
    }
}
