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
                        aria-controls="nav-home" aria-selected="true">By ID</a>
                    <a class="nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab"
                        aria-controls="nav-profile" aria-selected="false">By User Slug</a>
                    <a class="nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab"
                        aria-controls="nav-contact" aria-selected="false">Type and Slug</a>
                </div>
            </nav>
            <?php // navigation for search options ?>

            <div class="tab-content" id="nav-tabContent">
                <?php // search only by row_id in db ?>
                <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                    <form id="searchById" class="needs-validation bg-white" method="get" action="/admin" novalidate>
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
                    <form id="searchByUserSlug" class="bg-white" method="get" action="/admin" novalidate>
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
                    <form id="searchByType" class="bg-white" method="get" action="/admin" novalidate>
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
                if ($_GET):
            ?>
            <?php displayRecordCard($db_record, 'parent', $json_options, $types) ?>
            <?php endif ?>
        </div>
        <!-- // div#search-wrapper -->
    </div>
    <!-- // div card -->
</div>

<?php
function displayRecordCard ($record, $parent_or_child='child', $json_options='', $types=[]) {

    if ($record['id']): //if row_id exists

    //IF THE MODULE HAS A TITLE, USE IT, OR ELSE SHOW SLUG
    $record_type = $record['type'];
    $type_primary_module = $types[$record_type]['primary_module'];
    if ($type_primary_module && !($record_title = $record[$type_primary_module]))
        $record_title = $record['slug'];
    ?>

    <tr class="col-12 <?= $parent_or_child == 'parent' ? 'bg-info' : 'bg-light' ?>">
        <td>
            <div class="card">
                <a class="p-2 w-100 text-left card-header d-flex justify-content-between align-items-center text-decoration-none"
                    data-toggle="collapse" href="#output_<?=$record['id']?>" role="button" aria-expanded="false"
                    aria-controls="output_<?=$record['id']?>">
                    <h6 class="font-weight-light mb-0"><?= "<span class=\"badge badge-pill badge-success mr-2\">{$record['id']}</span><span class=\"badge badge-pill badge-primary mr-2\">{$record['type']}".($record['role_slug'] ? " | ".$record['role_slug'] : "")."</span><span class=\"pt-1\">{$record_title}</span>" ?></h6>
                    <i class="fal fa-chevron-down"></i>
                </a>
                <div class="collapse" id="output_<?=$record['id']?>">
                    <div class="card-body search_output p-0">
                        <pre style="width:50ch;" class="overflow-auto"><?= \json_encode($record, $json_options) ?></pre>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <span class="col-6 border-right border-light small">
                                <?php
                                $meta_1[] = ( $record['created_on'] ? 'created_on: '.\date('d-M-Y H:i', $record['created_on']) : null );
                                $meta_1[] = ( $record['updated_on'] ? 'updated_on: '.\date('d-M-Y H:i', $record['updated_on']) : null );
                                echo implode('<br>', array_filter($meta_1,'strlen'));
                                ?>
                            </span>
                            <span class="col-5 border-right border-light small">
                                <?php
                                $meta_2[] = ( $record['user_id'] ? 'user_id: '.$record['user_id'] : null );
                                $meta_2[] = ( $record['created_by'] ? 'created_by: '.$record['created_by'] : null );
                                $meta_2[] = ( $record['updated_by'] ? 'updated_by: '.$record['updated_by'] : null );
                                $meta_2[] = ( $record['content_privacy'] ? 'content_privacy: '.$record['content_privacy'] : null );
                                echo implode('<br>', array_filter($meta_2,'strlen'));
                                ?>
                            </span>
                            <span class="col-1">
                                <?php
                                $meta_3[] = '<a href="/admin/?row_id='.$record['id'].'"><span class="fas fa-external-link-alt"></span></a>';
                                echo implode('<br>', array_filter($meta_3,'strlen'));
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </td>
    </tr>
    <?php
    endif; //if row_id exists
}
?>