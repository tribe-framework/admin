<?php
require_once __DIR__ . '/includes/init.php';

$i = 0;
$or=array();
header('Content-Type: application/json');

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
    //$post = $dash->get_content($arr['id']);

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
    $post['slug'] = $dash->get_content_meta($post['id'], 'slug');

    $donotlist = 0;
    foreach ($types[$this_type]['modules'] as $module) {
        if (isset($module['list_field']) && $module['list_field']) {
            $module_input_slug_lang = $module['input_slug'] . (is_array($module['input_lang']) ? '_' . $module['input_lang'][0]['slug'] : '');
            $cont = $dash->get_content_meta($post['id'], $module_input_slug_lang);
            
            if ($module['list_non_empty_only'] && !trim($cont)) {
                $donotlist = 1;
            }
        }
    }

    if (!$donotlist) {
        $or['data'][$i][] = $post['id'];
        $or['data'][$i][] = $cont;

        // edit and view buttons
        $or['data'][$i][] = '<span class="d-flex">' . (($currentUser['role'] == 'admin' || $currentUser['user_id'] == $dash->get_content_meta($post['id'], 'user_id')) ? '<a class="mr-1" title="Edit" href="/admin/edit?type=' . $post['type'] . '&id=' . $post['id'] . ($this_type == 'user' ? '&role=' . $this_role : '') . '"><i class="fas fa-edit"></i></a>&nbsp;' : '') . '<a title="View" target="new" href="/' . $post['type'] . '/' . $post['slug'] . '"><i class="fas fa-external-link-alt"></i></a></span>';
    }

    $i++;
}

$or['data']=array_values($or['data']);
echo json_encode($or);
?>