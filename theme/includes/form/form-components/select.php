<div class="form-group mt-5">
    <select class="form-control pl-0 border-top-0 border-left-0 border-right-0 rounded-0 mt-1" id="select_<?= $module_input_slug_lang ?>" name="<?= $module_input_slug_lang ?>">
        <option <?= ($post[$module_input_slug_lang] ?? false)?'':'selected="selected"'; ?> value="">
            <?=$module_input_placeholder?$module_input_placeholder:'Select '.$module_input_slug_lang ?>
        </option>
        <?php
        if ($options=$module_input_options) {
            foreach ($options as $opt) {
                if (is_array($opt)) {
                    $_slug = $opt['slug'] ?? '';
                    $_title = $opt['title'] ?? '';
                    $_selected = '';

                    if (isset($post) && isset($opt)) {
                        $_selected = (($post[$module_input_slug_lang] ?? null) == $opt['slug']) ? 'selected="selected"' : '';
                    }

                    echo "<option value='$_slug' $_selected>$_title</option>";
                } else {
                    echo '<option value="'.$opt.'" '.(($post[$module_input_slug_lang]==$opt)?'selected="selected"':'').'>'.$opt.'</option>';
                }
            }
        } else {
            $options=$dash->get_all_ids($module_input_slug_lang, $types[$module_input_slug_lang]['primary_module'], 'ASC');
            foreach ($options as $opt) {
                $option=$dash->get_content($opt['id']);
                $titler=$dash->get_type_title_data($option);
                $title_slug=$titler['slug'];

                $_value = $option['slug'] ?? '';
                $_placeholder = $option[$title_slug] ?? '';

                $_is_selected = '';
                if ($post[$module_input_slug_lang] == $option['slug']) {
                    $_is_selected = 'selected';
                }

                echo "<option value='{$_value}' {$_is_selected}>{$_placeholder}</option>";
            }
        }
        ?>
    </select>
    <?php echo($module_input_placeholder?'<div class="col-12 row text-muted small m-0"><span class="ml-auto mr-0">'.$module_input_placeholder.'</span></div>':''); ?>
</div>
