<template>
    <div class="container">
       <div class="row mt-5" v-if="$gate.isAdmin()">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Users</h3>

                <div class="card-tools">
                    <button class="btn btn-primary" @click="openAddModal()">
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
                           <a href="#" @click="openEditModal(user)">
                                <i class="fas fa-edit blue"></i>
                                Edit
                            </a>
                            /
                             <a href="#" @click="deleteUser(user.id)">
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
        <div v-if="!$gate.isAdmin()">
            <not-found></not-found>
        </div>
        <!-- Modal -->
            <div class="modal fade" id="modalDialog" tabindex="-1" role="dialog" aria-labelledby="modalDialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDialogLabel" v-show="!editMode">Add User</h5>
                    <h5 class="modal-title" id="modalDialogLabel" v-show="editMode">Update User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                 <form @submit.prevent="editMode ? updateUser(): createUser()">
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
                    <button type="submit" v-if="!editMode" class="btn btn-primary">Create</button>
                    <button type="submit" v-if="editMode" class="btn btn-success">Update</button>
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
                editMode: false,
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
          updateUser(){
              this.$Progress.start()
              this.form.patch('api/user/'+this.form.id)
               .then(()=>{
                          this.$Progress.finish();
                          Fire.$emit('AfterCreate');
                          Swal.fire(
                            'User Updated!',
                            'User has been updated.',
                            'success'
                          );
                          
                        $('#modalDialog').modal('hide');
                    }).catch(()=>{
                         this.$Progress.fail();
                            Swal.fire(
                              'Wanring!',
                              'User not updated, try again',
                              'warning'
                            )
                    });
          },
          openAddModal(){
              this.form.reset();
              $('#modalDialog').modal('show');
              this.editMode = false;
              console.log(this.editMode);
          },
          openEditModal(user){
              this.editMode = true;
              this.form.reset();
              $('#modalDialog').modal('show');
              this.form.fill(user);
               console.log(this.editMode);
          },
          deleteUser(id){
              Swal.fire({
                  title: 'Are you sure?',
                  text: "You won't be able to revert this!",
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
              }).then((result) => {
              if (result.isConfirmed) {
                    this.form.delete('api/user/'+id)
                    .then(()=>{
                        Fire.$emit('AfterCreate');
                        Swal.fire(
                            'Deleted!',
                            'Your file has been deleted.',
                            'success'
                        )
                    }).catch(()=>{
                         Swal.fire(
                            'Warning!',
                            'User not deleted, try agian please',
                            'warning'
                        )
                    })
              }
            })
          },
          createUser () {
              this.$Progress.start()
              this.form.post('api/user')
              .then(()=>{
                  this.$Progress.finish();
                  Fire.$emit('AfterCreate');
                    Toast.fire({
                      icon: 'success',
                      title: 'User created successfully'
                    });
                  this.form.reset();
                  $('#modalDialog').modal('hide');
              })
              .catch(()=>{

              })
        },
        loadUsers(){
            if(this.$gate.isAdmin()){
              axios.get("api/user").then(({ data }) => (this.users = data));
            }
        },        
    },
    created() {
            this.loadUsers();
             Fire.$on('AfterCreate',() => {
               this.loadUsers();
           });
        }
}
</script>
