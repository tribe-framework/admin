<?php require_once __DIR__ . '/includes/_header.php';?>
<?php
/**
 * this code is responsible for listing the content in admin dash
 */
set_time_limit(600);
?>

<div class="p-3">
    <?php
if ($_GET['role']) {
    $role = $types['user']['roles'][$_GET[role]];
}

if (
    isset($types['user']['roles_restricted_within_matching_modules']) &&
    $types['user']['roles_restricted_within_matching_modules']
) {
    $user_restricted_to_input_modules = array_intersect(array_keys($currentUser), array_keys($types));
}

//echo $admin->get_admin_menu('list', $type, $role['slug']);
?>


    <h2 class="mb-4">
        List of Uploaded Files
    </h2>

    <table class="my-4 table table-borderless table-hover datatable">
        <thead class="thead-black">
            <tr>
                <th scope="col">#</th>
                <th scope="col" data-orderable="false" data-searchable="false"></th>
                <th scope="col" class="pl-2" data-orderable="true" data-searchable="true" style="max-width:50%">Filename</th>
                <th scope="col" data-orderable="false" data-searchable="false"></th>
                <th scope="col" data-orderable="false" data-searchable="false"></th>
            </tr>
        </thead>
        <tbody>
<?php
$sql = new Wildfire\Core\MySQL();
$dir   = new RecursiveDirectoryIterator(TRIBE_ROOT.'/uploads/');
$flat  = new RecursiveIteratorIterator($dir);
$flat->setMaxDepth(3);
$files = new RegexIterator($flat, UPLOAD_FILE_TYPES);
$posts=array();

$i=0;
foreach ($files as $file) {
    $i++;
    $filename=str_replace(TRIBE_ROOT, BASE_URL, $file);
    //$eval_sql_query.='$posts['.$filename.']=$sql->executeSQL();';
    $eval_sql[]="SELECT `id`, '".mysqli_real_escape_string($sql->databaseLink, $filename)."' AS `file`, `type`, `slug`, `content`->>'$.title' AS `title` FROM `data` WHERE `content` LIKE '%".mysqli_real_escape_string($sql->databaseLink, $filename)."%'";
}
$eval_sql_query=implode(' UNION ', $eval_sql);
$posts=$sql->executeSQL($eval_sql_query);

foreach ($posts as $post) {
    //$xs_file=$dash->get_uploaded_image_in_size($post['file'], 'xs')['url'];
    $xs_file=$post['file'];
    $tr_echo = '<tr><th scope="row">'.$post['id'].'</th><td>'.(exif_imagetype($xs_file)?'<img loading="lazy" src="'.$xs_file.'" width="80">':'<span class="fa-3x fas fa-file-alt"></span>').'</td><td>' . basename($post['file']) . '<br>used in <a href="/'.$post['type'].'/'.$post['slug'].'" target="new">'.$post['title'].'&nbsp;<span class="fas fa-external-link-alt"></span></a></td><td><span class="copy_btn btn btn-sm btn-outline-primary px-3 text-capitalize" data-clipboard-text="[['.$post['file'].']]"><span class="fas fa-copy mr-1"></span>copy shortcode</span></td><td><a href="'.$post['file'].'" target="new"><span class="fas fa-2x fa-external-link-square-alt"></span></a></td></tr>';
    echo $tr_echo;
}
?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/_footer.php';?>