<?php

require_once __DIR__ . '/../includes/_header.php';

// this code is responsible for listing the content in admin dash
if ($_GET['type'] == 'key_value_pair' || $_GET['type'] == 'api_key_secret') {
    ob_start();
    header('Location: /admin/meta');
}

if (isset($_GET['role'])) {
    $role = $types['user']['roles'][$_GET['role']] ?? null;
}

$_type = $_GET['type'] ?? false;
$_role = $_GET['role'] ?? false;
?>

<div>
    <?= $admin->get_admin_menu('list', $type, $role['slug'] ?? null); ?>

    <?php if ($type == 'user'): ?>
    <h5 class="mb-4">
    <?= $role['title'] ?>
    <small><i class="fas fa-angle-double-right"></i></small>
    List of <?= $types[$type]['plural'] ?>
    </h5>
    <?php endif ?>
    
</div>



<?php
// count number of records, if number of records are more than 25k, use ajax method with datatables
if ($_type == 'user') {
    $ids_number = $sql->executeSQL("SELECT COUNT(`id`) AS `total` FROM `data` WHERE `type`='$_type' AND `role_slug`='$_role'")[0]['total'];
} else {
    $ids_number = $sql->executeSQL("SELECT COUNT(*) AS `total` FROM `data` WHERE `type`='$_type'")[0]['total'];
}

// count the number of display columns, to limit table width to container size if there are fewer than 6 colummns
$listed_fields_number = 0 ;
foreach (array_column($types[$type]['modules'], 'list_field') as $is_listed)  { 
    if (isset($is_listed)) 
        $listed_fields_number++; 
}
?>

<?php if ($listed_fields_number>=6) { ?>
    </div><div class="px-lg-3"><!--closing container from includes/_header -->
<?php } ?>

    <form id="dtList" action="/admin/delete-dt-rows" method="post">
        <!-- delete modal -->
        <div id="deleteConfirm" class="modal fade" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Are you sure?</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>This will delete <span class="selectedListCount">n</span> item(s)</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Cancel</button>
                        <button id="deleteSelected" type="submit" class="btn btn-danger"><i class="fas fa-trash-alt mr-2"></i>Delete</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- / delete modal -->
        <!-- duplication modal -->
        <div id="duplicateConfirm" class="modal fade" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Are you sure?</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Make a copy <span class="selectedListCount">n</span> item(s)</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-copy mr-2"></i>Yes, copy</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- / duplication modal -->

        <input type="hidden" name="ids">
        <input type="hidden" name="type" value=<?=$_GET['type']?> >

        <table class="my-4 list table table-sm table-striped table-hover datatable border-bottom border-dark <?=($listed_fields_number>=6 ? 'cols-6-or-more' : '')?>" data-jsonpath="list-json" data-type="<?=$type?>" data-role="<?=$_GET['role'] ?? ''?>" data-lazyload=<?=($ids_number>25000 ? "true" : "false")?> >
            <thead class="thead-black">
                <tr>
                    <th scope="col">ID&nbsp;&nbsp;&nbsp;<span class="position-absolute mr-0" data-toggle="tooltip" data-placement="top" title="ID of a record is unique across the system. Slug is unique within the content type."><i class="fal fa-info-circle"></i></span></th>
                    <?php
                        $displayed_field_slugs = array();

                        foreach ($types[$type]['modules'] as $i => $module):
                            if (!in_array($module['input_slug'], $displayed_field_slugs)):
                                if (isset($module['list_field']) && $module['list_field']):
                    ?>
                    <th scope="col"
                        data-orderable="<?=isset($module['list_sortable']) ? $module['list_sortable'] : 'false'?>"
                        data-searchable="<?=isset($module['list_searchable']) ? $module['list_searchable'] : 'false'?>"
                        >
                        <?=$module['input_slug']?>
                    </th>
                    <?php
                                endif;
                                $displayed_field_slugs[] = $module['input_slug'];
                            endif;
                        endforeach;
                    ?>
                </tr>
            </thead>
        </table>
    </form>
</div>

<div id="toast-success" class="admin-toast toast position-fixed bg-dark text-white" role="alert" aria-live="assertive" aria-atomic="true" data-autohide="false">
    <div class="toast-body">
        <span>Changes saved successfully. Refresh to see</span>
        <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</div>

<div id="copy-success" class="admin-toast toast position-fixed bg-dark text-white" role="alert" aria-live="assertive" aria-atomic="true" data-autohide="true" data-delay="1000">
    <div class="toast-body">
        <span>Copied</span>
        <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</div>


<!-- Modal -->
<div class="modal fade editModal" id="editModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">...</h5>
        <button type="button" class="close editModalClose" data-id="" data-is_new="" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><span class="fal fa-lg fa-times"></span></span>
        </button>
      </div>
      <div class="modal-body">
        ...
      </div>
    </div>
  </div>
</div>

<?php if ($listed_fields_number>=6) { ?>
    </div><div class="p-3 container"> <!--opening container from includes/_header -->
<?php } ?>


<?php require_once __DIR__ . '/../includes/_footer.php';?>
