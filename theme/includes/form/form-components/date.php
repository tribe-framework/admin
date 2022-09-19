<?php
/**
 * @var array $types
 * @var array $post
 * @var string $type
 * @var string $module_input_slug_lang
 * @var string $module_input_placeholder
 * @var string $module_input_default_value
 */

$_placeholder = $module_input_placeholder ?: ucfirst($types[$type]['name']).' '.$module_input_slug_lang;
$_value = $post[$module_input_slug_lang] ?? $module_input_default_value;
?>
<div class="input-group mt-5">
    <div class="input-group-prepend">
        <span class="input-group-text border-top-0 border-left-0 border-right-0 rounded-0" id="basic-addon1">
            <i class="fas fa-calendar"></i>
        </span>
    </div>

    <input
            type="date"
            name="<?= $module_input_slug_lang ?>"
            class="form-control border-top-0 border-left-0 border-right-0 rounded-0 m-0"
            placeholder="<?= $_placeholder ?>"
            value="<?= $_value ?>">

    <?php
        if (isset($module_input_placeholder)) {
            echo "<div class='col-12 row text-muted small m-0'><span class='ml-auto mr-0'>$module_input_placeholder</span></div>";
        }
    ?>
</div>
