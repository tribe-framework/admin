<?php
require_once __DIR__ . '/../_init.php';

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

// if number is 25k build SQL query to fetch what user has typed in filter search
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

//remaining parts of the SQL statement, params sent by datatables
$_search_length = $_GET['length'] ?? 50;
$_search_start = $_GET['start'] ?? 0;
$_search_direction = $_GET['order'][0]['dir'] ?? 'DESC';
$_search_column = ($_GET['order'][0]['column'] ? $columns[$_GET['order'][0]['column']] : 'id');

if ($api->method('get')) {
    if ($types['user']['roles_restricted_within_matching_modules'] ?? false) {
        $user_restricted_to_input_modules = array_intersect(array_keys($currentUser), array_keys($types));
    }

    //create search_var for $dash->get_all_ids
    //if type is user, use role as well
    if ($_type=='user')
        $search_var = ['type' => $_type, 'role_slug' => $_role];
    else
        $search_var = $_type;

    //if more than 25k records, use SQL directly
    if ($unfiltered_ids_number>25000) {

        //part of query that fetches the correct results, takes care of sort order
        $_final_query = "FROM `data` WHERE `type`='{$_type}' ".($_type=='user' ? "AND `role_slug`='{$_role}'" : "")." ".( trim($_search_query) ? "AND (".implode(' OR ', $_search_sql_query).")" : "" )." ORDER BY `{$_search_column}` {$_search_direction}";

        //count the records, with $_final_query
        $filterable_ids_number = $sql->executeSQL("SELECT COUNT(`id`) AS `total` {$_final_query}")[0]['total'];

        //get ids, with $_final_query
        $ids = $sql->executeSQL("SELECT `id` {$_final_query} LIMIT {$_search_start}, {$_search_length}");

        //count is important for datatables to function properly
        $or = [
            'draw' => $_GET['draw'],
            'recordsTotal' => $unfiltered_ids_number,
            'recordsFiltered' => $filterable_ids_number
        ];
    }
    //if less than 25k records, simply use $dash->get_all_ids
    else {
        $filterable_ids_number = $unfiltered_ids_number;
        $ids = $dash->get_all_ids($search_var, $_search_column,  $_search_direction);

        //count is important for datatables to function properly
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

        // edit button
        $_editBtn = '';
        if ($currentUser['role'] == 'admin' || $currentUser['user_id'] == $_object['user_id']) {
            $_editRole = $_type == 'user' ? '&role=' . $_role : '';
            $_editBtn = "<a class='badge badge-sm border border-dark font-weight-bold text-uppercase' title='Click here to edit' href='/admin/edit?type={$post['type']}&id={$post['id']}{$_editRole}'><i class='fal fa-edit'></i>&nbsp;Edit</a>";
        }

        //view button
        $_viewBtn = "<a title='Click here to view this record' class='badge badge-sm border border-dark font-weight-bold text-uppercase' target='new' href='/{$post['type']}/{$post['slug']}'><i class='fal fa-external-link-alt'></i>&nbsp;View</a>";

        //privacy label
        $_contentPrivacy = "<span class='badge badge-sm border border-dark font-weight-bold text-uppercase' title='Content privacy set to ".($_object['content_privacy'] ?? '')."'><span class='fal fa-".(
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

        //slug with ellipsis, shows full on hover
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

            /* start: MODULE TEXT VALUE TO DISPLAY IN LIST FIELD (CELL) */

            //value of module as saved in database
            //if language is defined in types.json, it uses _lang
            $module_input_slug_lang = $module['input_slug'] . (is_array($module['input_lang'] ?? null) ? "_{$module['input_lang'][0]['slug']}" : '');

            if ($_object[$module_input_slug_lang]) {

                //if module value is an array
                if (is_array($_object[$module_input_slug_lang])) {

                    $the_module_texts=array();

                    //if module value has linked data, and is also an array
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
                    //if module value is an array, without linked data
                    else {
                        $the_module_text = implode(', ', $_object[$module_input_slug_lang]);
                    }

                } else {

                    //if module value is not an array, but has linked data
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
                    }
                    //if module value is not an array, and does not have linked data
                    else {
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

            //display the module value
            $cont = '<span class="text-ellipsis record_module">'.$the_module_text.'</span>';
            
            //display the second cell of each row (primary)
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

            /* ends : MODULE TEXT VALUE TO DISPLAY IN LIST FIELD (CELL) */

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