<?php
require_once __DIR__ . '/../init.php';

$dash = new \Wildfire\Core\Dash;
$sql = new \Wildfire\Core\MySQL;
$api = new \Wildfire\Api;

$i = 0;
$or=array();

$_type = $_GET['type'];
$_role = $_GET['role'];

if ($types['user']['roles_restricted_within_matching_modules'] ?? false) {
    $user_restricted_to_input_modules = array_intersect(array_keys($currentUser), array_keys($types));
}

if ($_type == 'user') {
    $ids = $dash->get_all_ids(['type' => $_type, 'role_slug' => $_role]);
} else if ($types[$_type]['type']=='user') {
    $ids = $dash->get_all_ids(['type' => 'user', 'role_slug' => $_type]);
} else {
    $ids = $dash->get_all_ids($_type);
}

$_dbObjects = $dash->getObjects($ids);

foreach ($_dbObjects as $_object) {
    if (
        ($types['user']['roles_restricted_within_matching_modules'] ?? false) &&
        !$admin->is_access_allowed($_object['id'], $user_restricted_to_input_modules)
    ) {
        continue;
    }

    $post = array();
    $post['id'] = $_object['id'];
    $post['type'] = $_type;
    $post['slug'] = $_object['slug'];

    $or['data'][$i][] = $post['id'];

    $donotlist = 0;
    foreach ($types[$_type]['modules'] as $module) {
        // skip if 'list_field' is set to false on module
        if (!($module['list_field'] ?? false)) {
            continue;
        }

        $module_input_slug_lang = $module['input_slug'] . (is_array($module['input_lang'] ?? null) ? "_{$module['input_lang'][0]['slug']}" : '');

        $cont = $_object[$module_input_slug_lang] ?? '';

        //For displaying list_linked_module
        if ($module['list_linked_module'] ?? false) {
            $cont_json_decoded = json_decode($cont, true);

            if (is_array($cont_json_decoded)) {
                foreach ($cont_json_decoded as $cont_json) {
                    $cont_json_decoded_arr[]=$dash->getAttribute(array('type'=>$module['list_linked_module']['linked_type'], 'slug'=>$cont_json), $module['list_linked_module']['display_module']);
                }

                $cont = implode(', ', $cont_json_decoded_arr);
            } else {
                $cont = $dash->getAttribute(array('type'=>$module['list_linked_module']['linked_type'], 'slug'=>$cont), $module['list_linked_module']['display_module']);
            }
        }

        if (($module['list_non_empty_only'] ?? false) && !trim($cont)) {
            $donotlist = 1;
        } else {
            $or['data'][$i][] = trim($cont);
        }
    }

    $_viewCount = '';
    if ($types[$_type]['display_prism_stat'] ?? false) {
        $_viewCount = $sql->executeSQL("select visit->>'$.url' as url, count(*) as count from trac where visit->'$.url' like '%{$_object['slug']}%' group by url order by count desc")[0]['count'] ?? 0;
        $_viewCount = "<span class='text-muted small mr-1' title='Visits'>{$_viewCount}</span>";
    }

    // edit and view buttons
    $_editBtn = '';
    if ($currentUser['role'] == 'admin' || $currentUser['user_id'] == $_object['user_id']) {
        $_editRole = $_type == 'user' ? '&role=' . $_role : '';
        $_editBtn = "<a class='mr-1' title='Edit' href='/admin/edit?type={$post['type']}&id={$post['id']}{$_editRole}'><i class='fas fa-edit'></i></a>";
    }

    $_viewBtn = "<a title='View' class='mr-2' target='new' href='/{$post['type']}/{$post['slug']}'><i class='fas fa-external-link-alt'></i></a>";

    // button controls for this single post
    $or['data'][$i][] = "<span class='d-flex align-items-center justify-content-end'>{$_viewCount} {$_editBtn} {$_viewBtn}</span>";

    if ($donotlist) {
        $or['data'][$i]=array();
        $i--;
    }

    $i++;
}

if ($or['data'])
    $or['data']=array_values($or['data']);
else {
    $or['data'][$i][0]='';
    $or['data'][$i][1]='No data in this yet.';
}

$api->json($or)->send();
