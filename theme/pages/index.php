<?php
/**
 * @var object $admin
 * @var object $dash
 * @var array $currentUser
 */
require_once __DIR__.'/../includes/_header.php';

echo $admin->get_admin_menu('dash');

if ($_GET) {
    if ($_GET['row_id']) {
        $db_record = $dash->getObject($_GET['row_id']);
    } else if ($_GET['type'] && $_GET['slug']) {
        $search = array(
            'type' => $_GET['type'],
            'slug' => $_GET['slug']
        );

        $db_record = $dash->getObject($search);
    }
}

$json_options = JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PARTIAL_OUTPUT_ON_ERROR|JSON_PRETTY_PRINT;
?>

<div class="row">
    <?php
        if ($currentUser['role'] == 'admin') {
            require_once ADMIN_THEME.'/includes/_search_panel.php';
        }

        if (isset($db_record)) {
            require_once ADMIN_THEME.'/includes/_analysis_panel.php';
        }
    ?>
</div>

<?php require_once ADMIN_THEME.'/includes/_footer.php' ?>
