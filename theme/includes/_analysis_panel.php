<?php
    $types_keys = array_keys($types);

    // $db_record is mentioned in /admin/index.php
    foreach($db_record as $key => $value) {
        if (in_array($key, $types_keys)) {
            $search = [
                'type' => $key,
                'slug' => $value
            ];
            $db_record_dependency['parent'][] = $dash->getObject($search);
        }
    }

    function getDbRecord ($db_record_dependency, $db_record) {
        $sql = new \Wildfire\Core\MySQL;
        $dash = new \Wildfire\Core\Dash;

        $q = $sql->executeSQL("SELECT `id` FROM `data` WHERE `content`->>'$.{$db_record['type']}_id'='{$db_record['slug']}' LIMIT 1000");

        if ($q) {
            foreach($q as $v) {
                $db_record_dependency['child'][] = $dash->getObject($v['id']);
            }
            unset($q);
        }

        $q = $sql->executeSQL("SELECT `id` FROM `data` WHERE FIND_IN_SET(`content`->>'$.{$db_record['type']}_ids', '{$db_record['id']}') LIMIT 1000");

        if ($q) {
            foreach($q as $v) {
                $db_record_dependency['child'][] = $dash->getObject($v['id']);
            }
            unset($q);
        }

        $q = $sql->executeSQL("SELECT `id` FROM `data` WHERE `content`->>'$.{$db_record['type']}'='{$db_record['slug']}' LIMIT 1000");

        if ($q) {
            foreach($q as $v) {
                $db_record_dependency['child'][] = $dash->getObject($v['id']);
            }
            unset($q);
        }

        $q = $sql->executeSQL("SELECT `id` FROM `data` WHERE JSON_CONTAINS(`content`->>'$.{$db_record['type']}', '\"{$db_record['slug']}\"', '$') LIMIT 1000");

        if ($q) {
            foreach($q as $v) {
                $db_record_dependency['child'][] = $dash->getObject($v['id']);
            }
            unset($q);
        }

        return $db_record_dependency;
    }

    if ($_GET && isset($db_record_dependency)) {
        $db_record_dependency = getDbRecord($db_record_dependency, $db_record);
    }
?>

<?php if ($_GET): ?>
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
                    <?php displayRecordCard($record, $parent_or_child, $json_options, $types) ?>
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
