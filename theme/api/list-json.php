<?php
require_once __DIR__ . '/../_init.php';

$dash = new \Wildfire\Core\Dash;
$sql = new \Wildfire\Core\MySQL;
$api = new \Wildfire\Api;

$i = 0;
$or=array();

$_type = $_GET['type'];
$_role = $_GET['role'];

if ($api->method('get')) {
    if ($types['user']['roles_restricted_within_matching_modules'] ?? false) {
        $user_restricted_to_input_modules = array_intersect(array_keys($currentUser), array_keys($types));
    }

    if ($_type == 'user') {
        $ids = $dash->get_all_ids(['type' => $_type, 'role_slug' => $_role]);
    } elseif ($types[$_type]['type']=='user') {
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

        $_viewCount = '';
        if ($types[$_type]['display_prism_stat'] ?? false) {
            $_viewCount = $sql->executeSQL("select visit->>'$.url' as url, count(*) as count from trac where visit->'$.url' like '%{$_object['slug']}%' group by url order by count desc")[0]['count'] ?? 0;
            $_viewCount = "<span class='text-muted small mx-1' title='Visits'>{$_viewCount}</span>";
        }

        // edit and view buttons
        $_editBtn = '';
        if ($currentUser['role'] == 'admin' || $currentUser['user_id'] == $_object['user_id']) {
            $_editRole = $_type == 'user' ? '&role=' . $_role : '';
            $_editBtn = "<a class='badge badge-sm border border-dark font-weight-bold text-uppercase' title='Edit' href='/admin/edit?type={$post['type']}&id={$post['id']}{$_editRole}'><i class='fal fa-edit'></i>&nbsp;Edit</a>";
        }

        $_viewBtn = "<a title='View' class='badge badge-sm border border-dark font-weight-bold text-uppercase' target='new' href='/{$post['type']}/{$post['slug']}'><i class='fal fa-external-link-alt'></i>&nbsp;View</a>";

        $_contentPrivacy = "<span class='badge badge-sm border border-dark font-weight-bold text-uppercase'><span class='fal fa-megaphone'></span> ".$_object['content_privacy']."</span>";

        // button controls for this single post
        $or['data'][$i][] = $post['id'];

        $donotlist = 0;
        foreach ($types[$_type]['modules'] as $module) {
            // skip if 'list_field' is set to false on module
            if (!($module['list_field'] ?? false)) {
                continue;
            }

            $_template[] = "";

            $module_input_slug_lang = $module['input_slug'] . (is_array($module['input_lang'] ?? null) ? "_{$module['input_lang'][0]['slug']}" : '');

            $cont = '<span class="text-ellipsis">'.($_object[$module_input_slug_lang] ? (is_array($_object[$module_input_slug_lang]) ? implode(', ', $_object[$module_input_slug_lang]) : $_object[$module_input_slug_lang]) : '').'</span>';

            //For displaying list_linked_module
            if ($module['list_linked_module'] ?? false) {
                $cont_json_decoded = json_decode($cont, true);

                if (is_array($cont_json_decoded)) {
                    foreach ($cont_json_decoded as $cont_json) {
                        $cont_json_decoded_arr[]=$dash->getAttribute(array('type'=>$module['list_linked_module']['linked_type'], 'slug'=>$cont_json), $module['list_linked_module']['display_module']);
                    }

                    $cont = '<span class="text-ellipsis">'.implode(', ', $cont_json_decoded_arr).'</span>';
                } else {
                    $cont = '<span class="text-ellipsis">'.$dash->getAttribute(array('type'=>$module['list_linked_module']['linked_type'], 'slug'=>$cont), $module['list_linked_module']['display_module']).'</span>';
                }
            }

            if (isset($module['input_primary']) ?? false)
                $cont .= "<div class='w-100 small'>{$_contentPrivacy} {$_viewCount} <span class='btn-options float-right'>{$_editBtn} {$_viewBtn}</span></div>";

            if (($module['list_non_empty_only'] ?? false) && !trim($cont)) {
                $donotlist = 1;
            } else {
                $or['data'][$i][] = is_array($cont) ? $cont : trim($cont);
            }
        }

        if ($donotlist) {
            $or['data'][$i]=array();
            $i--;
        }

        $i++;
    }

    if (!$or['data']) {
        $or = [
            'draw' => 0,
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => []
        ];
    }

    $api->json($or)->send();
}
