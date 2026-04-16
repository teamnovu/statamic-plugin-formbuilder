<?php

namespace Teamnovu\Formbuilder\Fieldtypes;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Statamic\Contracts\Assets\AssetContainer as AssetContainerContract;
use Statamic\Facades\AssetContainer as AssetContainerFacade;
use Statamic\Fields\Fieldtype;

class InputFileUpload extends Fieldtype
{
    protected $selectableInForms = true;

    protected $selectable = false;

    protected $categories = ['media'];

    protected $icon = 'upload-arrow-up';

    public static function title()
    {
        return __('formbuilder::form.title.file_upload');
    }

    protected function configFieldItems(): array
    {
        return [
            [
                'display' => __('Input Behavior'),
                'fields' => [
                    'label' => [
                        'display' => __('form.label.display'),
                        'instructions' => __('form.label.instruction'),
                        'type' => 'translatable_input',
                    ],
                    'hint' => [
                        'display' => __('form.hint.display'),
                        'instructions' => __('form.hint.instruction'),
                        'type' => 'translatable_input',
                    ],
                    'help' => [
                        'display' => __('form.help.display'),
                        'instructions' => __('form.help.instruction'),
                        'type' => 'translatable_input',
                    ],
                    'icon_label' => [
                        'display' => __('form.icon_label.display'),
                        'instructions' => __('form.icon_label.instruction'),
                        'type' => 'translatable_input',
                    ],
                    'max_files' => [
                        'display' => __('form.max_files.display'),
                        'instructions' => __('form.max_files.instruction'),
                        'type' => 'integer',
                        'default' => 1,
                        'force_in_config' => true,
                    ],
                    'max_filesize' => [
                        'display' => __('form.max_filesize.display'),
                        'instructions' => __('form.max_filesize.instruction'),
                        'type' => 'integer',
                        'default' => 10240, // 10 MB
                        'force_in_config' => true,
                    ],
                    'allowed_mimes' => [
                        'display' => __('form.allowed_mimes.display'),
                        'instructions' => __('form.allowed_mimes.instruction'),
                        'type' => 'array',
                        'placeholder' => 'application/pdf',
                        'default' => [
                            '.jpg',
                            '.png',
                            '.gif',
                            '.webp',
                            '.svg',
                            '.pdf',
                            '.doc',
                            '.docx',
                            '.xls',
                            '.xlsx',
                            '.zip',
                            '.mp4',
                        ],
                        'force_in_config' => true,
                    ],
                    'container' => [
                        'display' => __('form.upload_container.display'),
                        'instructions' => __('form.upload_container.instruction'),
                        'type' => 'text',
                        'default' => 'assets',
                    ],
                    'folder' => [
                        'display' => __('form.upload_folder.display'),
                        'instructions' => __('form.upload_folder.instruction'),
                        'type' => 'text',
                        'default' => 'form-uploads',
                    ],
                ],
            ],
        ];
    }

    public function defaultValue(): mixed
    {
        return $this->isMultiple() ? [] : null;
    }

    // Multi-file mode is determined solely by max_files > 1, which also controls
    // whether the frontend submits a single value or an array.
    private function isMultiple(): bool
    {
        return (int) $this->config('max_files', 1) > 1;
    }

    public function preProcess(mixed $data): mixed
    {
        return $data;
    }

    public function preProcessValidatable(mixed $value): mixed
    {
        // Multipart form submissions serialize JS null/undefined as the strings "null" / "undefined".
        // Normalise them to PHP null so that the nullable rule and Rule::when() work correctly.
        if (in_array($value, [null, '', 'null', 'undefined'], true)) {
            return null;
        }

        return $value;
    }

    public function process(mixed $data): mixed
    {
        if ($data === null || $data === '') {
            return $this->defaultValue();
        }

        $isMultiple = $this->isMultiple();

        $wrapped = Arr::wrap($data);
        $files = collect($wrapped)->filter(static fn ($file): bool => $file instanceof UploadedFile);

        if ($files->isEmpty()) {
            return $isMultiple ? $wrapped : Arr::first($wrapped);
        }

        $container = AssetContainerFacade::find($this->config('container', 'assets'));

        if (! $container) {
            // Silently skip upload if the configured container doesn't exist.
            // This avoids crashing form submissions due to a misconfigured blueprint.
            return $this->defaultValue();
        }

        $folder = $this->normalizedFolder();
        $limitedFiles = $this->limitFiles($files);

        // Validate all files before uploading any to avoid partial uploads on failure.
        $limitedFiles->each(fn (UploadedFile $file) => $this->ensureFileAllowed($file));

        $storedPaths = $limitedFiles
            ->map(fn (UploadedFile $file): string => $this->uploadFile($container, $folder, $file))
            ->values()
            ->all();

        return $isMultiple ? $storedPaths : ($storedPaths[0] ?? null);
    }

    public function preProcessIndex(mixed $value): mixed
    {
        return $value;
    }

    public function preload(): array
    {
        $container = AssetContainerFacade::find($this->config('container', 'assets'));
        $assetBaseUrl = $container ? rtrim($container->disk()->url(''), '/') : null;

        return [
            'locale' => auth()->user()->preferredLocale(),
            'cp_route' => config('statamic.cp.route', 'cp'),
            'asset_base_url' => $assetBaseUrl,
        ];
    }

    public function rules(): array
    {
        $maxKilobytes = (int) $this->config('max_filesize', 0);
        $maxFiles = (int) $this->config('max_files', 0);

        if ($this->isMultiple()) {
            $rules = ['sometimes', 'nullable', 'array'];

            if ($maxFiles > 0) {
                $rules[] = 'max:'.$maxFiles;
            }

            return $rules;
        }

        $rules = ['sometimes', 'nullable', 'file'];

        if ($maxKilobytes > 0) {
            $rules[] = 'max:'.$maxKilobytes;
        }

        // allowed_mimes supports both extensions (.jpg) and MIME types (image/jpeg).
        // Each is applied as a separate Laravel rule (AND semantics), which works correctly
        // for typical single-type configs. Mixed configs should use consistent types.
        $extensions = $this->allowedExtensions();
        $mimeTypes = $this->allowedMimeTypes();

        if ($extensions !== []) {
            $rules[] = 'mimes:'.implode(',', $extensions);
        }

        if ($mimeTypes !== []) {
            $rules[] = 'mimetypes:'.implode(',', $mimeTypes);
        }

        return $rules;
    }

    private function ensureFileAllowed(UploadedFile $file): void
    {
        $maxKilobytes = (int) $this->config('max_filesize', 0);
        $extensions = $this->allowedExtensions();
        $mimeTypes = $this->allowedMimeTypes();

        if ($maxKilobytes > 0 && $file->getSize() > $maxKilobytes * 1024) {
            throw ValidationException::withMessages([
                $this->field()->handle() => __('validation.max.file', ['max' => $maxKilobytes]),
            ]);
        }

        if ($extensions === [] && $mimeTypes === []) {
            return;
        }

        // OR semantics: the file is allowed if it matches any configured extension or MIME type.
        $extensionAllowed = $extensions !== [] && in_array(strtolower($file->getClientOriginalExtension()), $extensions, true);
        $mimeAllowed = $mimeTypes !== [] && in_array($file->getMimeType(), $mimeTypes, true);

        if (! $extensionAllowed && ! $mimeAllowed) {
            $all = array_merge(array_map(fn ($e) => '.'.$e, $extensions), $mimeTypes);
            throw ValidationException::withMessages([
                $this->field()->handle() => __('validation.mimetypes', ['values' => implode(', ', $all)]),
            ]);
        }
    }

    private function uploadFile(AssetContainerContract $container, string $folder, UploadedFile $file): string
    {
        $path = $this->buildPath($container, $folder, $file);

        $container
            ->makeAsset($path)
            ->upload($file);

        return $path;
    }

    private function buildPath(AssetContainerContract $container, string $folder, UploadedFile $file): string
    {
        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin';
        $slug = Str::slug($baseName) ?: 'upload';
        $makePath = fn (string $filename): string => $folder === '' ? $filename : "{$folder}/{$filename}";

        $path = $makePath("{$slug}.{$extension}");
        $iteration = 1;

        while ($container->asset($path)) {
            $path = $makePath("{$slug}-{$iteration}.{$extension}");
            $iteration++;
        }

        return $path;
    }

    private function normalizedFolder(): string
    {
        return trim((string) $this->config('folder', 'form-uploads'), '/');
    }

    private function limitFiles(Collection $files): Collection
    {
        $maxFiles = (int) $this->config('max_files', 0);

        if ($maxFiles > 0) {
            return $files->take($maxFiles);
        }

        return $files;
    }

    private function allowedExtensions(): array
    {
        return collect($this->normalizedAllowedMimes())
            ->reject(fn ($mime) => str_contains($mime, '/'))
            ->map(fn ($mime) => ltrim($mime, '.'))
            ->values()
            ->all();
    }

    private function allowedMimeTypes(): array
    {
        return collect($this->normalizedAllowedMimes())
            ->filter(fn ($mime) => str_contains($mime, '/'))
            ->values()
            ->all();
    }

    // Normalizes allowed_mimes to a flat string array, accepting both array and
    // comma-separated string formats as the config value may come in either form.
    private function normalizedAllowedMimes(): array
    {
        $mimes = $this->config('allowed_mimes');

        if (is_array($mimes)) {
            return collect($mimes)
                ->values()
                ->filter()
                ->map(fn ($mime) => trim((string) $mime))
                ->filter()
                ->all();
        }

        if (is_string($mimes)) {
            return collect(explode(',', $mimes))
                ->map(fn ($mime) => trim($mime))
                ->filter()
                ->values()
                ->all();
        }

        return [];
    }
}