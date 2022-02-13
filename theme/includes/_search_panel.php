<?php
$fn = new \Wildfire\Admin\Functions;

/**
 * doc
 * $db_record is mentioned in /admin/index.php
 */
?>

<div class="col-lg-6 mb-2 mb-lg-0">
    <div class="card">
        <div class="card-header font-weight-bold text-uppercase small px-3 py-1"><em><span class="fal fa-search"></span>&nbsp;Search</em></div>

        <div id="search_wrapper" class="card-body p-0">
            <?php // navigation for search options ?>
            <nav>
                <div class="nav nav-pills" id="nav-tab" role="tablist">
                    <a class="small font-weight-light nav-link text-uppercase active" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab"
                        aria-controls="nav-home" aria-selected="true">By ID</a>
                    <a class="small font-weight-light nav-link text-uppercase" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab"
                        aria-controls="nav-profile" aria-selected="false">By User Slug</a>
                    <a class="small font-weight-light nav-link text-uppercase" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab"
                        aria-controls="nav-contact" aria-selected="false">By Type &amp; Slug</a>
                </div>
            </nav>
            <?php // navigation for search options ?>

            <div class="tab-content" id="nav-tabContent">
                <?php // search only by row_id in db ?>
                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                    <form id="searchById" class="needs-validation bg-white" method="get" action="/admin" novalidate>
                        <div class="input-group">
                            <input type="number" name="row_id" class="form-control" value="<?=$_GET['row_id'] ?? ''?>" placeholder="Enter ID"
                                required>
                            <button class="btn btn-secondary" type="submit" data-search="id"><i class="far fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <?php // search a user by their slug ?>
                <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                    <form id="searchByUserSlug" class="bg-white" method="get" action="/admin" novalidate>
                        <div class="input-group">
                            <input type="hidden" name="type" value="user">
                            <input type="text" name="slug" class="form-control" placeholder="Enter user slug" required>
                            <button class="btn btn-secondary" type="submit" data-search="userSlug"><i
                                    class="far fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <?php // search db based on type & slug ?>
                <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
                    <form id="searchByType" class="bg-white" method="get" action="/admin" novalidate>
                        <div class="input-group">
                            <div class="input-group-prepend col-4 px-0">
                                <select name="type" id="search_type" class="custom-select">
                                    <option value="" disabled selected hidden>Select Type</option>
                                    <?php
                                        foreach($types as $t):
                                            if(($t['type'] ?? null) != 'content') {
                                                continue;
                                            }
                                    ?>
                                    <option value="<?= $t['slug'] ?>"><?= ucwords($t['plural']) ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                            <input type="text" name="slug" class="form-control" placeholder="Enter slug" required>
                            <div class="input-group-append">
                                <button class="btn btn-secondary" type="submit" data-search="typeSlug">
                                    <i class="far fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php if ($_GET): // card list - visible only when a search query is made ?>
            <div class="mt-3">
                <?php $fn->displayRecordCard(['record' => $db_record, 'parent_or_child' => 'parent', 'types' => $types, 'tab_default_state' => 'show', 'display_legend' => true]) ?>
            </div>
            <?php endif ?>
        </div>
        <!-- // div#search-wrapper -->
    </div>
    <!-- // div card -->
</div>
