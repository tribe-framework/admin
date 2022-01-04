<?php
require_once __DIR__."/includes/_header.php";

$json_encode_options = JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PARTIAL_OUTPUT_ON_ERROR|JSON_PRETTY_PRINT|JSON_NUMERIC_CHECK;
$types_location = TRIBE_ROOT . "/config/types.json";
$types_data = \file_get_contents($types_location);
$types_data = \json_decode($types_data, 1);
$current_page = $_GET['edit'] ?? 'webapp';

if ($_POST) {
    $types_data['webapp'] = \array_replace($types_data['webapp'], $_POST);
    $types_data = \json_encode($types_data, $json_encode_options);
    \file_put_contents($types_location, $types_data);
    $types_data = \json_decode($types_data, 1);
    $types = \array_replace($types, $types_data);
}
?>

<div class="row">
    <div class="col-12 mx-auto">
        <div class="col-12">
            <form action="" method="post">
                <div class="mb-4">
                    <div class="card-body p-0">
                        <div class="btn-toolbar justify-content-between">
                            <?= $admin->list_types($type) ?>
                            <button type="submit" class="btn btn-outline-primary border-top-0 border-left-0 border-right-0 rounded-0 save_btn"><span class="fa fa-save mr-2"></span>Save</button>
                        </div>
                    </div>
                </div>

                <h1 class="mt-3 h2">Edit types.json / <?= $current_page ?></h1>

                <div class="d-flex mb-4 flex-wrap">
                    <?php
                        foreach ($types_data as $type_name => $t):
                            $badge_color = ($type_name == $current_page) ? 'badge-primary' : 'badge-info';
                    ?>
                        <a href="/admin/types?edit=<?=$type_name?>" class="badge badge-pill <?=$badge_color?> mr-2"><?= $type_name ?></a>
                    <?php endforeach; ?>
                </div>

                <?php
                    switch ($current_page) {
                        case 'webapp':
                            include_once __DIR__."/includes/_edit-types-webapp.php";
                            break;

                        case 'user':
                            include_once __DIR__."/includes/_edit-types-user.php";
                            break;

                        default:
                            include_once __DIR__."/includes/_edit-types-other.php";
                            break;
                    }
                ?>

                <div class="mb-4">
                    <div class="card-body p-0">
                        <div class="btn-toolbar justify-content-between">
                            <?= $admin->list_types($type) ?>
                            <button type="submit" class="btn btn-outline-primary border-top-0 border-left-0 border-right-0 rounded-0 save_btn"><span class="fa fa-save mr-2"></span>Save</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__."/includes/_footer.php" ?>
