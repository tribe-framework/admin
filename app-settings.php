<?php use \Wildfire\Core\Console ?>
<?php require_once __DIR__."/includes/_header.php" ?>

<?php
$json_encode_options = JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PARTIAL_OUTPUT_ON_ERROR|JSON_PRETTY_PRINT|JSON_NUMERIC_CHECK;
$types_location = TRIBE_ROOT . "/config/types.json";
$types_data = \file_get_contents($types_location);
$types_data = \json_decode($types_data, 1);

if ($_POST) {
    // $types_data['webapp'] = $_POST;
    $types_data['webapp'] = \array_replace($types_data['webapp'], $_POST);
    $types_data = \json_encode($types_data, $json_encode_options);
    \file_put_contents($types_location, $types_data);
    $types_data = \json_decode($types_data, 1);
    $types = \array_replace($types, $types_data);
}
?>

<div class="row">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0 bg-white px-0">
            <li class="breadcrumb-item small"><a href="/admin">Home</a></li>
            <li class="breadcrumb-item small active" aria-current="page">Settings</li>
        </ol>
    </nav>
</div>

<div class="row">
    <nav class="col-md-3 bg-light">
        <h5 class="mt-3 text-muted">Settings</h5>
        <ul class="nav flex-column">
            <li class="nav-item"><a class="nav-link active" href="/admin/app-settings"><i
                        class="fal fa-browser mr-2"></i>Application</a></li>
        </ul>
    </nav>
    <div class="col-12 col-md-9">
        <h1 class="mt-3 h2 text-muted text-capitalize mb-0">Manage app settings</h1>
        <div class="col-md-7">
            <form action="" method="post">
                <?php
                    $dash = new \Wildfire\Core\Dash();

                    $ignored_fields = ['searchable_types'];

                    foreach ($types_data["webapp"] as $key => $value):
                        $v_type = \gettype($value);

                        if ($v_type == "string" && !\in_array($key, $ignored_fields)):
                ?>
                <div class="form-group">
                    <label class="h6" for="<?= "{webapp_{$key}}" ?>"><?=ucwords(str_replace('_', ' ', $key))?></label>
                    <input class="form-control" type="text" name="<?= $key ?>" id="<?= "webapp_{$key}" ?>"
                        value="<?=$value?>">
                </div>
                <?php elseif ($v_type == "boolean" || ($v_type == "integer" && \in_array($value, [0,1]))): ?>
                <div class="custom-control custom-switch">
                    <input type="hidden" name="<?=$key?>" value="0">
                    <input class="custom-control-input" type="checkbox" id="<?= "webapp_{$key}" ?>" name="<?=$key?>"
                        <?= $value ? 'checked': '' ?> value="1">
                    <label class="custom-control-label" for="<?= "webapp_{$key}" ?>">
                        <?=ucwords(str_replace('_', ' ', $key))?>
                    </label>
                </div>
                <?php elseif($key == "searchable_types"): ?>
                <div class="form-group mt-3">
                    <p class="h6"><?=ucwords(str_replace('_', ' ', $key))?></p>
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
                            for="searchable_type_<?=$i?>"><?=ucwords(str_replace('_', ' ', $type_key))?>
                        </label>
                    </div>
                    <?php endforeach ?>
                </div>
                <?php endif ?>
                <?php endforeach ?>

                <button type="submit" class="btn btn-primary px-5">Save</button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__."/includes/_footer.php" ?>
