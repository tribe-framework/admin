<div id="app" class="container">
    <div id="edit-name">
        <div class="form-group">
            <label for="name" class="h6">Name</label>
            <input id="name" class="form-control" type="text" v-model="name">
        </div>
        <div class="form-group">
            <label for="plural" class="h6">Plural</label>
            <input id="plural" class="form-control" type="text" v-model="plural">
        </div>
    </div>

    <div>
        <h6>User Roles</h6>
        <button type="button" class="btn btn-outline-primary" @click="newRole">New<i
                class="ml-2 rounded-circle fas fa-plus"></i></button>
        <ul>
            <li v-for="(role, key, index) in roles" :key="index">
                <a href="#/" :data-slug="role.slug" @click="edit">{{ role.title }}</a>
            </li>
        </ul>
    </div>
    <div id="edit-role" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header align-items-center">
                    <span class="modal-title font-weight-bold">{{editRole.role ? 'Edit' : 'New'}} Role</span>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="role_title" class="mb-0 small font-weight-bold">title</label>
                        <input id="role_title" name="title" type="text" class="form-control" :value="editRole.title">
                    </div>
                    <div v-if="!editRole.slug" class="form-group">
                        <label for="slug" class="mb-0 small font-weight-bold">slug</label>
                        <input id="slug" name="slug" type="text" class="form-control" :value="editRole.slug">
                    </div>
                    <div v-if="!editRole.role" class="form-group">
                        <label for="role" class="mb-0 small font-weight-bold">role</label>
                        <select name="role" id="role" class="custom-select">
                            <option value="" disabled selected hidden>Select role</option>
                            <option v-for="role in roleTypes" :value="role">{{role}}</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="role-dismiss" type="button" class="btn btn-outline-danger m-0 mr-2 px-4"
                        data-dismiss="modal">Cancel</button>
                    <button v-if="editRole.role" type="button" class="btn btn-secondary m-0 mr-1 px-4"
                        @click="updateRole">Update</button>
                    <button v-else type="button" class="btn btn-success m-0 mr-1 px-4"
                        @click="updateRole">Create</button>
                </div>
            </div>
        </div>
    </div>

    <div>
        <h6>Role Types</h6>
        <button type="button" class="btn btn-outline-primary" @click="newRoleType">New<i
                class="ml-2 rounded-circle fas fa-plus"></i></button>
        <ul>
            <li v-for="roleType in roleTypes">{{ roleType }}</li>
        </ul>
    </div>
    <div id="edit-role-type" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header align-items-center">
                    <span class="modal-title font-weight-bold">Edit Role Type</span>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="role_title" class="mb-0 small text-capitalize font-weight-bold">title</label>
                        <input id="role_title" name="title" type="text" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="dismiss btn btn-outline-danger m-0 mr-2 px-4"
                        data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success m-0 mr-1 px-4" @click="updateRoleType">Create</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
<?php
$_user = json_decode(file_get_contents(TRIBE_ROOT."/config/types.json"))->user;
$_user = json_encode($_user);
?>

let user = <?=$_user?>;

let app = new Vue({
    el: '#app',
    data: {
        roles: user.roles,
        name: user.name,
        plural: user.plural,
        selectedRole: null,
        allRoles: null,
    },
    mounted: function() {
        let submitButton = document.querySelectorAll('button[type="submit"]');

        submitButton.forEach(btn => btn.addEventListener('click', e => {
            e.preventDefault();

            axios.post('/admin/update-types-user', {
                name: this.name,
                plural: this.plural,
                roles: {
                    ...this.roles
                }
            }).then(res => {
                $('#toast-success').toast('show');
            }).catch(() => {
                $('#toast-danger').toast('show');
            })
        }))
    },
    computed: {
        editRole: function() {
            if (this.selectedRole) {
                return this.roles[this.selectedRole];
            }

            return "";
        },
        roleTypes: {
            get: function() {
                if (this.allRoles) return this.allRoles;

                let types = [];

                Object.keys(this.roles).forEach(role => {
                    types.push(this.roles[role].role);
                })

                return [...new Set(types)];
            },
            set: function (val) {
                this.allRoles = [...new Set(val)];
            }
        }
    },
    methods: {
        edit(e) {
            e.preventDefault();
            this.selectedRole = e.target.dataset.slug;
            $('#edit-role').modal({
                show: true,
                backdrop: 'static',
                keyboard: false
            });
        },
        updateRole() {
            let form = document.querySelectorAll('#edit-role input');
            let select = document.querySelector('#edit-role select');
            const formEl = {};

            form.forEach(f => {
                formEl[f.name] = f.value;
                f.value = '';
            })

            if (this.selectedRole) { // for edited role
                this.roles = Object.assign({}, this.roles, {
                    ...this.roles,
                    [this.editRole.slug]: {
                        ...this.editRole,
                        ...formEl
                    }
                })
            } else { // for new role
                this.roles = Object.assign({}, this.roles, {
                    ...this.roles,
                    [formEl.slug]: {
                        ...formEl,
                        role:select.value
                    }
                })
            }

            $('#role-dismiss').click();
            select.value = '';
            this.selectedRole = null;
        },
        newRole() {
            this.selectedRole = null;
            $('#edit-role').modal({
                show: true,
                backdrop: 'static',
                keyboard: false
            });
        },
        newRoleType() {
            $('#edit-role-type').modal({
                show: true,
                backdrop: 'static',
                keyboard: false
            })
        },
        updateRoleType(e) {
            let role = document.querySelector('#edit-role-type input');
            $('#edit-role-type button.dismiss').click();
            this.roleTypes = [...this.roleTypes, role.value];
            role.value = '';
        }
    }
});
</script>
