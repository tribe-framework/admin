<?php
/**
 * this code is responsible for editing and modifying content in admin dashboard
 * basically the form that the admin uses
 *
 * @var object $dash
 * @var object $admin
 * @var array $currentUser
 * @var array $post
 * @var array $types
 * @var string $type
 */

require_once __DIR__ . '/../_init.php';

if (($_GET['edit_form'] ?? null) == 'true') {
    require_once __DIR__ .'/../includes/_header.php';
}

$role = null;

if (isset($_GET['id'])) {
	$post = $dash->getObject($_GET['id']);
}

$is_user_allowed = ($currentUser['role'] == 'admin') || ($post['user_id'] == $currentUser['user_id']) || (!$_GET['id']);

if (!$is_user_allowed) {
    header('Location: /admin/');
    die();
}

if (isset($_GET['role'])) {
    $role = $types['user']['roles'][$_GET['role']];
}

if (($post['type'] ?? ($_GET['type'] ?? null)) != $type) {
    header('Location: /admin/');
    die();
}

//for testing restricted min and max ids for archive format changes
if (isset($_GET['id']) && !($pid = $_GET['id'])) {
    $pid = $dash->get_next_id();
}
?>

<!-- css for typeout -->
<link rel="stylesheet" type="text/css" href="<?=ADMIN_URL?>/plugins/typeout/typeout.css">

<!-- popup banner for save (probably not being used anymore) -->
<div class="popup-banner">
    <a id="infos"></a>
    <div id="infos" class="d-none alert alert-success shadow shadow-sm mb-0">
        <div class="progress"></div>
        <span class="text"></span>
    </div>

    <a id="errors"></a>
    <div id="errors" class="d-none alert alert-danger shadow shadow-sm mb-0">
        <div class="progress"></div>
        <span class="text"></span>
    </div>
</div>

<form method="post" class="edit_form" action="/admin/json" autocomplete="off">
    <?=
		$admin->get_admin_menu(
			($types[$type]['disallow_editing'] ?? null) ? 'view' : 'edit',
			$type,
			$role['slug'] ?? '',
			$_GET['id'] ?? ''
		);
    ?>

    <div class="form-style">
        <?php require ADMIN_THEME . '/includes/form/form.php';?>
    </div>

    <?php if (($_GET['type'] ?? null) == 'user' && isset($_GET['role'])) : ?>
        <input type="hidden" name="role_slug" value="<?= $_GET['role'] ?>">
    <?php endif ?>
    <input type="hidden" name="class" value="dash">

<?php
$is_content_change_allowed = ($types['webapp']['allow_type_change'] ?? false) && ($types[$type]['type'] == 'content');
if ($is_content_change_allowed):
?>
    <div class="form-group mt-5">
        <select class="form-control pl-0 border-top-0 border-left-0 border-right-0 rounded-0 mt-1" id="select_type" name="type">
        <?php
        if (!($post_type = $post['type'])) {
            $post_type = $_GET['type'];
        }

        foreach ($types as $key => $value):
            if ($value['type'] == 'content'): ?>
            <option value="<?=$value['slug']?>"
                <?=$value['slug'] === $post_type ? 'selected="selected"' : ''?>>
                <?=ucfirst($value['name'])?>
            </option>
        <?php
            endif;
        endforeach;
        ?>
        </select>

        <div class="col-12 row text-muted small m-0">
            <span class="ml-auto mr-0">Change content type (rarely used, use with caution)</span>
        </div>
    </div>
<?php
elseif ($types[$type]['type'] == 'user'):

    if ($types['webapp']['allow_role_change'] ?? false) { ?>
    <div class="form-group mt-5">
        <select class="form-control pl-0 border-top-0 border-left-0 border-right-0 rounded-0 mt-1" id="select_type"
            name="role_slug">
            <?php foreach ($types['user']['roles'] as $key => $value): ?>
            <option value="<?=$key?>" <?= ($key === $post['role_slug'] || $key === $_GET['role']) ? 'selected="selected"' : '' ?>>
                <?=ucfirst($value['title'])?>
            </option>
            <?php endforeach; ?>
        </select>

        <div class="col-12 row text-muted small m-0">
            <span class="ml-auto mr-0">Change user role (rarely used, use with caution)</span>
        </div>
    </div>
    <?php
    }
    else {
        if ($role['slug']):
    ?>
    <input type="hidden" name="role_slug" value="<?=$role['slug']?>">
    <?php elseif ($post['role_slug']): ?>
    <input type="hidden" name="role_slug" value="<?=$post['role_slug']?>">
    <?php
        endif;
    }
    ?>

    <input type="hidden" name="type" value="user">

<?php
else:
?>
    <input type="hidden" name="type" value="<?=$types[$type]['slug']?>">
<?php
endif
?>

<?php if ($types[$type]['type'] == 'content'): ?>
    <input type="hidden" name="user_id" value="<?=$post['user_id'] ?? $currentUser['user_id']?>">
<?php endif?>

<input type="hidden" name="function" value="push_content">
<input type="hidden" name="id" value="<?=$_GET['id'] ?? ''?>">
<input type="hidden" name="slug" value="<?=$post['slug'] ?? ''?>">

<?php
if ($post) {
    $ignored_keys = \Wildfire\Core\Dash::$ignored_keys;

    foreach ($post as $key => $value) {
        $modules = array_column($types[$type]['modules'], 'input_slug');

        if (!in_array($key, $modules) && !in_array($key, $ignored_keys)) {
            echo "<input type='hidden' name='{$key}' value='{$value}'>";
        }
    }
}
?>

<?php require_once __DIR__."/../includes/_display_log.php"; ?>
</form>

<!-- delete modal -->
<div class="modal fade" id="delete_conf_<?=$_GET['id'] ?? ''?>" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <span>Are you sure you wish to delete this content?</span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" onclick="deleteRecord(event)" class="btn btn-danger">Yes, delete it</button>
            </div>
        </div>
    </div>
</div>

<!-- toast for successful copy -->
<div id="toast-success" class="admin-toast toast position-fixed bg-dark text-white" role="alert" aria-live="assertive" aria-atomic="true" data-delay="1000">
    <div class="toast-body">
        <span>Copied</span>
        <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</div>
<!-- toast for successful save -->
<div id="save-success" class="admin-toast toast position-fixed bg-dark text-white" role="alert" aria-live="assertive" aria-atomic="true" data-delay="4000">
    <div class="toast-body">
        <span>Saved successfully</span>
        <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
</div>

<!-- js for typeout -->
<script type="text/javascript" src="/vendor/wildfire/admin/theme/assets/plugins/typeout/typeout.js"></script>

<?php
if (($_GET['edit_form'] ?? null) == 'true') {
    require_once __DIR__ .'/../includes/_footer.php';
}
