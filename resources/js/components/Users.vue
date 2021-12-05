<template>
    <div class="container">
       <div class="row mt-5">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Users</h3>

                <div class="card-tools">
                    <button 
                        class="btn btn-primary"
                        data-toggle="modal" 
                        data-target="#addNew">
                            <i class="fas fa-user-plus"></i>
                            Add New User
                    </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Registered At</th>  
                        <th>Modify</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="user in users.data" :key="user.id">
                       <td>{{user.id}}</td>
                    <td>{{user.name}}</td>
                    <td>{{user.email}}</td>
                    <td>{{user.type | upText}}</td>
                    <td>{{user.created_at | myDate}}</td>
                      <td>
                           <a href="#">
                                <i class="fas fa-edit blue"></i>
                                Edit
                            </a>
                            /
                             <a href="#">
                                <i class="fas fa-trash red"></i>
                                Delete
                            </a>
                      </td>
                    </tr>
                    
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>

        <!-- Modal -->
            <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addNewLabel">Add New</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                 <form @submit.prevent="createUser()">
                <div class="modal-body">
                    <div class="form-group">
                        <input 
                            type="text" 
                            class="form-control" 
                            placeholder="Enter Name"
                            v-model="form.name"
                            name="name">
                          <div class="red" v-if="form.errors.has('name')" v-html="form.errors.get('name')" />
                    </div>

                    <div class="form-group">
                        <input 
                            type="email" 
                            class="form-control" 
                            placeholder="Enter Email"
                            v-model="form.email"
                            name="email">
                          <div class="red" v-if="form.errors.has('email')" v-html="form.errors.get('email')" />
                    </div>

                     <div class="form-group">
                        <textarea  
                            class="form-control" 
                            placeholder="Enter Bio"
                            v-model="form.bio"
                            name="bio"></textarea>
                          <div class="red" v-if="form.errors.has('bio')" v-html="form.errors.get('bio')" />
                    </div>

                    <div class="form-group">
                        <select class="form-control" v-model="form.type" name="type">
                            <option value="">Select User Role</option>
                            <option value="admin">Admin</option>
                            <option value="user">Standard User</option>
                            <option value="author">Author</option>
                        </select>
                        <div class="red" v-if="form.errors.has('type')" v-html="form.errors.get('type')" />
                      </div>

                      <div class="form-group">
                        <input 
                            type="password" 
                            class="form-control" 
                            placeholder="Enter Password"
                            v-model="form.password"
                            name="bio">
                          <div class="red" v-if="form.errors.has('password')" v-html="form.errors.get('password')" />
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
                 </form>
                </div>
            </div>
            </div>

    </div>
</template>

<script>
    export default {
        data: () => {
           return {
                users : {},
                form: new Form({
                    id:'',
                    name : '',
                    email: '',
                    password: '',
                    type: '',
                    bio: '',
                    photo: ''
                })
            }
        },
     methods: {
          createUser () {
              this.$Progress.start()
              this.form.post('api/user');
              this.$Progress.finish();
              Fire.$emit('AfterCreate');

              Toast.fire({
                    icon: 'success',
                    title: 'User created successfully'
              });

              this.form.reset();
              $('#addNew').modal('hide');
        },
        loadUsers(){
            axios.get("api/user").then(({ data }) => (this.users = data));
        },

        
    },
    created() {
            this.loadUsers();
             Fire.$on('AfterCreate',() => {
               this.loadUsers();
           });
          //  setInterval(() => this.loadUsers(), 3000);
        }
}
</script>
