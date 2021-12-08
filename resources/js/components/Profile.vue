<template>
    <div class="container">
        <div class="row">
          <div class="col-md-3 mt-5">

            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="text-center">
                    <img 
                        class="profile-user-img img-fluid img-circle" 
                        v-bind:src="profile_url" 
                        alt="User profile picture">
                </div>
                <h3 class="profile-username text-center">{{profile_name}}</h3>
                <ul class="list-group list-group-unbordered mb-3">
                  <li class="list-group-item">
                    <b>Followers</b> <a class="float-right">1,322</a>
                  </li>
                  <li class="list-group-item">
                    <b>Following</b> <a class="float-right">543</a>
                  </li>
                  <li class="list-group-item">
                    <b>Friends</b> <a class="float-right">13,287</a>
                  </li>
                </ul>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

    
          </div>
          <!-- /.col -->
          <div class="col-md-9 mt-5">
            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Settings</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="tab-pane active" id="settings">
                    <form class="form-horizontal">
                      <div class="form-group row">
                        <label for="name" class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" id="name" placeholder="Name" v-model="form.name" @keypress="updateProfileName()">
                           <div class="red" v-if="form.errors.has('name')" v-html="form.errors.get('name')" />
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="email" class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10">
                          <input type="email" class="form-control" id="email" placeholder="Email" v-model="form.email">
                          <div class="red" v-if="form.errors.has('email')" v-html="form.errors.get('email')" />
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="bio" class="col-sm-2 col-form-label">Bio</label>
                        <div class="col-sm-10">
                          <textarea class="form-control" id="bio" placeholder="Bio" v-model="form.bio"></textarea>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="profile" class="col-sm-2 col-form-label">Profile</label>
                        <div class="col-sm-10">
                          <input type="file" class="form-control" id="profile" @change="updateProfile" style="min-height:42px;">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="password" class="col-sm-2 col-form-label">Password</label>
                        <div class="col-sm-10">
                          <input type="password" class="form-control" id="password" placeholder="Password" v-model="form.password">
                          <div class="red" v-if="form.errors.has('password')" v-html="form.errors.get('password')" />
                        </div>
                      </div>
                      <div class="form-group row">
                        <div class="offset-sm-2 col-sm-10">
                          <button type="submit" class="btn btn-danger" @click.prevent="updateInfo">Submit</button>
                        </div>
                      </div>
                    </form>
                  </div>
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
    </div>
</template>

<script>
    export default {
        data: ()=>{
                return {
                    profile_url:'',
                    profile_name:'',
                    form: new Form({
                            id:'',
                            name:'',
                            email:'',
                            password:'',
                            type:'',
                            bio:'',
                            photo:'',
                    })
                }
        },
        methods: {
                updateInfo(){
                        this.$Progress.start()
                        this.form.put('api/profile')
                        .then(()=>{
                            this.$Progress.finish();
                        }).catch(()=>{
                            this.$Progress.fail();
                        })
                },
                updateProfile(e){
                    let file = e.target.files[0];
                    let reader = new FileReader();
                    let limit = 1024*300;
                    if(file['size'] > limit){
                        Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'You are uploading a large file',
                            })
                        return false;
                    }
                    reader.onloadend = (file) => {
                        this.form.photo = reader.result;
                    }
                   reader.readAsDataURL(file);
                  this.profile_url = URL.createObjectURL(e.target.files[0]);  
                },
                updateProfileName(){
                    this.profile_name = this.form.name;
                }
        },
        created(){
                axios.get("api/profile")
                  .then((data) => {
                      this.form.reset();
                      this.form.id = data.data.id;
                      this.form.name = data.data.name;
                      this.form.email = data.data.email;
                      this.form.bio = data.data.bio;
                      this.form.photo = data.data.photo;
                      this.form.type = data.data.type;
                      this.profile_url = "img/profile/"+data.data.photo;
                      this.profile_name = data.data.name;
                  });
        }
    }
</script>
