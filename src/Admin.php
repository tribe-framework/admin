<?php

namespace Wildfire;

use \Wildfire\Auth as Auth;
use \Wildfire\Core\Dash;

class Admin {
    public function __construct() {
        $this->dash = new Dash();
    }

    public function get_admin_menu($page, $type = '', $role_slug = '', $id = 0, $edit_form = false) {
        $op = '';

        if ($page == 'dash') {
            $op .= '
			<div class="mb-4"><div class="card-body p-0">
			<div class="btn-toolbar justify-content-between">
			  ' . $this->list_types() . '
			</div>
			</div></div>';
        }
        elseif ($page == 'list') {
            $op .= '
			<div class="mb-4"><div class="card-body p-0">
			<div class="btn-toolbar justify-content-between">
			  ' . $this->list_types($type) ."<div id='edit-btn-group' class='d-none btn-group'><button type='button' data-toggle='modal' data-target='#duplicateConfirm' data-attr='copy-multi' class='btn btn-outline-primary border-top-0 border-right-0 border-left-0'><i class='fas fa-copy mr-2'></i>Copy</button><button type='button' data-toggle='modal' data-target='#deleteConfirm' data-attr='delete-multi' class='btn btn-outline-danger border-top-0 border-right-0 border-left-0'><i class='fas fa-trash-alt mr-2'></i>Delete</button></div>". $this->new_and_list($type, $role_slug) . '
			</div>
			</div></div>';
        }
        elseif ($page == 'edit') {
            $edit_options = $this->edit_options($type, $id, $edit_form);

            $op .= "<div class='mb-4 bg-white sticky-top' style='z-index:100;'>
                        <div class='card-body p-0'>
                            <div class='btn-toolbar border-bottom py-2 border-light justify-content-center align-items-center'>{$edit_options}</div>
                        </div>
                    </div>";
        }

        return $op;
    }

    public function edit_options($type, $id = 0, $edit_form = false): string
    {
        $view_url = $id ? BASE_URL . '/' . $type . '/' . $this->dash->getAttribute($id, 'slug') : '#';
        $visibility_class = $type == 'user' ? 'd-none' : '';
        $is_disabled = $id ? '' : 'disabled';

        $button = [];
        $button['save'] = "<button type='submit' class='btn px-5 btn-success rounded-0 save_btn'><i class='fas fa-save'></i> Save</button>";
        $button['view'] = "<a href='{$view_url}' target='new' class='btn px-5 btn-outline-primary rounded-0 view_btn {$visibility_class}' {$is_disabled}><i class='fas fa-external-link-alt'></i> View</a>";
        $button['delete'] = "<button type='button' class='btn px-5 btn-outline-danger rounded-0 delete_btn' data-id='{$id}'><i class='fas fa-trash-alt mr-2'></i>Delete</button>";
        $button['close'] = $edit_form ? '' : "<button type='button' class='btn d-inline-flex align-items-center justify-content-center rounded-0 close_btn' title='Close'><i class='fas fa-times'></i></button>";

        $formId = $id ? "#{$id}" : '';
        return "<h6 class='mb-0 text-muted' id='formId'>{$formId}</h6>
                <div class='btn-group mx-auto'>
                    {$button['save']} {$button['view']} {$button['delete']}
                </div>
                {$button['close']}";
    }

    public function new_and_list($type, $role_slug = '') {
        return '
		<div class="btn-group">
			<a class="edit_button btn btn-outline-primary border-top-0 border-left-0 border-right-0 rounded-0" data-type="'.$type.'"  data-role="'.$role_slug.'"href="#editModal" data-toggle="modal" data-href="/admin/edit?type=' . $type . (trim($role_slug ?? '') ? '&role=' . urlencode($role_slug) : '') . '"><span class="fas fa-edit"></span>&nbsp;New</a>
			<a href="' . BASE_URL . '/admin/list?type=' . $type . (trim($role_slug ?? '') ? '&role=' . urlencode($role_slug) : '') . '" class="btn btn-outline-primary border-top-0 border-left-0 border-right-0 rounded-0"><span class="fas fa-list"></span>&nbsp;List</a>
		</div>';
    }

    public function list_types($type = '') {
        $types = $this->dash->getTypes();

        if ($type) {

            $list_types = '<div class="btn-group" role="group"><a href="' . BASE_URL . '/admin" class="btn btn-outline-primary border-top-0 border-left-0 border-right-0 rounded-0"><span class="fas fa-tachometer-alt"></span></a>';

            $list_types .= '<button id="types-admin-dropdown" type="button" class="btn btn-outline-primary border-top-0 border-left-0 border-right-0 rounded-0 dropdown-toggle" data-toggle="dropdown">' . (isset($type) ? ucfirst($types[$type]['plural'] ?? '') : '') . '&nbsp;<span class="sr-only">Content types</span></button><div class="dropdown-menu" aria-labelledby="types-admin-dropdown">';
            foreach ($types as $key => $value) {
                if (isset($types[$key]['type']) && $types[$key]['type'] == 'content') {
                    $list_types .= '<a class="dropdown-item" href="' . BASE_URL . '/admin/list?type=' . $types[$key]['slug'] . '">' . ucfirst($types[$key]['plural']) . '</a>';
                }
            }
            $list_types .= '</div></div>';
        } else {

            $list_types = '<div class="btn-group" role="group">';

            $list_types .= '<button id="types-admin-dropdown" type="button" class="btn btn-outline-primary border-top-0 border-left-0 border-right-0 rounded-0 dropdown-toggle d-md-none" data-toggle="dropdown">' . (isset($types[$type]) ? ucfirst($types[$type]['plural']) : '') . '&nbsp;Content types</button><div class="dropdown-menu" aria-labelledby="types-admin-dropdown">';
            foreach ($types as $key => $value) {
                if (isset($types[$key]['type']) && $types[$key]['type'] == 'content') {
                    $list_types .= '<a class="dropdown-item" href="' . BASE_URL . '/admin/list?type=' . $types[$key]['slug'] . '">' . ucfirst($types[$key]['plural']) . '</a>';
                }
            }
            $list_types .= '</div><div class="btn-group d-none d-md-block" role="group">';
            foreach ($types as $key => $value) {
                if (isset($types[$key]['type']) && $types[$key]['type'] == 'content' && $types[$key]['slug']) {
                    $list_types .= '<a class="btn btn-outline-primary border-top-0 border-left-0 border-right-0 rounded-0" href="' . BASE_URL . '/admin/list?type=' . $types[$key]['slug'] . '">' . ucfirst($types[$key]['plural']) . '</a>';
                }
            }
            $list_types .= '</div></div>';
        }
        return $list_types;
    }

    public function is_access_allowed($id) {
        $types = $this->dash->getTypes();
        $auth = new Auth();
        $currentUser = $auth->getCurrentUser();

        //if user has even on field allowing access to edit post, they will be given access to the post
        $allowed_access = 0;
        if ( $types['user']['roles'][$currentUser['role_slug']]['role'] == 'admin' || $types['user']['roles'][$currentUser['role_slug']]['role'] == 'crew' ) {
           $allowed_access = true;
        } else {
            $allowed_access = false;
        }

        return $allowed_access;
    }
}
