<?php
    $types_keys = array_keys($types);

    // $db_record is mentioned in /admin/index.php
    foreach($db_record as $key => $value) {
        if (in_array($key, $types_keys)) {
            $search = [
                'type' => $key,
                'slug' => $value
            ];
            $db_record_dependency[] = $dash->get_content($search);
        }
    }

    function getDbRecord ($db_record_dependency, $db_record) {
        $sql = new \Wildfire\Core\MySQL;
        $dash = new \Wildfire\Core\Dash;

        $q = $sql->executeSQL("SELECT `id` FROM `data` WHERE `content`->>'$.{$db_record['type']}_id'='{$db_record['slug']}'");

        if ($q) {
            foreach($q as $v) {
                $db_record_dependency[] = $dash->get_content($v['id']);
            }
            unset($q);
        }

        $q = $sql->executeSQL("SELECT `id` FROM `data` WHERE FIND_IN_SET(`content`->>'$.{$db_record['type']}_ids', '{$db_record['id']}')");

        if ($q) {
            foreach($q as $v) {
                $db_record_dependency[] = $dash->get_content($v['id']);
            }
            unset($q);
        }

        $q = $sql->executeSQL("SELECT `id` FROM `data` WHERE `content`->>'$.{$db_record['type']}'='{$db_record['slug']}'");

        if ($q) {
            foreach($q as $v) {
                $db_record_dependency[] = $dash->get_content($v['id']);
            }
            unset($q);
        }

        $q = $sql->executeSQL("SELECT `id` FROM `data` WHERE JSON_CONTAINS(`content`->>'$.{$db_record['type']}', '\"{$db_record['slug']}\"', '$')");

        if ($q) {
            foreach($q as $v) {
                $db_record_dependency[] = $dash->get_content($v['id']);
            }
            unset($q);
        }

        return $db_record_dependency;
    }

    $db_record_dependency = getDbRecord($db_record_dependency, $db_record);
?>

<?php if ($_POST): ?>
<div class="px-0 col-lg-6 border border-light">
    <?php if (\sizeof($db_record_dependency)): ?>
    <table id="analysisTable" class="table table-borderless">
        <thead>
            <tr>
                <th>Associations</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($db_record_dependency as $key => $record): ?>
                <tr class="col-12">
                    <td>
                        <div class="card">
                            <a class="w-100 text-left card-header d-flex justify-content-between align-items-center text-decoration-none"
                                data-toggle="collapse" href="#output_<?=$key?>" role="button" aria-expanded="false"
                                aria-controls="output_<?=$key?>">
                                <span><?= "{$record['id']} &#8594; {$record['type']} &#8594; {$record['slug']}" ?></span>
                                <i class="fas fa-plus-square"></i>
                            </a>
                            <div class="collapse" id="output_<?=$key?>">
                                <div class="card-body search_output">
                                    <pre style="width:50ch;" class="overflow-auto"><?= \json_encode($record, $json_options) ?></pre>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <span class="col-6 border-right border-light">
                                            Created:<br><?=\date('d-M-Y H:i', $record['created_on'])?>
                                        </span>
                                        <span class="col-6">Updated:<br><?=\date('d-M-Y H:i', $record['updated_on'])?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
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
