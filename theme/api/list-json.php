<?php
/**
 * @var object $sql
 * @var object $dash
 * @var object $admin
 * @var array $types
 */
require_once __DIR__ . '/../_init.php';

$api = new \Wildfire\Api;
$fn = new \Wildfire\Admin\Functions;

$i = 0;
$or=array();

$_type = $_GET['type'] ?? null;
$_role = $_GET['role'] ?? null;

// count number of records, if number of records are more than 5k, use ajax method with datatables
if ($_type == 'user') {
    $unfiltered_ids_number = $sql->executeSQL("SELECT COUNT(`id`) AS `total` FROM `data` WHERE `type`='$_type' AND `role_slug`='$_role'")[0]['total'];
} else {
    $unfiltered_ids_number = $sql->executeSQL("SELECT COUNT(`id`) AS `total` FROM `data` WHERE `type`='$_type'")[0]['total'];
}

// if number is 5k build SQL query to fetch what user has typed in filter search
if ($unfiltered_ids_number>5000) {
    //load the search query
    $_search_query = addslashes(strtolower($_GET['search']['value']));

    if ( strstr($_search_query, '##') ) {
        $temp_arr = explode('##', $_search_query);
        foreach ($temp_arr as $temp_val) {
            $temp_val = explode('::', $temp_val);
            $_search_by_column[] = trim($temp_val[0]);
            $_search_query_by_column[] = trim($temp_val[1]);
        }
    }
    else { $_search_by_column = false; }

    // loading all list-able columns for the type
    foreach ($types[$_type]['modules'] as $module)  {
        if (isset($module['list_field']) || $module['input_slug']=='id') {
            $columns[]=$module['input_slug'];
        }

        //searchable columns, marry it to search query - SQL
        if (!$_search_by_column) {
            if ( isset($module['list_searchable']) && trim($_search_query) ) {
                $columns[]=$module['input_slug'];
                $_search_sql_query[] = "LOWER(`content`->>'$.".$module['input_slug']."') LIKE '%{$_search_query}%'";
                $_search_sql_query[] = "`id`='".$_search_query."'";
            }
        } else {
            if ($key = array_search($module['input_slug'], $_search_by_column)) {
                $columns[]=$module['input_slug'];

                if ($module['input_slug']=='id')
                    $input_module_key = "`id`";
                else
                    $input_module_key = "LOWER(`content`->>'$.".$module['input_slug']."')";

                $_search_query = $_search_query_by_column[$key];
                if ($_search_query=='**') {
                    $_search_sql_query[] = "(
                        ".$input_module_key." = '' OR
                        ".$input_module_key." LIKE '[\"\"]' OR
                        ".$input_module_key." IS NULL
                        )";
                }
                else if ($_search_query=='!**') {
                    $_search_sql_query[] = "(
                        ".$input_module_key." != '' AND
                        ".$input_module_key." NOT LIKE '[\"\"]' AND
                        ".$input_module_key." IS NOT NULL
                        )";
                }
                else {
                    $_search_sql_query[] = "(
                        ".$input_module_key." LIKE '%{$_search_query}%' OR
                        ".$input_module_key." = '$_search_query'
                    )";
                }
            }
        }
    }
}

//remaining parts of the SQL statement, params sent by datatables
$_search_length = $_GET['length'] ?? 50;
$_search_start = $_GET['start'] ?? 0;
$_search_direction = $_GET['order'][0]['dir'] ?? 'DESC';
$_search_column = ((isset($_GET['order']) && $_GET['order'][0]['column']) ? $columns[$_GET['order'][0]['column']] : 'id');

if ($api->method('get')) {

    //create search_var for $dash->get_all_ids
    //if type is user, use role as well
    $search_var = ($_type == 'user') ? ['type' => $_type, 'role_slug' => $_role] : $_type;

    //if more than 5k records, use SQL directly
    if ($unfiltered_ids_number>5000) {
        if ($_search_sql_query ?? false) {
            $_search_sql_query_joined = !$_search_by_column ? implode(' OR ', $_search_sql_query) : implode(' AND ', $_search_sql_query);
        }

        //part of query that fetches the correct results, takes care of sort order
        $_final_query = "FROM `data` WHERE `type`='{$_type}' ".($_type=='user' ? "AND `role_slug`='{$_role}'" : "")." ".( trim($_search_query) ? "AND (".$_search_sql_query_joined.")" : "" )." ORDER BY `{$_search_column}` {$_search_direction}";

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
    //if less than 5k records, simply use $dash->get_all_ids
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
        if ( !$admin->is_access_allowed($_object['id']) ) {
            continue;
        }

        if ($or['data'][$i] = $fn->getDatatableRowArray($_object, $i))
            $i++;
    }

    if (!$or['data'][0][0]) {
        $or = ['data' => false];
    }

    $api->json($or)->send();
}
