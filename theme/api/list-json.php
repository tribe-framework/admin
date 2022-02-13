<?php
require_once __DIR__ . '/../_init.php';

$api = new \Wildfire\Api;
$fn = new \Wildfire\Admin\Functions;

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

        if ($or['data'][$i] = $fn->getDatatableRowArray($_object, $i))
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