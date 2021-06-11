<?php require_once __DIR__ . '/includes/_header.php';?>

<?php
use Wildfire\Api\Api as Api;
$api = new Api();
?>

<?php echo $admin->get_admin_menu('dash'); ?>

<div class="container">
    <a name="infos"></a>
    <div id="infos" class="d-none alert alert-success"></div>

    <a name="errors"></a>
    <div id="errors" class="d-none alert alert-danger"></div>
</div>

<div class="card-group m-0">
	<div class="card my-2">
	  <div class="card-header">Key-value pairs</div>
	  <div class="card-body">
	    <p class="card-text">Key-value pairs for quickly saving meta-data to be used in front-end theme, editable from the admin. <strong>These are publicly viewable values.</strong> Do not store passwords.</p>
	    <form id="key_value_pair_edit_form" method="post" class="edit_form" action="/admin/json" autocomplete="off" data-redirect-on-save="/admin/meta">
    	    <input type="text" class="form-control" name="meta_key" placeholder="Key">
    	    <input type="text" class="form-control" name="meta_value" placeholder="Value">
    	    <input type="text" class="form-control" name="title" placeholder="Remarks">
	    	<input type="hidden" name="class" value="dash">
	    	<input type="hidden" name="type" value="key_value_pair">
	    	<input type="hidden" name="function" value="push_content">
	    	<input type="hidden" name="user_id" value="<?=$currentUser['user_id']?>">
	    	<input type="hidden" name="content_privacy" value="public">
	    	<button type="submit" class="btn btn-outline-primary border-top-0 border-left-0 border-right-0 rounded-0 save_btn" data-form-id="key_value_pair_edit_form"><span class="fa fa-save"></span>&nbsp;Save</button>
	    </form>
	    <table class="table mt-5">
		<thead>
		    <tr>
		      <th scope="col">#</th>
		      <th scope="col">Remarks</th>
		      <th scope="col">Key</th>
		      <th scope="col">Value</th>
		      <th scope="col">Created</th>
		      <th scope="col"></th>
		    </tr>
		  </thead>
		  <tbody>
		    <?php
$ids = $dash->get_all_ids('key_value_pair');
foreach ($ids as $idr) {
    $pair = $dash->get_content($idr['id']);
    echo '<tr><th scope="row">' . $pair['id'] . '</th><td>' . ($pair['title'] ?? '<em>&lt;untitled&gt;</em>') . '<small><br><pre>$dash->get_content_meta(' . $pair['id'] . ', \'meta_value\')</pre></small></td><td>' . $pair['meta_key'] . '</td><td>' . $pair['meta_value'] . '</td><td>' . date('Y-m-d', $pair['created_on']) . '</td><td>' . (($currentUser['role'] == 'admin' || $currentUser['user_id'] == $dash->get_content_meta($pair['id'], 'user_id')) ? '<a href="/admin/edit?type=' . $pair['type'] . '&id=' . $pair['id'] . ($type == 'user' ? '&role=' . $_GET['role'] : '') . '"><span class="fas fa-edit"></span></a>' : '') . '</td></tr>';
}
?>
		  </tbody>
		</table>
	  </div>
	</div>
</div>

<div class="card-group m-0">
	<div class="card my-2">
	  <div class="card-header">API keys</div>
	  <div class="card-body">
	    <p class="card-text">Auto-generated API key-secret pairs. Make sure you enter remarks to be able to place why you created them.</p>
	    <form id="api_key_secret_edit_form" method="post" class="edit_form" action="/admin/json" autocomplete="off" data-redirect-on-save="/admin/meta">
    	    <input type="text" class="form-control" name="api_key" placeholder="API Key" value="<?=uniqid()?>" readonly>
    	    <input type="text" class="form-control" name="api_secret" placeholder="Secret" value="<?=$api->guidv4()?>" readonly>
    	    <input type="text" class="form-control" name="title" placeholder="Remarks">
	    	<input type="hidden" name="class" value="dash">
	    	<input type="hidden" name="type" value="api_key_secret">
	    	<input type="hidden" name="function" value="push_content">
	    	<input type="hidden" name="user_id" value="<?=$currentUser['user_id']?>">
	    	<input type="hidden" name="content_privacy" value="private">
	    	<button type="submit" class="btn btn-outline-primary border-top-0 border-left-0 border-right-0 rounded-0 save_btn" data-form-id="api_key_secret_edit_form"><span class="fa fa-save"></span>&nbsp;Save</button>
	    </form>
	    <table class="table mt-5">
		<thead>
		    <tr>
		      <th scope="col">#</th>
		      <th scope="col">Remarks</th>
		      <th scope="col">API Key</th>
		      <th scope="col">Secret</th>
		      <th scope="col">Created</th>
		      <th scope="col"></th>
		    </tr>
		  </thead>
		  <tbody>
		    <?php
$ids = $dash->get_all_ids('api_key_secret');
foreach ($ids as $idr) {
    $pair = $dash->get_content($idr['id']);
    echo '<tr><th scope="row">' . $pair['id'] . '</th><td>' . ($pair['title'] ?? '<em>&lt;untitled&gt;</em>') . '</td><td>' . $pair['api_key'] . '</td><td>' . $pair['api_secret'] . '</td><td>' . date('Y-m-d', $pair['created_on']) . '</td><td>' . (($currentUser['role'] == 'admin' || $currentUser['user_id'] == $dash->get_content_meta($pair['id'], 'user_id')) ? '<a href="/admin/edit?type=' . $pair['type'] . '&id=' . $pair['id'] . ($type == 'user' ? '&role=' . $_GET['role'] : '') . '"><span class="fas fa-edit"></span></a>' : '') . '</td></tr>';
}
?>
		  </tbody>
		</table>
	  </div>
	</div>
</div>

<?php require_once __DIR__ . '/includes/_footer.php';?>
