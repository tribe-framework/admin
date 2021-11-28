<?php require_once __DIR__ . '/includes/_header.php';?>

<?php
/**
 * this code is responsible for editing and modifying content in admin dashboard
 * basically the form that the admin uses
 */
?>

<?php
$role = null;

if (isset($_GET['id'])) {
	$post = $dash->get_content($_GET['id']);
}

if (
	!(
		$currentUser['role'] == 'admin' ||
		$post['user_id'] == $currentUser['user_id'] ||
		!$_GET['id']
	)
):
?>
Not allowed. <a href="/admin/">Go back</a>
<?php
else:
	if (isset($_GET['role'])) {
		$role = $types['user']['roles'][$_GET['role']];
	}

	if ((isset($_GET['id']) && $post['type'] == $type) || !isset($_GET['id'])):
		//for testing resticted min and max ids for archive format changes
		if (isset($_GET['id']) && !($pid = $_GET['id'])) {
			$pid = $dash->get_next_id();
		}

		?>

<link rel="stylesheet" type="text/css" href="<?=ADMIN_URL?>/plugins/typeout/typeout.css">

<div class="popup-banner">
    <a name="infos"></a>
    <div id="infos" class="d-none alert alert-success shadow shadow-sm mb-0">
        <div class="progress"></div>
        <span class="text"></span>
    </div>

    <a name="errors"></a>
    <div id="errors" class="d-none alert alert-danger shadow shadow-sm mb-0">
        <div class="progress"></div>
        <span class="text"></span>
    </div>
</div>


<form method="post" class="edit_form" action="/admin/json" autocomplete="off">
    <?=
		$admin->get_admin_menu(
			$types[$type]['disallow_editing'] ?
			'view' :
			'edit',
			$type,
			$role['slug'] ?? '',
			$_GET['id'] ?? ''
		);
		?>

    <h2 class="form_title">
        <?php if ($type === 'user'): ?>
        <?=$role['title']?>&nbsp;<small><span class="fas fa-angle-double-right"></span></small>&nbsp;
        <?php endif;?>
        Edit <?=($types[$type]['name'] ?? $type)?><?=($post['id'] ? ' / ID: ' . $post['id'] : ' / New')?>
    </h2>

    <div class="form-style">
        <?php include __DIR__ . '/form.php';?>
    </div>

    <input type="hidden" name="class" value="dash">

    <?php
if (
	($types['webapp']['allow_type_change'] ?? false) &&
	($types[$type]['type'] == 'content')
):
?>
    <div class="form-group mt-5">
        <select class="form-control pl-0 border-top-0 border-left-0 border-right-0 rounded-0 mt-1" id="select_type"
            name="type">
            <?php
if (!($post_type = $post['type'])) {
	$post_type = $_GET['type'];
}
?>
            <?php foreach ($types as $key => $value): ?>
            <?php if ($types[$key]['type'] == 'content'): ?>
            <option value="<?=$types[$key]['slug']?>"
                <?=$types[$key]['slug'] === $post_type ? 'selected="selected"' : ''?>>
                <?=ucfirst($types[$key]['name'])?>
            </option>
            <?php endif?>
            <?php endforeach?>
        </select>

        <div class="col-12 row text-muted small m-0">
            <span class="ml-auto mr-0">Change content type (rarely used, use with caution)</span>
        </div>
    </div>
    <?php elseif ($types[$type]['type'] == 'user'): ?>

    <?php if ($types['webapp']['allow_role_change'] ?? false) { ?>
    <div class="form-group mt-5">
        <select class="form-control pl-0 border-top-0 border-left-0 border-right-0 rounded-0 mt-1" id="select_type"
            name="role_slug">
            <?php foreach ($types['user']['roles'] as $key => $value): ?>
            <option value="<?=$key?>" <?=$key === $post['role_slug'] ? 'selected="selected"' : ''?>>
                <?=ucfirst($value['title'])?>
            </option>
            <?php endforeach?>
        </select>

        <div class="col-12 row text-muted small m-0">
            <span class="ml-auto mr-0">Change user role (rarely used, use with caution)</span>
        </div>
    </div>
    <?php } else { ?>
    <?php if ($role['slug']): ?>
    <input type="hidden" name="role_slug" value="<?=$role['slug']?>">
    <?php elseif ($post['role_slug']): ?>
    <input type="hidden" name="role_slug" value="<?=$post['role_slug']?>">
    <?php endif?>
    <?php } ?>

    <input type="hidden" name="type" value="user">

    <?php else: ?>
    <input type="hidden" name="type" value="<?=$types[$type]['slug']?>">
    <?php endif?>

    <?php if ($types[$type]['type'] == 'content'): ?>
    <input type="hidden" name="user_id" value="<?=$post['user_id'] ?: $currentUser['user_id']?>">
    <?php endif?>

    <input type="hidden" name="function" value="push_content">
    <input type="hidden" name="id" value="<?=$_GET['id']?>">
    <input type="hidden" name="slug" value="<?=$post['slug']?>">

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

    <?php if ($types['webapp']['display_activity_log']): ?>
    <div class="form-group">
        <button class="btn btn-light w-100 text-left d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#mysql_log"
            aria-expanded="false" aria-controls="mysql_log">
            <span><i class="fas fa-clipboard-list mr-3"></i>Log</span>
            <i class="fas fa-chevron-circle-down"></i>
        </button>
        <div class="collapse border border-light overflow-auto" id="mysql_log">
            <div class="container">
                <?php if (count($post['mysql_activity_log'] ?? [])): ?>
                    <?php
                        $access_log = \array_reverse($post['mysql_activity_log'], true);
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

    <?php
    if (count($types[$type]['modules']) > 3) {
        echo $admin->get_admin_menu(
            ($types[$type]['disallow_editing'] ? 'view' : 'edit'),
            $type,
            $role['slug'],
            $_GET['id']
        );
    }
    ?>
    <p>&nbsp;</p>
</form>

<div class="modal fade" id="delete_conf_<?=$_GET['id']?>" tabindex="-1" role="dialog">
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
                <form method="post" class="edit_form" action="/admin/json">
                    <input type="hidden" name="class" value="dash">
                    <input type="hidden" name="function" value="do_delete">
                    <input type="hidden" name="type" value="<?=$type?>">
                    <input type="hidden" name="id" value="<?=$_GET['id']?>">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Yes, delete it</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php endif?>
<?php endif?>

<?php require_once __DIR__ . '/includes/_footer.php';?>
