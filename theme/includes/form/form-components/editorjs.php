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
<div class="editorjs mt-5 border-bottom border"
     id="editorjs"
     data-input-slug="<?php echo $module_input_slug_lang; ?>"
     placeholder="<?php echo $module_input_placeholder ?: ucfirst($types[$type]['name']) . ' ' . $module_input_slug_lang ?>"
><?php echo $post[$module_input_slug_lang] ?: $module_input_default_value ?>
</div>
<input type="hidden" name="<?php echo $module_input_slug_lang ?>">

<?php if ($module_input_placeholder) : ?>
    <div class='col-12 row text-muted small m-0'>
        <span class='ml-auto mr-0'><?php echo $module_input_placeholder ?></span>
    </div>
<?php endif ?>
