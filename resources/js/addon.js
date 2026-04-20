


import CustomFormFieldText from './components/fieldtypes/CustomFormFieldText.vue';
import CustomFormFieldCheckboxes from './components/fieldtypes/CustomFormFieldCheckboxes.vue';
import CustomFormFieldRadioButtons from './components/fieldtypes/CustomFormFieldRadioButtons.vue';
import CustomFormFieldFileUpload from './components/fieldtypes/CustomFormFieldFileUpload.vue';
import DisplayTextField from './components/fieldtypes/DisplayTextField.vue';

import TranslatableInput from './components/fieldtypes/TranslatableInput.vue';
import TranslatableBard from './components/fieldtypes/TranslatableBard.vue';


//form fieldtypes
Statamic.booting(() => {
    Statamic.$components.register('translatable_input-fieldtype', TranslatableInput);
    Statamic.$components.register('translatable_bard-fieldtype', TranslatableBard);

    Statamic.$components.register('input_email-fieldtype', CustomFormFieldText);
    Statamic.$components.register('input_telephone-fieldtype', CustomFormFieldText);
    Statamic.$components.register('input_phone-fieldtype', CustomFormFieldText);
    Statamic.$components.register('input_number-fieldtype', CustomFormFieldText);
    Statamic.$components.register('input_text-fieldtype', CustomFormFieldText);
    Statamic.$components.register('input_daterange-fieldtype', CustomFormFieldText);
    Statamic.$components.register('input_date-fieldtype', CustomFormFieldText);

    Statamic.$components.register('input_slider-fieldtype', CustomFormFieldText);
    Statamic.$components.register('input_textarea-fieldtype', CustomFormFieldText);
    Statamic.$components.register('input_switch-fieldtype', CustomFormFieldText);
    Statamic.$components.register('input_select-fieldtype', CustomFormFieldCheckboxes);
    Statamic.$components.register('input_checkboxes-fieldtype', CustomFormFieldCheckboxes);
    Statamic.$components.register('input_radio_buttons-fieldtype', CustomFormFieldRadioButtons);
    Statamic.$components.register('input_file_upload-fieldtype', CustomFormFieldFileUpload);
    Statamic.$components.register('display_text-fieldtype', DisplayTextField);

});
