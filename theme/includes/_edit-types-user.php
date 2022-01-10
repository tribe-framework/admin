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
        <h6>Roles</h6>
        <button type="button" class="btn btn-outline-primary" @click="newRole">New<i
                class="ml-2 rounded-circle fas fa-plus"></i></button>
        <ul>
            <li v-for="role in roles">
                <a href="#/" :data-slug="role.slug" @click="edit">{{ role.title }}</a>
            </li>
        </ul>
    </div>

    <div id="edit-role" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header align-items-center">
                    <span class="modal-title font-weight-bold">Edit Role</span>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div v-if="modal" class="form-group">
                        <label for="role_title" class="mb-0 small text-capitalize font-weight-bold">title</label>
                        <input id="role_title" name="title" type="text" class="form-control" :value="editRole.title">
                    </div>
                    <div v-if="!editRole.slug" class="form-group">
                        <label for="slug" class="mb-0 small text-capitalize font-weight-bold">slug</label>
                        <input id="slug" name="slug" type="text" class="form-control" :value="editRole.slug">
                    </div>
                    <div v-if="modal && !editRole.role" class="form-group">
                        <label for="role" class="mb-0 small text-capitalize font-weight-bold">role</label>
                        <input id="role" name="role" type="text" class="form-control" :value="editRole.role">
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
        modal: false
    },
    computed: {
        editRole: function() {
            if (this.selectedRole) {
                return this.roles[this.selectedRole];
            }

            return "";
        }
    },
    methods: {
        edit(e) {
            e.preventDefault();
            this.selectedRole = e.target.dataset.slug;
            this.modal = true;
            $('#edit-role').modal({
                show: true,
                backdrop: 'static',
                keyboard: false
            });
        },
        updateRole() {
            let form = document.querySelectorAll('#edit-role input');
            const formEl = {};

            form.forEach(f => {
                formEl[f.name] = f.value;
            })

            if (this.selectedRole) {
                this.roles[this.editRole.slug] = {
                    ...this.editRole,
                    ...formEl
                }
            } else {
                this.roles[formEl.slug] = formEl;
            }

            $('#role-dismiss').click();
            this.modal = false;
            this.selectedRole = null;
        },
        newRole() {
            this.selectedRole = null;
            this.modal = true;
            $('#edit-role').modal({
                show: true,
                backdrop: 'static',
                keyboard: false
            });
        }
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
    }
});
</script>
