<?php
/**
 * doc
 * $db_record is mentioned in /admin/index.php
 */
?>

<div class="col-lg-6 mx-auto">
    <div class="card">
        <div class="card-header">Search</div>

        <div id="search_wrapper" class="card-body">
            <?php // navigation for search options ?>
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
            <?php // navigation for search options ?>

            <div class="tab-content" id="nav-tabContent">
                <?php // search only by row_id in db ?>
                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                    <form id="searchById" class="needs-validation bg-white" method="post" action="/admin" novalidate>
                        <div class="mb-3 input-group">
                            <input type="number" name="row_id" class="form-control" placeholder="Search record by Id"
                                required>
                            <button class="btn btn-secondary" type="submit" data-search="id"><i class="far fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <?php // search a user by their slug ?>
                <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                    <form id="searchByUserSlug" class="bg-white" method="post" action="/admin" novalidate>
                        <div class="mb-3 input-group">
                            <input type="hidden" name="type" value="user">
                            <input type="text" name="slug" class="form-control" placeholder="Search user by slug" required>
                            <button class="btn btn-secondary" type="submit" data-search="userSlug"><i
                                    class="far fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <?php // search db based on type & slug ?>
                <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
                    <form id="searchByType" class="bg-white" method="post" action="/admin" novalidate>
                        <div class="mb-3 input-group">
                            <div class="input-group-prepend col-4 px-0">
                                <select name="type" id="search_type" class="custom-select">
                                    <option value="" disabled selected hidden>Select Type</option>
                                    <?php
                                        foreach($types as $t):
                                            if($t['type'] != 'content') {
                                                continue;
                                            }
                                    ?>
                                    <option value="<?= $t['slug'] ?>"><?= ucwords($t['plural']) ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <input type="text" name="slug" class="form-control" placeholder="Search type by slug" required>
                            <div class="input-group-append">
                                <button class="btn btn-secondary" type="submit" data-search="typeSlug">
                                    <i class="far fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php
                // card list - visible only when a search query is made
                if ($_POST):
            ?>
            <div class="card mb-3">
                <a class="w-100 text-left card-header d-flex justify-content-between align-items-center text-decoration-none"
                    data-toggle="collapse" href="#search_output" role="button" aria-expanded="false"
                    aria-controls="search_output">
                    <span><?= "{$db_record['id']} &#8594; {$db_record['type']} &#8594; {$db_record['slug']}" ?></span>
                    <i class="fas fa-plus-square"></i>
                </a>
                <div class="collapse" id="search_output">
                    <div class="card-body search_output">
                        <pre><?= json_encode($db_record, $json_options) ?></pre>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <span class="col-6 border-right border-light">
                                Created:<br><?=\date('d-M-Y H:i', $db_record['created_on'])?>
                            </span>
                            <span class="col-6">Updated:<br><?=\date('d-M-Y H:i', $db_record['updated_on'])?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif ?>
        </div>
        <!-- // div#search-wrapper -->
    </div>
    <!-- // div card -->
</div>
