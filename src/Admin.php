<?php

namespace Wildfire;

use \Wildfire\Auth as Auth;
use \Wildfire\Core\Dash;

class Admin {
    public function __construct() {
        $this->dash = new Dash();
    }

    public function get_admin_menu($page, $type = '', $role_slug = '', $id = 0) {
        $op = '';
        if ($page == 'dash') {
            $op .= '
			<div class="mb-4"><div class="card-body p-0">
			<div class="btn-toolbar justify-content-between">
			  ' . $this->list_types() . '
			</div>
			</div></div>';
        }
        if ($page == 'list') {
            $op .= '
			<div class="mb-4"><div class="card-body p-0">
			<div class="btn-toolbar justify-content-between">
			  ' . $this->list_types($type) ."<div id='edit-btn-group' class='d-none btn-group'><button type='button' data-toggle='modal' data-target='#duplicateConfirm' data-attr='copy-multi' class='btn btn-outline-primary border-top-0 border-right-0 border-left-0'><i class='fas fa-copy mr-2'></i>Copy</button><button type='button' data-toggle='modal' data-target='#deleteConfirm' data-attr='delete-multi' class='btn btn-outline-danger border-top-0 border-right-0 border-left-0'><i class='fas fa-trash-alt mr-2'></i>Delete</button></div>". $this->new_and_list($type, $role_slug) . '
			</div>
			</div></div>';
        }
        if ($page == 'edit') {
            $op .= '
			<div class="mb-4"><div class="card-body p-0">
			<div class="btn-toolbar justify-content-center">
			' . $this->edit_options($type, $id) . '
			</div>
			</div></div>';
        }
        return $op;
    }

    public function edit_options($type, $id = 0) {
        return '<div class="btn-group">
					<button type="submit" class="btn px-5 btn-success rounded-0 save_btn"><span class="fas fa-save"></span>&nbsp;Save</button>
					<a href="' . ($id ? BASE_URL . '/' . $type . '/' . $this->dash->getAttribute($id, 'slug') : '#') . '" target="new" class="btn px-5 btn-primary rounded-0 view_btn ' . ($type == 'user' ? 'd-none' : '') . ' ' . ($id ? '' : 'disabled') . '"><span class="fas fa-external-link-alt"></span>&nbsp;View</a>
				</div>';
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

    public function is_access_allowed($id, $user_restricted_to_input_modules = array()) {
        $auth = new Auth();
        $currentUser = $auth->getCurrentUser();

        //if user has even on field allowing access to edit post, they will be given access to the post
        $allowed_access = 0;
        if (count($user_restricted_to_input_modules)) {
            foreach ($user_restricted_to_input_modules as $key => $value) {
                if (is_array($this->dash->get_content_meta($id, $value)) && count(array_intersect($currentUser[$value], $this->dash->get_content_meta($id, $value)))) {
                    $allowed_access = 1;
                    break;
                } elseif (in_array($this->dash->get_content_meta($id, $value), $currentUser[$value]) || ($currentUser[$value] && $this->dash->get_content_meta($id, $value) == $currentUser[$value])) {
                    $allowed_access = 1;
                    break;
                }
            }
        } else {
            $allowed_access = 1;
        }

        return $allowed_access;
    }
}
