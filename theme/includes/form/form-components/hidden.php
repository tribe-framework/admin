<input
    type="hidden"
    name="<?= $module_input_slug_lang ?>"
    value="<?= $post && $post[$module_input_slug_lang] ?
        $post[$module_input_slug_lang] :
        $module_input_default_value
    ?>"
>
