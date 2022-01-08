<?php
$placeholder = $module_input_placeholder ?: ucfirst($types[$type]['name']) . " $module_input_slug_lang";
?>

 <div id="text-group" class="text-group">
    <div class="input-group">
        <div class="input-group-prepend border-right">
            <span
                class="input-group-text justify-content-center"
                style="min-width: 3rem;"
                ><i class="fas fa-align-justify"></i>
            </span>
        </div>

        <input
            type="text"
            name="<?= $module_input_slug_lang ?>"
            class="form-control m-0"
            placeholder="<?= $placeholder ?>"
            value="<?= $type_name_value ?>"
            data-type="csv"
        >
    </div>
    <div class="col-12 row text-muted small m-0">
        <span class="ml-auto mr-0"><?= $placeholder ?> (CSV)</span>
    </div>
</div>
