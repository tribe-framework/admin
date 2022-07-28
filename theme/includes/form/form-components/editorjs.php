<?php
/**
 * @var string $module_input_placeholder
 * @var string $module_input_slug_lang
 * @var string $module_input_default_value
 * @var string $type
 * @var array $post
 * @var array $types
 */
?>
<div>
    <div class="editorjs py-4 pl-5 mt-5 border border-light shadow-sm">
        <input type="hidden" name="<?= $module_input_slug_lang ?>">
        <div id="<?= "editor_{$module_input_slug_lang}" ?>"
             class="editorjs-tool"
             data-input-slug="<?= $module_input_slug_lang ?>"
             placeholder="<?= $module_input_placeholder ?: ucfirst($types[$type]['name']) . ' ' . $module_input_slug_lang ?>">
        </div>
    </div>

    <?php if ($module_input_placeholder) : ?>
        <div class='col-12 text-right text-muted small m-0'>
            <span class='mr-0'><?= $module_input_placeholder ?></span>
        </div>
    <?php endif ?>
</div>
