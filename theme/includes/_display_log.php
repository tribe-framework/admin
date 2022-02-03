<?php if ($types['webapp']['display_activity_log'] ?? false): ?>
    <?php
        $sql = new \Wildfire\Core\MySQL;

        $activity_log = $post['mysql_activity_log'] ?? array();
        $activity_log = array_map('json_decode', $activity_log, array_fill(0, sizeof($activity_log), 1));
    ?>
    <div class="form-group">
        <button class="btn btn-light w-100 text-left d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#mysql_log"
            aria-expanded="false" aria-controls="mysql_log">
            <span><i class="fas fa-clipboard-list mr-3"></i>Log</span>
            <i class="fas fa-chevron-circle-down"></i>
        </button>
        <div class="collapse border border-light overflow-auto" id="mysql_log">
            <div class="container">
                <?php if (is_array($activity_log) && count($activity_log ?? [])): ?>
                    <?php
                        $access_log = \array_reverse($activity_log, true);
                        foreach ($access_log as $key => $log):
                    ?>
                    <p class="mb-0 small px-2 row <?= $key%2 ? 'bg-light' : 'bg-white' ?>">
                        <span class="text-muted mr-2 col-1 border-right border-black-50 text-center"><?= (int) $key + 1 ?></span>
                        <span class="text-warning text-center fw-bold col-2 border-right"><?= $log['time'] ?></span>
                        <span class="col">user <a href="/admin/edit?type=user&id=<?=$log['user_id']?>" class="text-secondary" target="_blank"><?= $log['user_name'] ? "{$log['user_id']} ({$log['user_name']})" : "{$log['user_id']}" ?></a> <?= $log['message'] ?></span>
                    </p>
                    <?php endforeach ?>
                <?php else: ?>
                    <p class="text-muted mb-0 py-4 text-center">No records to display</p>
                <?php endif ?>
            </div>
        </div>
    </div>
<?php endif; ?>
