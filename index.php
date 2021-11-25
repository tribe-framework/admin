<?php
require_once __DIR__ . '/includes/_header.php';

echo $admin->get_admin_menu('dash');
?>

<div class="card-group m-0">
    <?php if ($currentUser['role']=='admin') { ?>
    <div class="card my-2">
        <div class="card-header">Search by ID</div>
        <div id="search_wrapper" class="card-body">
            <form class="needs-validation sticky-top bg-white" novalidate>
                <div class="input-group mb-3">
                    <input type="number" class="form-control" placeholder="Type any ID used in the system" required>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="submit">Fetch</button>
                    </div>
                    <div class="invalid-feedback">Invalid</div>
                </div>
            </form>
            <div id="search_output" class="border border-light d-flex flex-column">
                <pre></pre>
                <button class="btn btn-secondary mt-2 align-self-end"
                    ><i class="fas fa-broom mr-1 me-1"></i>Clear result
                </button>
            </div>
        </div>
    </div>
    <?php } ?>
    <div class="card my-2">
        <div class="card-header">Analytics</div>
        <div class="card-body">
            <p class="card-text text-muted">Coming soon...</p>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/_footer.php';?>
