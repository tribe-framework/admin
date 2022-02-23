<?php
/**
 * @var object $theme
 * @var object $dash
 * @var array $menus
 * @var array $types
 */

$admin_menus['admin_menu'] = $menus['admin_menu'];
$admin_menus['admin_footer_1'] = $menus['admin_footer_1'];
$admin_menus['admin_footer_2'] = $menus['admin_footer_2'];
$admin_menus['admin_menu']['logo']['name'] = "{$menus['admin_menu']['logo']['name']}";

$items = $admin_menus['admin_menu'];
$session_user = $dash->getSessionUser();
?>
<nav class="navbar navbar-expand-md navbar-light bg-primary mb-4 pt-1 pb-0">
    <!--  logo  -->
    <div>
        <a href="/admin" class="navbar-brand">
            <?php
            if (isset($items['logo']['name'])) :
                echo $items['logo']['name'];
                ?>
                <?php if (isset($items['logo']['byline'])): ?>
                <span class="small byline"><?= $items['logo']['byline'] ?></span>
            <?php endif; ?>
            <?php
            else:
                echo 'Wildfire';
            endif;
            ?>
        </a>
        <a id="preview" href="<?= $items['logo']['href'] ?? '#' ?>" class="text-white text-decoration-none px-2" title="Go to site" target="_blank" style="font-size: 1.2rem">
            <?php if (isset($items['logo']['src']) && trim($items['logo']['src'])): ?>
                <img <?= isset($items['logo']['height']) ? "height='{$items['logo']['height']}'" : '' ?> src="<?= $items['logo']['src'] ?>" alt="">
            <?php else: ?>
                <i class="fas fa-eye mr-2"></i>Preview
            <?php endif; ?>
        </a>
    </div>

    <!--  collapsible button  -->
    <button class="navbar-toggler text-white" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
        <i class="fas fa-bars"></i>
    </button>

    <!--  menu  -->
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ml-auto mr-0">
            <?php if (isset($items['menu'])): ?>
                <?php
                    foreach ($items['menu'] as $item):
                        $is_user_restricted = ($item['admin_access_only'] ?? false) && $types['user']['roles'][$session_user['role_slug']]['role'] != 'admin';
                        if ($is_user_restricted) continue;
                ?>
                    <?php if(is_array($item['submenu'] ?? null)): ?>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link text-white dropdown-toggle" title="<?= $item['title'] ?? '' ?>" role="button" data-toggle="dropdown">
                                <?= $item['name'] ?? '' ?>
                            </a>
                            <div class="dropdown-menu <?= $item['dropdown_class'] ?? '' ?>">
                                <?php foreach ($item['submenu'] as $sub_item): ?>
                                    <a href="<?= $sub_item['href'] ?? '#' ?>" class="dropdown-item" title="<?= $sub_item['title'] ?? '' ?>">
                                        <?= $sub_item['name'] ?>
                                    </a>
                                <?php endforeach ?>
                            </div>
                        </li>
                    <?php
                    elseif (isset($item['submenu'])):
                        $submenu = $item['submenu'];
                        $is_user_role_menu = false;
                        if (is_array($types[$submenu]['roles'] ?? null)) {
                            $sub_items = $types[$submenu]['roles'];
                            $is_user_role_menu = true;
                        }
                        else {
                            $_type = $item['submenu'] ?? '';
                            $_priority_field = $types[$submenu]['priority_field'] ?? '';
                            $_priority_order = $types[$submenu]['priority_order'] ?? '';
                            $sub_items = $dash->get_all_ids($_type, $_priority_field, $_priority_order);

                            unset($_type, $_priority_field, $_priority_order);
                        }
                    ?>
                        <li class="nav-item dropdown">
                            <a href="#" class="nav-link text-white dropdown-toggle" title="<?= $item['title'] ?? '' ?>" role="button" data-toggle="dropdown">
                                <?= $item['name'] ?? '' ?>
                            </a>
                            <div class="dropdown-menu <?= $item['dropdown_class'] ?? '' ?>">
                                <?php if (is_array($sub_items)) : ?>
                                <?php
                                    foreach ($sub_items as $key => $opt):
                                        if ($is_user_role_menu) {
                                            $sub_item = $opt;
                                            if (isset($opt['type']) && $opt['type']) {
                                                $sub_item['href'] = '/admin/list?type=' . $key;
                                            } else {
                                                $sub_item['href'] = '/admin/list?type=' . $item['submenu'] . '&role=' . $key;
                                            }
                                        } else {
                                            $sub_item = $this->dash->get_content($opt['id']);
                                            $sub_item['href'] = '/' . $item['submenu'] . '/' . $sub_item['slug'];
                                        }
                                ?>
                                        <a href="<?=$sub_item['href'] ?? ''?>" class="dropdown-item">
                                            <?=$sub_item['title'] ?? ''?>
                                        </a>
                                <?php endforeach ?>
                                <?php endif ?>
                            </div>
                        </li>
                    <?php
                    else:
                        $data_ext = '';
                        if (isset($item['data'])) {
                            foreach ($item['data'] as $data) {
                                foreach ($data as $k => $v) {
                                    $data_ext .= 'data-' . $k . '="' . $v . '" ';
                                }
                            }
                        }
                    ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" <?=$data_ext ?? ''?> href="<?=$item['href'] ?? ''?>" title="<?= $item['title'] ?? ''?>">
                                <?=$item['name'] ?? ''?>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach ?>
            <?php endif ?>
        </ul>
    </div>
</nav>
