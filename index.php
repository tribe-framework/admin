<?php
require_once __DIR__ . '/includes/_header.php';

echo $admin->get_admin_menu('dash');

if ($_POST) {
    if ($_POST['row_id']) {
        $db_record = $dash->get_content($_POST['row_id']);
    } else if ($_POST['type'] && $_POST['slug']) {
        $search = array(
            'type' => $_POST['type'],
            'slug' => $_POST['slug']
        );

        $db_record = $dash->get_content($search);
    }
}

$json_options = JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PARTIAL_OUTPUT_ON_ERROR|JSON_PRETTY_PRINT;
?>

<div class="card-group m-0">
    <?php
        if ($currentUser['role'] == 'admin') {
            require_once __DIR__.'/includes/_search_panel.php';
        }

        require_once __DIR__.'/includes/_analysis_panel.php';
    ?>
</div>

<?php require_once __DIR__ . '/includes/_footer.php' ?>
