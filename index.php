<?php require_once __DIR__ . '/includes/_header.php';?>

<?php echo $admin->get_admin_menu('dash'); ?>

<div class="card-group m-0">
    <?php if ($currentUser['role']=='admin') { ?>
    <div class="card my-2">
        <div class="card-header">Search by ID</div>
        <div class="card-body">
            <div class="input-group mb-3">
              <input type="text" class="form-control" placeholder="Type any ID used in the system">
              <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="button">Get data</button>
              </div>
            </div>
            <div class="border border-1 border-dark">
                <pre><?=json_encode($dash->get_content(1), JSON_PRETTY_PRINT) ?></pre>
            </div>
        </div>
    </div>
    <?php } ?>
    <div class="card my-2">
        <div class="card-header">Analytics</div>
        <div class="card-body">
            <p class="card-text">Porro quisquam et aliquid quas assumenda sunt culpa. Laudantium eum ex aut accusantium
                consequuntur dolor sed. Inventore quam quod est ut fugiat cumque beatae. Suscipit eaque non autem
                dignissimos voluptatibus quo et. Commodi aliquam est aut incidunt voluptatem et.</p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/_footer.php';?>
