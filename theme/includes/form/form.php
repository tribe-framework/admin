<?php
$post = $post ?? null; // set $post to NULL if it doesn't exist
$types = $dash->getTypes();

function formComponent($v) {
    return "form-components/{$v}.php";
}

$components = [
    'text' => 'text',
    'multi-text' => 'text',
    'multi_text' => 'text',
    'textarea' => 'textarea',
    'multi-textarea' => 'textarea',
    'multi_textarea' => 'textarea',
    'typeout' => 'typeout',
    'date' => 'date',
    'url' => 'url',
    'multi-url' => 'url',
    'multi_url' => 'url',
    'number' => 'number',
    'multi-number' => 'number',
    'multi_number' => 'number',
    'checkbox' => 'checkbox',
    'tel' => 'tel',
    'hidden' => 'hidden',
    'priority' => 'priority',
    'email' => 'email',
    'password' => 'password',
    'select' => 'select',
    'multi-drop' => 'multi-drop',
    'multi_drop' => 'multi-drop',
    'multi-select' => 'multi-select',
    'multi_select' => 'multi-select',
    'file_uploader' => 'file-uploader',
    'google_map_marker' => 'google-map-marker',
    'color' => 'color',
    'multi-color' => 'color',
    'multi_color' => 'color',
];

foreach ($types[$type]['modules'] as $module) {
    if (
        isset($module['restrict_to_roles']) &&
        !in_array($role['slug'], $module['restrict_to_roles'])
    ) {
        continue;
    }

    $module_input_slug = $module['input_slug'] ?? null;
    $module_input_type = $module['input_type'] ?? null;
    $module_input_lang = $module['input_lang'] ?? null;
    $module_input_primary = $module['input_primary'] ?? null;
    $module_input_options = $module['input_options'] ?? null;
    $module_input_placeholder = $module['input_placeholder'] ?? null;
    $module_input_step = $module['input_step'] ?? null;
    $module_input_min = $module['input_min'] ?? null;
    $module_input_max = $module['input_max'] ?? null;
    $slug_displayed = 0;

    $module_input_slug_arr = array();

    if (is_array($module_input_lang)) {
        $module_input_slug_arr = $module_input_lang;
    } else {
        $module_input_slug_arr[0]['slug'] = '';
    }

    foreach ($module_input_slug_arr as $input_lang) {
        $module_append = isset($input_lang['slug']) && $input_lang['slug'] ? "_{$input_lang['slug']}" : '';
        $module_input_slug_lang = $module_input_slug . $module_append;

        $module_input_default_value = '';
        $module_autofill = $module['autofill'] ?? null;

        if ($module_autofill == 'user_id') {
            $module_input_default_value = $dash->get_unique_user_id();
        }

        // replace _ with -
        $module_input_type_alt = \str_replace('_', '-', $module_input_type);

        if (array_key_exists($module_input_type, $components)) {
            include formComponent($components[$module_input_type]);
        } else if ($module_input_type_alt && \array_key_exists($module_input_type_alt, $components)) {
            $module_input_type = $module_input_type_alt;
            include formComponent($components[$module_input_type]);
        } else {
            echo "<em style='color: red; border-left: 2px solid red; padding: 2px 8px;'>{$module_input_type} : form-component not found</em><br/>";
        }
    }
}
