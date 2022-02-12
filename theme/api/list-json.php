<?php
require_once __DIR__ . '/../_init.php';

$dash = new \Wildfire\Core\Dash;
$sql = new \Wildfire\Core\MySQL;
$api = new \Wildfire\Api;

$i = 0;
$or=array();

$_type = $_GET['type'];
$_role = $_GET['role'];

// count number of records, if number of records are more than 25k, use ajax method with datatables
if ($_type == 'user') {
    $unfiltered_ids_number = $sql->executeSQL("SELECT COUNT(`id`) AS `total` FROM `data` WHERE `type`='$_type' AND `role_slug`='$_role'")[0]['total'];
} else {
    $unfiltered_ids_number = $sql->executeSQL("SELECT COUNT(`id`) AS `total` FROM `data` WHERE `type`='$_type'")[0]['total'];
}

if ($unfiltered_ids_number>25000) {
    //load the search query
    $_search_query = strtolower($_GET['search']['value']);

    // loading all list-able columns for the type
    foreach ($types[$_type]['modules'] as $module)  { 
        if (isset($module['list_field'])) {
            $columns[]=$module['input_slug'];
        }

        //searchable columns, marry it to search query - SQL
        if (isset($module['list_searchable']) && trim($_search_query)) {
            $columns[]=$module['input_slug'];
            $_search_sql_query[] = "LOWER(`content`->>'$.".$module['input_slug']."') LIKE '%{$_search_query}%'";
        }
    }
}

//remaining parts of the SQL statement
$_search_length = $_GET['length'] ?? 50;
$_search_start = $_GET['start'] ?? 0;
$_search_direction = $_GET['order'][0]['dir'] ?? 'DESC';
$_search_column = ($_GET['order'][0]['column'] ? $columns[$_GET['order'][0]['column']] : 'id');

if ($api->method('get')) {
    if ($types['user']['roles_restricted_within_matching_modules'] ?? false) {
        $user_restricted_to_input_modules = array_intersect(array_keys($currentUser), array_keys($types));
    }

    if ($_type=='user')
        $search_array = ['type' => $_type, 'role_slug' => $_role];
    else
        $search_array = ['type' => $_type];

    if ($unfiltered_ids_number>25000) {
        $filterable_ids_number = $sql->executeSQL("SELECT COUNT(`id`) AS `total` FROM `data` WHERE `type`='{$_type}' ".($_type=='user' ? "AND `role_slug`='{$_role}'" : "")." ".( trim($_search_query) ? "AND (".implode(' OR ', $_search_sql_query).")" : "" )." ORDER BY `{$_search_column}` {$_search_direction}")[0]['total'];

        $ids = $sql->executeSQL("SELECT `id` FROM `data` WHERE `type`='{$_type}' ".($_type=='user' ? "AND `role_slug`='{$_role}'" : "")." ".( trim($_search_query) ? "AND (".implode(' OR ', $_search_sql_query).")" : "" )." ORDER BY `{$_search_column}` {$_search_direction} LIMIT {$_search_start}, {$_search_length}");

        $or = [
            'draw' => $_GET['draw'],
            'recordsTotal' => $unfiltered_ids_number,
            'recordsFiltered' => $filterable_ids_number
        ];
    }

    else {
        $filterable_ids_number = $unfiltered_ids_number;
        $ids = $dash->get_ids($search_array, '=', 'AND', $_search_column,  $_search_direction);

        $or = [
            'draw' => 1,
            'recordsTotal' => $unfiltered_ids_number,
            'recordsFiltered' => $unfiltered_ids_number
        ];
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
            $_editBtn = "<a class='badge badge-sm border border-dark font-weight-bold text-uppercase' title='Click here to edit' href='/admin/edit?type={$post['type']}&id={$post['id']}{$_editRole}'><i class='fal fa-edit'></i>&nbsp;Edit</a>";
        }

        $_viewBtn = "<a title='Click here to view this record' class='badge badge-sm border border-dark font-weight-bold text-uppercase' target='new' href='/{$post['type']}/{$post['slug']}'><i class='fal fa-external-link-alt'></i>&nbsp;View</a>";

        $_contentPrivacy = "<span class='badge badge-sm border border-dark font-weight-bold text-uppercase' title='Content privacy set to ".(isset($_object['content_privacy']) ?? '')."'><span class='fal fa-".(
                $_object['type'] == 'user' ? 'user' : (
                    $_object['content_privacy'] == 'public' ? 'megaphone' : (
                        $_object['content_privacy'] == 'private' ? 'link' : (
                            $_object['content_privacy'] == 'pending' ? 'hourglass-half' : 'paragraph'
                        )
                    )
                )
            )."'></span> ".( 
                $_object['type'] == 'user' ? 'user' : 
                ($_object['content_privacy'] ?? "draft")
            )."</span>";

        $_slugLine = '<span class="d-none d-md-inline-block" data-toggle="tooltip" data-placement="bottom" title="'.$post['slug'].'"><span class="ml-1 small text-muted slug-ellipsis">'.$post['slug'].'</span></span>';

        // button controls for this single post
        $or['data'][$i][] = '<span>'.$post['id'].'</span>';

        $donotlist = 0;
        foreach ($types[$_type]['modules'] as $module) {
            // skip if 'list_field' is set to false on module
            if (!($module['list_field'] ?? false)) {
                continue;
            }

            $_template[] = "";

            $module_input_slug_lang = $module['input_slug'] . (is_array($module['input_lang'] ?? null) ? "_{$module['input_lang'][0]['slug']}" : '');

            if ($_object[$module_input_slug_lang]) {

                if (is_array($_object[$module_input_slug_lang])) {

                    $the_module_texts=array();

                    if ($module['list_linked_module'] ?? false) {

                        foreach ($_object[$module_input_slug_lang] as $obj_value) {

                            $pointerSpan = '<span 
                                data-linked_type="'.$module['list_linked_module']['linked_type'].'" 
                                data-linked_slug="'.$obj_value.'" 
                                data-linked_display_module="'.$module['list_linked_module']['display_module'].'" 
                                tabindex="0" 
                                data-container="body" 
                                data-toggle="popover" 
                                data-trigger="hover" 
                                data-placement="bottom" 
                                data-content="'.$obj_value.'"
                            >';

                            $the_module_texts[] = $pointerSpan.$obj_value.'</span>';
                        }

                        $the_module_text = implode(', ', $the_module_texts);
                    }
                    else {
                        $the_module_text = implode(', ', $_object[$module_input_slug_lang]);
                    }

                } else {

                    if ($module['list_linked_module'] ?? false) {
                        $pointerSpan = '<span 
                            data-linked_type="'.$module['list_linked_module']['linked_type'].'" 
                            data-linked_slug="'.$_object[$module_input_slug_lang].'" 
                            data-linked_display_module="'.$module['list_linked_module']['display_module'].'" 
                            tabindex="0" 
                            data-container="body" 
                            data-toggle="popover" 
                            data-trigger="hover" 
                            data-placement="bottom" 
                            data-content="'.$_object[$module_input_slug_lang].'"
                        >';
                    } else {
                        $pointerSpan = '<span 
                            data-toggle="tooltip" 
                            data-placement="bottom" 
                            title="'.$_object[$module_input_slug_lang].'"
                        >';
                    }

                    $the_module_text = $pointerSpan.$_object[$module_input_slug_lang].'</span>';
                }

            } else {
                $the_module_text = '';
            }

            $cont = '<span class="text-ellipsis record_module">'.$the_module_text.'</span>';
                    

            if (isset($module['input_primary']) ?? false) {
                $cont .= "<div class='w-100 small'>
                            <span class='btn-options float-left'>
                                {$_contentPrivacy} {$_viewCount} {$_slugLine}
                            </span>
                            <span class='btn-options float-left float-md-right'>
                                {$_editBtn} {$_viewBtn}
                            </span>
                        </div>";
            }

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