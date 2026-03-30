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
                    'multiple' => [
                        'display' => __('form.multiple.display'),
                        'instructions' => __('form.multiple.instruction'),
                        'type' => 'toggle',
                        'default' => false,
                    ],
                    'max_files' => [
                        'display' => __('form.max_files.display'),
                        'instructions' => __('form.max_files.instruction'),
                        'type' => 'integer',
                        'default' => 1,
                    ],
                    'max_filesize' => [
                        'display' => __('form.max_filesize.display'),
                        'instructions' => __('form.max_filesize.instruction'),
                        'type' => 'integer',
                    ],
                    'allowed_mimes' => [
                        'display' => __('form.allowed_mimes.display'),
                        'instructions' => __('form.allowed_mimes.instruction'),
                        'type' => 'array',
                        'placeholder' => 'application/pdf',
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
        return $this->config('multiple') ? [] : null;
    }

    public function preProcess(mixed $data): mixed
    {
        return $data;
    }

    public function process(mixed $data): mixed
    {
        if ($data === null || $data === '') {
            return $this->defaultValue();
        }

        $files = collect(Arr::wrap($data))
            ->filter(static fn ($file): bool => $file instanceof UploadedFile);

        if ($files->isEmpty()) {
            return $this->config('multiple') ? Arr::wrap($data) : Arr::first(Arr::wrap($data));
        }

        $container = AssetContainerFacade::find($this->config('container', 'assets'));

        if (! $container) {
            return $this->defaultValue();
        }

        $folder = $this->normalizedFolder();
        $limitedFiles = $this->limitFiles($files);

        $storedPaths = $limitedFiles
            ->map(function (UploadedFile $file) use ($container, $folder): string {
                $this->ensureFileAllowed($file);

                return $this->uploadFile($container, $folder, $file);
            })
            ->values()
            ->all();

        return $this->config('multiple') ? $storedPaths : ($storedPaths[0] ?? null);
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
        $allowedMimes = $this->allowedMimeList();
        $maxFiles = (int) $this->config('max_files', 0);

        if ($this->config('multiple')) {
            // Accept single or multiple uploads; enforce limits in processing.
            return [];
        }

        $rules = ['file'];

        if ($maxKilobytes > 0) {
            $rules[] = 'max:'.$maxKilobytes;
        }

        if ($allowedMimes !== '') {
            $rules[] = 'mimetypes:'.$allowedMimes;
        }

        return $rules;
    }

    private function ensureFileAllowed(UploadedFile $file): void
    {
        $maxKilobytes = (int) $this->config('max_filesize', 0);
        $allowedMimes = $this->normalizedAllowedMimes();

        if ($maxKilobytes > 0 && $file->getSize() > $maxKilobytes * 1024) {
            throw ValidationException::withMessages([
                $this->field()->handle() => __('validation.max.file', ['max' => $maxKilobytes]),
            ]);
        }

        if ($allowedMimes !== [] && ! in_array($file->getMimeType(), $allowedMimes, true)) {
            throw ValidationException::withMessages([
                $this->field()->handle() => __('validation.mimetypes', ['values' => implode(', ', $allowedMimes)]),
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
        $iteration = 1;

        $filename = "{$slug}.{$extension}";
        $path = $folder === '' ? $filename : "{$folder}/{$filename}";

        while ($container->asset($path)) {
            $filename = "{$slug}-{$iteration}.{$extension}";
            $path = $folder === '' ? $filename : "{$folder}/{$filename}";
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

    private function allowedMimeList(): string
    {
        return collect($this->normalizedAllowedMimes())->implode(',');
    }

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
