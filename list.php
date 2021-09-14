<?php require_once __DIR__ . '/includes/_header.php';?>
<?php
/**
 * this code is responsible for listing the content in admin dash
 */
?>

<?php
if ($_GET['type'] == 'key_value_pair' || $_GET['type'] == 'api_key_secret') {
    ob_start();
    header('Location: /admin/meta');
}
?>

<div class="p-3">
    <?php
if ($_GET['role']) {
    $role = $types['user']['roles'][$_GET[role]];
}

echo $admin->get_admin_menu('list', $type, $role['slug']);
?>


    <h2 class="mb-4">
        <?php if ($type == 'user'): ?>
            <?=$role['title']?>&nbsp;
            <small><span class="fas fa-angle-double-right"></span></small>&nbsp;
        <?php endif;?>

        List of <?=$types[$type]['plural']?>
    </h2>

    <table class="my-4 table table-borderless table-hover datatable">
        <thead class="thead-black">
            <tr>
                <th scope="col">#</th>
                <?php
$i = 0;
$displayed_field_slugs = array();

foreach ($types[$type]['modules'] as $module):
    if (!in_array($module['input_slug'], $displayed_field_slugs)):
        if (isset($module['list_field']) && $module['list_field']):
        ?><th scope="col" class="pl-2" data-orderable="<?=isset($module['list_sortable']) ? $module['list_sortable'] : 'false'?>" data-searchable="<?=isset($module['list_searchable']) ? $module['list_searchable'] : 'false'?>" style="<?=(isset($module['input_primary']) && $module['input_primary']) ? 'max-width:50%' : ''?>"><?=$module['input_slug']?></th><?php
    endif;
    $displayed_field_slugs[] = $module['input_slug'];
endif;
$i++;
endforeach;
?>
                <th scope="col" data-orderable="false" data-searchable="false"></th>
            </tr>
        </thead>

    </table>
</div>

<?php require_once __DIR__ . '/includes/_footer.php';?>
