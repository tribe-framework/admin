<?php
require_once __DIR__ . '/includes/_header.php';

echo $admin->get_admin_menu('dash');
?>

<div class="card-group m-0">
    <?php if ($currentUser['role']=='admin') { ?>
    <div class="card my-2">
        <div class="card-header">Search</div>
        <div id="search_wrapper" class="card-body">
            <nav>
                <div class="nav nav-pills" id="nav-tab" role="tablist">
                    <a class="nav-link active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab"
                        aria-controls="nav-home" aria-selected="true">By Id</a>
                    <a class="nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab"
                        aria-controls="nav-profile" aria-selected="false">User by slug</a>
                    <a class="nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab"
                        aria-controls="nav-contact" aria-selected="false">Type+Slug</a>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                    <form id="searchById" class="needs-validation bg-white" novalidate>
                        <div class="mb-3 input-group">
                            <input type="number" class="form-control" placeholder="Search record by Id" required>
                            <button class="btn btn-secondary" type="submit" data-search="id"
                                ><i class="far fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                    <form id="searchByUserSlug" class="bg-white" novalidate>
                        <div class="mb-3 input-group">
                            <input type="text" class="form-control" placeholder="Search user by slug" required>
                            <button class="btn btn-secondary" type="submit" data-search="userSlug"
                                ><i class="far fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
                    <form id="searchByType" class="bg-white" novalidate>
                        <div class="mb-3 input-group">
                            <div class="input-group-prepend col-4 px-0">
                                <select name="search_type" id="search_type" class="custom-select">
                                    <option value="" disabled selected hidden>Select Type</option>
                                    <?php
                                        foreach($types as $t):
                                            if($t['type'] == 'content'):
                                    ?>
                                    <option value="<?= $t['slug'] ?>"><?= ucfirst($t['plural']) ?></option>
                                    <?php
                                            endif;
                                        endforeach;
                                    ?>
                                </select>
                            </div>
                            <input type="text" class="form-control" placeholder="Search type by slug" required>
                            <div class="input-group-append">
                                <button class="btn btn-secondary" type="submit" data-search="typeSlug"
                                    ><i class="far fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div id="search_output" class="border border-light d-flex flex-column">
                <pre></pre>
                <button class="btn btn-secondary mt-2 align-self-end"><i class="fas fa-broom mr-1 me-1"></i>Clear result
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
