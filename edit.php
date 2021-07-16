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
		$role = $types['user']['roles'][$_GET[role]];
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

								            <?php if ($role['slug']): ?>
								                <input type="hidden" name="role_slug" value="<?=$role['slug']?>">
								            <?php elseif ($post['role_slug']): ?>
                <input type="hidden" name="role_slug" value="<?=$post['role_slug']?>">
            <?php endif?>

            <?php
if (
	($types['webapp']['allow_type_change'] ?? false) &&
	($types[$type]['type'] == 'content')
):
?>
                <div class="form-group mt-5">
                    <select
                        class="form-control pl-0 border-top-0 border-left-0 border-right-0 rounded-0 mt-1"
                        id="select_type"
                        name="type"
                    >
                    <?php
if (!($post_type = $post['type'])) {
	$post_type = $_GET['type'];
}
?>
                    <?php foreach ($types as $key => $value): ?>
                        <?php if ($types[$key]['type'] == 'content'): ?>
                            <option
                                value="<?=$types[$key]['slug']?>"
                                <?=$types[$key]['slug'] === $post_type ? 'selected="selected"' : ''?>
                            >
                                <?=ucfirst($types[$key]['name'])?>
                            </option>
                        <?php endif?>
                    <?php endforeach?>
                    </select>

                    <div class="col-12 row text-muted small m-0">
                        <span class="ml-auto mr-0">Change content type (rarely used, use with caution)</span>
                    </div>
                </div>
            <?php else: ?>
                <input type="hidden" name="type" value="<?=$types[$type]['slug']?>">
            <?php endif?>

            <?php if ($types[$type]['type'] == 'content'): ?>
                <input
                    type="hidden"
                    name="user_id"
                    value="<?=$post['user_id'] ?: $currentUser['user_id']?>"
                >
            <?php endif?>

            <input type="hidden" name="function" value="push_content">
            <input type="hidden" name="id" value="<?=$_GET['id']?>">
            <input type="hidden" name="slug" value="<?=$post['slug']?>">

            <?php
if ($post) {
	foreach ($post as $key => $value) {
		$modules = array_column($types[$type]['modules'], 'input_slug');
		if (!in_array($key, $modules) && $key != 'type' && $key != 'function' && $key != 'class' && $key != 'slug' && $key != 'id' && $key != 'updated_on' && $key != 'created_on') {
			echo '<input type="hidden" name="' . $key . '" value="' . $value . '">';
		}
	}
}
?>

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
