<?php

$_ignoreKeys = 'headmeta_title_glue,headmeta_title_prepend,headmeta_title_append,modules,type,slug';
$_ignoreKeys = explode(',', $_ignoreKeys);

$_type = $types_data[$current_page];

foreach ($_ignoreKeys as $key) {
    unset($_type[$key]);
}
?>

<?php foreach ($_type as $key => $value): ?>
    <?php if ($key != 'disallow_editing'): ?>
        <div class="form-group">
            <label for="<?=$key?>" class="h6 mb-0"><?=$key?></label>
            <input id="<?=$key?>" name="<?=$key?>" class="form-control" type="text" value="<?=$value?>">
        </div>
    <?php else: ?>
        <div class="custom-control custom-switch">
            <input type="hidden" name="<?=$key?>" value="0">
            <input type="checkbox" class="custom-control-input" name="<?=$key?>" id="<?=$key?>" value="1" <?=$value ? 'checked' : ''?> >
            <label class="custom-control-label" for="<?=$key?>"><?=$key?></label>
        </div>
    <?php endif ?>
<?php endforeach ?>
