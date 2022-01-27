<?php
namespace Wildfire\Admin;

use \Wildfire\Core\Dash;
use \Wildfire\Core\MySQL as SQL;

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
                        <span class="badge badge-pill badge-light"><span class="badge badge-pill badge-info text-info mr-1">.</span> Parent</span>
                        <span class="badge badge-pill badge-light"><span class="badge badge-pill badge-secondary text-secondary mr-1">.</span>Child</span>
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
                                // parent records are marked with "info" color & children with "secondary" color
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
                            <pre style="width:50ch;" class="overflow-auto"><?= \json_encode($options['record'], $json_options) ?></pre>
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
}
