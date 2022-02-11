<div class="textarea-group" id="textarea-group-<?= $module_input_slug_lang ?>">
    <?php
    $type_name_values = array();

    if (is_array($post[$module_input_slug_lang])) {
        $type_name_values = $post[$module_input_slug_lang];
    } else if ($post[$module_input_slug_lang]) {
        $type_name_values[0] = $post[$module_input_slug_lang];
    } else {
        $type_name_values[0] = $module_input_default_value;
    }

    foreach ($type_name_values as $i => $type_name_value):
        if ($i < 1 || trim($type_name_value)):
    ?>
        <?php
            $textarea_name = $module_input_slug_lang . (($module_input_type == 'multi-textarea' || $module_input_type == 'multi_textarea') ? '[]' : '');
            $placeholder = $module_input_placeholder ?: ucfirst($types[$type]['name']) . " $module_input_slug_lang";
        ?>
        <div class="input-group mt-5">
            <div class="input-group-prepend">
                <span class="input-group-text border-top-0 border-left-0 border-right-0 rounded-0" id="basic-addon1_<?= "{$textarea_name}-{$i}" ?>">
                    <i class="fas fa-align-justify"></i>
                </span>
            </div>

            <textarea
                id="<?= $module_input_slug_lang ?>"
                name="<?= $textarea_name ?>"
                class="border-top-0 border-left-0 border-right-0 rounded-0 form-control"
                placeholder="<?= $placeholder ?>"
            ><?= $type_name_value ?></textarea>

            <?php if ($module_input_placeholder): ?>
                <div class="col-12 row text-muted small m-0">
                    <span class="ml-auto mr-0"><?= $module_input_placeholder ?></span>
                </div>
            <?php endif ?>

            <?php if ($module_input_type == 'multi-textarea' || $module_input_type == 'multi_textarea'): ?>
                <div
                    class="input-group-append multi_add_btn"
                    data-group-class="textarea-group"
                    data-input-slug="<?= $module_input_slug_lang ?>">
                    <button class="btn btn-outline-primary" type="button"><i class="fas fa-plus"></i></button>
                </div>
                <?php if ($module_input_placeholder): ?>
                    <div class="col-12 row text-muted small m-0">
                        <span class="ml-auto mr-0"><?= $module_input_placeholder ?></span>
                    </div>
                <?php endif; // if $module_input_placeholder ?>
            <?php endif; // if multi-textarea ?>
        </div>
    <?php
        endif; // trim $type_name_value
    endforeach;
    ?>
</div>
