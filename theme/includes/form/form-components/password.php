<div class="input-group mt-5">
    <div class="input-group-prepend">
        <span id="basic-addon1" class="input-group-text border-top-0 border-left-0 border-right-0 rounded-0"
            ><i class="fas fa-key"></i>
        </span>
    </div>

    <input
        autocomplete="off"
        type="password"
        name="<?= $module_input_slug_lang ?>"
        class="form-control border-top-0 border-left-0 border-right-0 rounded-0 m-0"
        placeholder="<?= $module_input_placeholder ?
            $module_input_placeholder :
            ucfirst($types[$type]['name']).' '.$module_input_slug_lang
        ?>"
    >
    <?php if ($post && $post[$module_input_slug_lang]): ?>
        <small class="col-12 row form-text text-muted"
            >To keep the password unchanged, leave this field empty
        </small>
    <?php endif ?>
</div>

<?php if($module_input_placeholder): ?>
    <div class="col-12 row text-muted small m-0">
        <span class="ml-auto mr-0"
            ><?= $module_input_placeholder ?>
        </span>
    </div>
<?php endif ?>
