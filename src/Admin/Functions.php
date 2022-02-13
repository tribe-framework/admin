<?php
namespace Wildfire\Admin;

use \Wildfire\Core\Dash;
use \Wildfire\Core\MySQL as SQL;
use \Wildfire\Auth;

class Functions {
    public $json_options;

    public function __construct()
    {
        $this->json_options = JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PARTIAL_OUTPUT_ON_ERROR|JSON_PRETTY_PRINT;
    }

    public function displayRecordCard (array $options)
    {
        $_default_options = [
            'record' => '',
            'parent_or_child' => 'child',
            'types' => [],
            'tab_default_state' => '',
            'display_legend' => false
        ];

        $options = array_merge($_default_options, $options);

        //if row_id doesn't exists
        if (is_array($options['record']) && !isset($options['record']['id'])) return;

        $json_options = $this->json_options;

        //IF THE MODULE HAS A TITLE, USE IT, OR ELSE SHOW SLUG
        $record_type = $options['record']['type'];
        $type_primary_module = $options['types'][$record_type]['primary_module'] ?? '';

        if ($type_primary_module
            && !($record_title = $options['record'][$type_primary_module])
            && !($record_title = $options['record']['title'])
            && !($record_title = $options['record']['name'])
        ) {
            $record_title = $options['record']['slug'];
        }

        // pure html code to print
        ?>
        <tr class="col-12">
            <td>
                <?php if ($options['display_legend']): ?>
                    <div class="px-2 mb-3">
                        <span class="badge badge-pill badge-light"><span class="badge badge-pill badge-dark text-dark mr-1">.</span> The record</span>
                        <span class="badge badge-pill badge-light"><span class="badge badge-pill badge-info text-info mr-1">.</span> Parent records</span>
                        <span class="badge badge-pill badge-light"><span class="badge badge-pill badge-secondary text-secondary mr-1">.</span>Child records</span>
                    </div>
                <?php endif; ?>
                <div class="card">
                    <a
                        class="p-2 w-100 text-left card-header d-flex justify-content-between align-items-center text-decoration-none"
                        data-toggle="collapse"
                        href="#output_<?=$options['record']['id']?>"
                        role="button"
                        aria-expanded="false"
                        aria-controls="output_<?=$options['record']['id']?>"
                        ><h6 class="font-weight-light mb-0 d-flex align-items-center">
                            <?php
                                // parent records are marked with "info" color & children with "secondary" color, the record itself with dark
                                if ($options['record']['id'] == $_GET['row_id'])
                                    $badge_poc = 'badge-dark';
                                else
                                    $badge_poc = $options['parent_or_child'] == 'parent' ? 'badge-info' : 'badge-secondary';
                            ?>
                            <span class="badge badge-pill <?= $badge_poc ?> mr-2"><?= $options['record']['id']?></span>
                            <span class="badge badge-pill badge-primary mr-2">
                                <?= "{$options['record']['type']}" ?> <?= isset($options['record']['role_slug']) ? " | ".$options['record']['role_slug'] : "" ?>
                            </span>
                            <span class="pt-1"><?=$record_title ?? ''?></span>
                        </h6>
                        <i class="fal fa-chevron-down"></i>
                    </a>
                    <div class="collapse <?=$options['tab_default_state']?>" id="output_<?=$options['record']['id']?>">
                        <div class="card-body search_output p-0">
                            <pre style="white-space:pre-wrap" class="overflow-auto"><?= \json_encode($options['record'], $json_options) ?></pre>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <span class="col-6 border-right border-light small">
                                    <?php
                                    $meta_1[] = ( $options['record']['created_on'] ? 'created_on: '.\date('d-M-Y H:i', $options['record']['created_on']) : null );
                                    $meta_1[] = ( $options['record']['updated_on'] ? 'updated_on: '.\date('d-M-Y H:i', $options['record']['updated_on']) : null );
                                    echo implode('<br>', array_filter($meta_1,'strlen'));
                                    ?>
                                </span>
                                <span class="col-5 border-right border-light small">
                                    <?php
                                    $meta_2[] = ( $options['record']['user_id'] ? 'user_id: '.$options['record']['user_id'] : '' );
                                    $meta_2[] = ( ($options['record']['created_by'] ?? null) ? 'created_by: '.$options['record']['created_by'] : '' );
                                    $meta_2[] = ( ($options['record']['updated_by'] ?? null) ? 'updated_by: '.$options['record']['updated_by'] : '' );
                                    $meta_2[] = ( ($options['record']['content_privacy'] ?? null) ? 'content_privacy: '.$options['record']['content_privacy'] : '' );

                                    echo implode('<br>', array_filter($meta_2,'strlen'));
                                    ?>
                                </span>
                                <span class="col-1">
                                    <?php
                                    $meta_3[] = '<a href="/admin/?row_id='.$options['record']['id'].'"><span class="fas fa-external-link-alt"></span></a>';
                                    echo implode('<br>', array_filter($meta_3,'strlen'));
                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
        <?php // html ends
    }

    public function getDbRecord (array $db_record_dependency, array $db_record)
    {
        $sql = new SQL;
        $dash = new Dash;

        $q = $sql->executeSQL("SELECT `id` FROM `data` WHERE `content`->>'$.{$db_record['type']}_id'='{$db_record['id']}' LIMIT 1000");

        if ($q) {
            foreach($q as $v) {
                $db_record_dependency['child'][] = $dash->getObject($v['id']);
            }
            unset($q);
        }

        $q = $sql->executeSQL("SELECT `id` FROM `data` WHERE FIND_IN_SET(`content`->>'$.{$db_record['type']}_ids', '{$db_record['id']}') LIMIT 1000");

        if ($q) {
            foreach($q as $v) {
                $db_record_dependency['child'][] = $dash->getObject($v['id']);
            }
            unset($q);
        }

        $q = $sql->executeSQL("SELECT `id` FROM `data` WHERE `content`->>'$.{$db_record['type']}'='{$db_record['slug']}' LIMIT 1000");

        if ($q) {
            foreach($q as $v) {
                $db_record_dependency['child'][] = $dash->getObject($v['id']);
            }
            unset($q);
        }

        $q = $sql->executeSQL("SELECT `id` FROM `data` WHERE JSON_CONTAINS(`content`->>'$.{$db_record['type']}', '\"{$db_record['slug']}\"', '$') LIMIT 1000");

        if ($q) {
            foreach($q as $v) {
                $db_record_dependency['child'][] = $dash->getObject($v['id']);
            }
            unset($q);
        }

        return $db_record_dependency;
    }

    public function getDatatableRowArray($_object, $row_number=0) {
        
        $sql = new SQL;
        $auth = new Auth();
        $dash = new Dash;

        $currentUser = $auth->getCurrentUser();
        $types = $dash->getTypes();

        $post = array();

        //little messy right now, too many overlapping variables
        //using everything to reduce number of variables in the function
        $_type = $_object['type'];
        $_role = ($_object['role_slug'] ?? '');
        $post['id'] = $_object['id'];
        $post['type'] = $_object['type'];
        $post['role'] = ($_object['role_slug'] ?? '');
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
            $_editBtn = "<a class='edit_button badge badge-sm border border-dark font-weight-bold text-uppercase' title='Click here to edit' data-type='{$post['type']}' data-role='{$post['role']}' data-slug='{$post['slug']}' data-row_number='{$row_number}' data-id='{$post['id']}' href='#editModal' data-toggle='modal' data-href='/admin/edit?type={$post['type']}&id={$post['id']}{$_editRole}'><i class='fas fa-edit text-success'></i>&nbsp;Edit</a>";
        }

        //view button
        $_viewBtn = "<a title='Click here to view this record' class='".($post['type']=='user'?'d-none':'')." badge badge-sm border border-dark font-weight-bold text-uppercase' target='new' href='/{$post['type']}/{$post['slug']}'><i class='fas fa-external-link-alt text-success'></i>&nbsp;View</a>";

        //privacy label
        $_contentPrivacy = "<span class='badge badge-sm border border-dark font-weight-bold text-uppercase' title='Content privacy set to ".($_object['content_privacy'] ?? '')."'><span class='fas fa-".(
                $_object['type'] == 'user' ? 'user' : (
                    $_object['content_privacy'] == 'public' ? 'megaphone text-green' : (
                        $_object['content_privacy'] == 'private' ? 'link text-red' : (
                            $_object['content_privacy'] == 'pending' ? 'hourglass-half text-yellow' : 'paragraph text-dark'
                        )
                    )
                )
            )."'></span> ".( 
                $_object['type'] == 'user' ? 'user' : 
                (trim($_object['content_privacy']) ?? "draft")
            )."</span>";

        //slug with ellipsis, shows full on hover
        $_slugLine = '<span class="d-none d-md-inline-block" data-toggle="tooltip" data-placement="bottom" title="'.$post['slug'].'"><span class="ml-1 small text-muted slug-ellipsis">'.$post['slug'].'</span></span>';

        // button controls for this single post
        $data[] = '<span>'.$post['id'].'</span>';

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
                $data[] = is_array($cont) ? $cont : trim($cont);
            }
        }

        if ($post['id'] && !$donotlist)
            return $data;
        else
            return false;
    }
}
