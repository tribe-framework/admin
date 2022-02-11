<div class="url-group" id="url-group-<?php echo $module_input_slug_lang; ?>">

<?php
    $i=0;
    $type_name_values=array();
    if (
        isset($post[$module_input_slug_lang]) &&
        is_array($post[$module_input_slug_lang])
    ) {
        $type_name_values=$post[$module_input_slug_lang];
    } else if (
        isset($post[$module_input_slug_lang]) &&
        $post[$module_input_slug_lang]
    ) {
        $type_name_values[0]=$post[$module_input_slug_lang];
    } else {
        $type_name_values[0]=$module_input_default_value;
    }
?>

<?php
    foreach ($type_name_values as $type_name_value):
        if ($i<1 || trim($type_name_value)):
?>
    <div class="input-group mt-5">
        <div class="input-group-prepend">
            <span id="basic-addon1" class="input-group-text border-top-0 border-left-0 border-right-0 rounded-0"
                ><i class="fas fa-link"></i>
            </span>
        </div>

        <input
            type="url"
            name="<?= $module_input_slug_lang.(($module_input_type=='multi-url' || $module_input_type=='multi_url') ? '[]' : '') ?>"
            class="form-control border-top-0 border-left-0 border-right-0 rounded-0 m-0"
            placeholder="<?=
                isset($module_input_placeholder) ?
                    $module_input_placeholder :
                    ucfirst($types[$type]['name'])." $module_input_slug_lang"
            ?>"
            value="<?= $type_name_value ?>">

            <?php if (($module_input_type=='multi-url' || $module_input_type=='multi_url')): ?>
                <div
                    class="input-group-append multi_add_btn"
                    data-group-class="url-group"
                    data-input-slug="<?= $module_input_slug_lang ?>"
                    ><button class="btn btn-outline-primary" type="button"><i class="fas fa-plus"></i></button>
                </div>
            <?php endif; ?>
    </div>

    <?php if (isset($module_input_placeholder)): ?>
        <div class="col-12 row text-muted small m-0"
            ><span class="ml-auto mr-0"><?= $module_input_placeholder ?></span>
        </div>
    <?php endif; ?>
<?php
        endif;
    $i++;
    endforeach;
?>
</div>
