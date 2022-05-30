<div class="editorjs mt-5 border-bottom" id="editorjs-<?php echo $module_input_slug_lang; ?>" data-input-slug="<?php echo $module_input_slug_lang; ?>" placeholder="<?php echo ($module_input_placeholder ? $module_input_placeholder : ucfirst($types[$type]['name']) . ' ' . $module_input_slug_lang); ?>"><?php echo ($post[$module_input_slug_lang] ? $post[$module_input_slug_lang] : $module_input_default_value); ?></div>
<input type="hidden" name="<?php echo $module_input_slug_lang; ?>">

<?php echo ($module_input_placeholder ? '<div class="col-12 row text-muted small m-0"><span class="ml-auto mr-0">' . $module_input_placeholder . '</span></div>' : ''); ?>
