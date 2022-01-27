<?php
    $fn = new \Wildfire\Admin\Functions;

    $types_keys = array_keys($types);
    $db_record_dependency = array();

    // $db_record is mentioned in /admin/index.php
    foreach($db_record as $key => $value) {
        if (in_array($key, $types_keys)) {
            $_parent = $dash->getObject(['type' => $key, 'slug' => $value]);

            if (!$_parent && is_numeric($value)) {
                $_parent = $dash->getObject($value);
            }

            if ($_parent && $_parent['type'] == $key) {
                $db_record_dependency['parent'][] = $_parent;
            }

            unset($_parent);
        }
    }

    $db_record_dependency = $fn->getDbRecord($db_record_dependency, $db_record);

if ($_GET):
?>
<div class="px-0 col-lg-6 border border-light">
    <?php if (isset($db_record_dependency) && \sizeof($db_record_dependency)): ?>
    <table id="analysisTable" class="table table-borderless">
        <thead>
            <tr>
                <th>Related records</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($db_record_dependency as $parent_or_child => $single_record): ?>
                <?php foreach($single_record as $key => $record): ?>
                    <?php $fn->displayRecordCard(['record'=>$record, 'parent_or_child'=>$parent_or_child, 'types'=>$types]) ?>
                <?php endforeach ?>
            <?php endforeach ?>
        </tbody>
    </table>
    <?php else: ?>
        <div class="d-flex h-100 align-items-center justify-content-center">
            <p class="text-muted">No associations to list</p>
        </div>
    <?php endif ?>
</div>
<?php endif ?>
