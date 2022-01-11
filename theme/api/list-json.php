<?php
require_once __DIR__ . '/../init.php';

$i = 0;
$or=array();

$this_type = $_GET['type'];
$this_role = $_GET['role'];

if (
    isset($types['user']['roles_restricted_within_matching_modules']) &&
    $types['user']['roles_restricted_within_matching_modules']
) {
    $user_restricted_to_input_modules = array_intersect(array_keys($currentUser), array_keys($types));
}

if ($this_type == 'user') {
    $ids = $dash->get_all_ids(array('type' => $this_type, 'role_slug' => $this_role));
} else if ($types[$this_type]['type']=='user') {
    $ids = $dash->get_all_ids(array('type' => 'user', 'role_slug' => $this_type));
} else {
    $ids = $dash->get_all_ids($this_type);
}

foreach ($ids as $arr) {
    if (
        isset($types['user']['roles_restricted_within_matching_modules']) &&
        $types['user']['roles_restricted_within_matching_modules'] &&
        !$admin->is_access_allowed($arr['id'], $user_restricted_to_input_modules)
    ) {
        continue;
    }

    $post = array();
    $post['id'] = $arr['id'];
    $post['type'] = $this_type;
    $post['slug'] = $dash->getAttribute($post['id'], 'slug');

    $or['data'][$i][] = $post['id'];

    $donotlist = 0;
    foreach ($types[$this_type]['modules'] as $module) {
        if (isset($module['list_field']) && $module['list_field']) {
            $module_input_slug_lang = $module['input_slug'] . (isset($module['input_lang']) && is_array($module['input_lang']) ? "_{$module['input_lang'][0]['slug']}" : '');
            $cont = $dash->getAttribute($post['id'], $module_input_slug_lang);

            //For displaying list_linked_module
            if (isset($module['list_linked_module']) && $module['list_linked_module']) {
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

            if (isset($module['list_non_empty_only']) && $module['list_non_empty_only'] && !trim($cont)) {
                $donotlist = 1;
            } else {
                $or['data'][$i][] = $cont;
            }
        }
    }

    // edit and view buttons
    $or['data'][$i][] = '<span class="d-flex">' . (($currentUser['role'] == 'admin' || $currentUser['user_id'] == $dash->getAttribute($post['id'], 'user_id')) ? '<a class="mr-1" title="Edit" href="/admin/edit?type=' . $post['type'] . '&id=' . $post['id'] . ($this_type == 'user' ? '&role=' . $this_role : '') . '"><i class="fas fa-edit"></i></a>&nbsp;' : '') . '<a title="View" target="new" href="/' . $post['type'] . '/' . $post['slug'] . '"><i class="fas fa-external-link-alt"></i></a></span>';

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

$api = new \Wildfire\Api;
$api->json($or)->send();
?>