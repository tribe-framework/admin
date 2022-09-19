<?php
/**
 * @var string $module_input_slug_lang
 * @var string $module_input_placeholder
 * @var array $module_input_options
 * @var array $types
 * @var object $dash
 */

$is_selected = '';
if ($post[$module_input_slug_lang] ?? false) {
    $is_selected = "selected='selected'";
}
?>
<div class="form-group mt-5">
    <select
            id="select_<?= $module_input_slug_lang ?>"
            name="<?= $module_input_slug_lang ?>"
            class="form-control pl-0 border-top-0 border-left-0 border-right-0 rounded-0 mt-1" >
        <option <?= $is_selected ?> value="">
            <?= $module_input_placeholder ? $module_input_placeholder : "Select $module_input_slug_lang" ?>
        </option>

        <?php
        if ($options = $module_input_options) {
            foreach ($options as $opt) {
                if (is_array($opt)) {
                    $_slug = $opt['slug'] ?? '';
                    $_title = $opt['title'] ?? '';
                    $is_selected = '';

                    if (
                            isset($post) &&
                            ($post[$module_input_slug_lang] ?? null) == $opt['slug']
                    ) {
                        $is_selected = "selected='selected'";
                    }

                    echo "<option value='$_slug' $is_selected>$_title</option>";
                }
                else {
                    $is_selected = '';

                    if ($post[$module_input_slug_lang] == $opt) {
                        $is_selected = "selected='selected'";
                    }

                    echo "<option value='$opt' $is_selected>$opt</option>";
                }
            }
        } else {
            $options = $dash->get_all_ids($module_input_slug_lang, $types[$module_input_slug_lang]['primary_module'], 'ASC');

            foreach ($options as $opt) {
                $option = $dash->getObject($opt['id']);
                $titler = $dash->get_type_title_data($option['type']);
                $title_slug = $titler['slug'];

                $is_selected = '';
                if (($post[$module_input_slug_lang] ?? null) == $option['slug']) {
                    $is_selected = "selected = 'selected'";
                }

                echo "<option value='{$option['slug']}' $is_selected>{$option[$title_slug]}</option>";
            }
        }
        ?>
    </select>

    <?php
    if ($module_input_placeholder) {
        echo "<div class='col-12 row text-muted small m-0'><span class='ml-auto mr-0'>$module_input_placeholder</span></div>";
    }
    ?>
</div>
