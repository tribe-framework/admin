<?php
$dash = new \Wildfire\Core\Dash;

$ignored_fields = ['searchable_types'];

foreach ($types_data["webapp"] as $key => $value):
    $v_type = \gettype($value);

    if ($v_type == "string" && !\in_array($key, $ignored_fields)):
?>
    <div class="form-group">
        <label class="h6" for="<?= "{webapp_{$key}}" ?>"><?=$key?></label>
        <input class="form-control" type="text" name="<?= $key ?>" id="<?= "webapp_{$key}" ?>"
            value="<?=$value?>">
    </div>
<?php elseif ($v_type == "boolean" || ($v_type == "integer" && \in_array($value, [0,1]))): ?>
    <div class="custom-control custom-switch">
        <input type="hidden" name="<?=$key?>" value="0">
        <input class="custom-control-input" type="checkbox" id="<?= "webapp_{$key}" ?>" name="<?=$key?>"
            <?= $value ? 'checked': '' ?> value="1">
        <label class="custom-control-label" for="<?= "webapp_{$key}" ?>">
            <?=$key?>
        </label>
    </div>
<?php elseif($key == "searchable_types"): ?>
    <div class="form-group mt-3">
        <p class="h6"><?=$key?></p>
        <input type="hidden" name="searchable_types" value="">
        <?php
            $types_data_keys = $types_data;
            unset($types_data_keys['webapp']);
            $types_data_keys = \array_keys($types_data_keys);

            foreach ($types_data_keys as $i => $type_key):
        ?>
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" name="searchable_types[]"
                id="searchable_type_<?=$i?>" value="<?=$type_key?>"
                <?= \in_array($type_key, $types_data['webapp']['searchable_types']) ? 'checked' : ''?>>
            <label class="custom-control-label"
                for="searchable_type_<?=$i?>"><?=$type_key?>
            </label>
        </div>
        <?php endforeach ?>
    </div>
<?php endif ?>
<?php endforeach ?>
