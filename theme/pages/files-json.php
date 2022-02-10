<?php
require_once __DIR__ . '/../_init.php';

set_time_limit(600);

$i = 0;
$or=array();
header('Content-Type: application/json');

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
    $or['data'][$i][] = $post['id'];
    $or['data'][$i][] = (exif_imagetype($xs_file)?'<img loading="lazy" src="'.$xs_file.'" width="80">':'<span class="fa-3x fas fa-file-alt"></span>');
    $or['data'][$i][] = basename($post['file']) . '<br>used in <a href="/'.$post['type'].'/'.$post['slug'].'" target="new">'.$post['title'].'&nbsp;<span class="fas fa-external-link-alt"></span></a>';
    $or['data'][$i][] = '<span class="copy_btn btn btn-sm btn-outline-primary px-3 text-capitalize" data-clipboard-text="[['.$post['file'].']]"><span class="fas fa-copy mr-1"></span>copy shortcode</span>';
    $or['data'][$i][] = '<a href="'.$post['file'].'" target="new"><span class="fas fa-2x fa-external-link-square-alt"></span></a>';

    $i++;
}

$or['data']=array_values($or['data']);
echo json_encode($or);
?>