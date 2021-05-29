<?php include_once __DIR__ . '/header.php';?>

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
	    <p class="card-text">On the front-end key-value pairs can be accessed from $dash->get_content($id). Usually used to save website metadata.</p>
	    <form id="key_value_pair_edit_form" method="post" class="edit_form" action="/admin/json" autocomplete="off">
    	    <input type="text" class="form-control" name="meta_key" placeholder="Key">
    	    <input type="text" class="form-control" name="meta_value" placeholder="Value">
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
		      <th scope="col">Key</th>
		      <th scope="col">Value</th>
		      <th scope="col">updated_on</th>
		    </tr>
		  </thead>
		  <tbody>
		    <?php
$ids = $dash->get_all_ids('key_value_pair');
foreach ($ids as $idr) {
    $pair = $dash->get_content($idr['id']);
    echo '<tr><th scope="row">' . $pair['id'] . '</th><td>' . $pair['meta_key'] . '</td><td>' . $pair['meta_value'] . '</td><td>' . date('Y-m-d', $pair['created_on']) . '</td></tr>';
}
?>
		  </tbody>
		</table>
	  </div>
	</div>
	<div class="card my-2">
	  <div class="card-header">API keys</div>
	  <div class="card-body">
	    <p class="card-text">The API secret is displayed only once, when the API key is created.</p>
	    <form id="api_key_secret_edit_form" method="post" class="edit_form" action="/admin/json" autocomplete="off">
    	    <input type="text" class="form-control" name="api_key" placeholder="API Key" value="<?=uniqid()?>">
    	    <input type="text" class="form-control" name="api_secret" placeholder="API Secret" value="<?=$api->guidv4()?>">
	    	<input type="hidden" name="class" value="dash">
	    	<input type="hidden" name="type" value="api_key_secret">
	    	<input type="hidden" name="function" value="push_content">
	    	<input type="hidden" name="user_id" value="<?=$currentUser['user_id']?>">
	    	<input type="hidden" name="content_privacy" value="public">
	    	<button type="submit" class="btn btn-outline-primary border-top-0 border-left-0 border-right-0 rounded-0 save_btn" data-form-id="api_key_secret_edit_form"><span class="fa fa-save"></span>&nbsp;Save</button>
	    </form>
	  </div>
	</div>
</div>

<?php include_once __DIR__ . '/footer.php';?>
