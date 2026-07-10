<?php

namespace Teamnovu\Formbuilder\Http\Controllers\CP;

use Illuminate\Http\Request;
use Illuminate\Mail\Markdown;
use Statamic\Facades\Site;
use Statamic\Http\Controllers\Controller;
use Teamnovu\Formbuilder\Support\LocalizedValueResolver;

// Renders a saved email config as a full styled HTML preview, used inside the CP modal iframe.
class FormEmailPreviewController extends Controller
{
    public function show(Request $request, $form)
    {
        abort_unless(
            auth()->user()?->isSuper() || auth()->user()?->hasPermission('configure forms'),
            403
        );

        // email_index and locale are set by the preview button onclick in FormsController.
        $emailConfigs = collect($form->email() ?? []);
        $index = (int) $request->query('email_index', 0);
        $locale = $request->query('locale', Site::default()->handle());

        $config = $emailConfigs->get($index);

        if (! $config) {
            return response('<p style="font-family:sans-serif;color:#6b7280;padding:2rem">No email configuration found at index '.$index.'.</p>');
        }

        $template = $config['html'] ?? null;

        if (! $template) {
            return response('<p style="font-family:sans-serif;color:#6b7280;padding:2rem">No HTML template configured for this email.</p>');
        }

        $configForRender = app(LocalizedValueResolver::class)
            ->resolveConfiguration($config, $locale);

        $data = [
            'email_config' => $configForRender,
            'fields' => collect($this->buildDummyFields($form)),
            'form' => $this->makeFormProxy($form),
            'site' => $locale,
            'locale' => $locale,
        ];

        try {
            // Use Markdown::render() so the full mail pipeline runs:
            // namespace registration, blade rendering, and CSS inlining.
            $html = app(Markdown::class)->render($template, $data)->toHtml();
        } catch (\Throwable $e) {
            $html = '<p style="font-family:monospace;color:red;padding:1rem">Template error: '.htmlspecialchars($e->getMessage()).'</p>';
        }

        return response($html);
    }

    // Builds placeholder field data matching the shape Email::addData() produces,
    // so templates that loop $fields (e.g. submission.blade.php) render without errors.
    private function buildDummyFields($form): array
    {
        return $form->blueprint()->fields()->all()
            ->map(function ($field) {
                $display = $field->display();
                $handle = $field->handle();
                $type = $field->type();
                $config = $field->config();

                $value = new class($display)
                {
                    public function __construct(private string $display) {}

                    public function value(): string
                    {
                        return "[{$this->display}]";
                    }

                    public function __toString(): string
                    {
                        return "[{$this->display}]";
                    }
                };

                return compact('display', 'handle', 'config', 'value') + ['fieldtype' => $type];
            })
            ->values()
            ->all();
    }

    // Wraps the form so $form->value()?->title() in submission.blade.php resolves correctly.
    private function makeFormProxy($form): object
    {
        return new class($form)
        {
            public function __construct(private $form) {}

            public function value(): object
            {
                return $this->form;
            }

            public function title(): string
            {
                return $this->form->title();
            }

            public function __toString(): string
            {
                return $this->form->title();
            }
        };
    }
}
