<?php

namespace Teamnovu\Formbuilder\Http\Controllers\CP;

use Statamic\Http\Controllers\CP\Forms\FormsController as StatamicFormsController;

class FormsController extends StatamicFormsController
{
    protected function editFormBlueprint($form)
    {
        $blueprint = parent::editFormBlueprint($form);
        $contents = $blueprint->contents();

        foreach ($contents['tabs']['main']['sections'] as &$section) {
            foreach ($section['fields'] as &$field) {
                if ($field['handle'] !== 'email') {
                    continue;
                }

                $field['field']['fields'] = $this->customizeEmailFields(
                    $field['field']['fields'],
                    $form->handle()
                );
            }
        }

        return $blueprint->setContents($contents);
    }

    private function customizeEmailFields(array $fields, string $formHandle): array
    {
        $customized = [];

        foreach ($fields as $field) {
            if ($field['handle'] === 'subject') {
                $field['field']['type'] = 'translatable_input';
            }

            if ($field['handle'] === 'html') {
                $customized[] = [
                    'handle' => 'mail_text',
                    'field' => [
                        'type' => 'translatable_bard',
                        'display' => __('formbuilder::form.email_config.mail_text_display'),
                        'instructions' => __('formbuilder::form.email_config.mail_text_instruction'),
                    ],
                ];
            }

            $customized[] = $field;
        }

        $customized[] = [
            'handle' => 'mail_preview',
            'field' => [
                'type' => 'html',
                'display' => __('formbuilder::form.email_config.mail_preview_display'),
                'instructions' => __('formbuilder::form.email_config.mail_preview_instruction'),
                'sanitize' => false,
                'html' => $this->mailPreviewModalHtml($formHandle),
            ],
        ];

        return $customized;
    }

    private function mailPreviewModalHtml(string $formHandle): string
    {
        $baseUrl = cp_route('forms.email-preview', $formHandle);

        $openOnclick =
            'var d=this.nextElementSibling;'.
            "var stack=this.closest('.grid-stacked');".
            "var row=this.closest('.grid-stacked > *');".
            'var idx=(stack&&row)?Array.from(stack.children).indexOf(row):0;'.
            "var locale=(document.documentElement.lang||'de').split('-')[0];".
            "d.querySelector('iframe').src='{$baseUrl}?email_index='+idx+'&locale='+locale;".
            'd.showModal();';

        $closeOnclick = "this.closest('dialog').close()";
        $backdropClose = 'if(event.target===this)this.close()';

        return
            "<button type=\"button\" onclick=\"{$openOnclick}\" class=\"relative inline-flex items-center justify-center whitespace-nowrap shrink-0 font-medium antialiased cursor-pointer no-underline bg-linear-to-b from-white to-gray-50 hover:to-gray-100 hover:bg-gray-50 text-gray-900 border border-gray-300 shadow-ui-sm dark:from-gray-850 dark:to-gray-900 dark:hover:to-gray-850 dark:hover:bg-gray-900 dark:border-gray-700/80 dark:text-gray-300 dark:shadow-ui-md px-3 h-8 text-[0.8125rem] leading-tight gap-2 rounded-lg\">".
                __('formbuilder::form.email_config.mail_preview_button').
            '</button>'.
            "<dialog onclick=\"{$backdropClose}\" style=\"width:min(90vw,860px);height:85vh;padding:0;border:0;border-radius:8px;box-shadow:0 25px 50px rgba(0,0,0,.25);overflow:hidden\">".
                '<div style="display:flex;align-items:center;justify-content:space-between;padding:10px 16px;border-bottom:1px solid #e5e7eb;background:#f9fafb">'.
                    '<span style="font-size:13px;font-weight:600;color:#374151">'.__('formbuilder::form.email_config.mail_preview_modal_title').'</span>'.
                    "<button type=\"button\" onclick=\"{$closeOnclick}\" style=\"font-size:20px;line-height:1;color:#9ca3af;background:none;border:none;cursor:pointer;padding:2px 6px\">&#x2715;</button>".
                '</div>'.
                '<iframe style="width:100%;height:calc(100% - 45px);border:0"></iframe>'.
            '</dialog>';
    }
}
