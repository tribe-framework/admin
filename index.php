<?php
require_once __DIR__ . '/includes/_header.php';

echo $admin->get_admin_menu('dash');

if ($_GET) {
    if ($_GET['row_id']) {
        $db_record = $dash->get_content($_GET['row_id']);
    } else if ($_GET['type'] && $_GET['slug']) {
        $search = array(
            'type' => $_GET['type'],
            'slug' => $_GET['slug']
        );

        $db_record = $dash->get_content($search);
    }
}

$json_options = JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PARTIAL_OUTPUT_ON_ERROR|JSON_PRETTY_PRINT;
?>

<div class="row">
    <?php
        if ($currentUser['role'] == 'admin') {
            require_once __DIR__.'/includes/_search_panel.php';
        }

        require_once __DIR__.'/includes/_analysis_panel.php';
    ?>
</div>

<?php require_once __DIR__ . '/includes/_footer.php' ?>
