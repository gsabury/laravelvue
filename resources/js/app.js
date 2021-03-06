/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue').default;

// import VueMoment from 'vue-moment'
// import moment from 'moment-timezone'
// Vue.use(VueMoment, {moment,})

import moment from 'moment';

import VueRouter from 'vue-router'
Vue.use(VueRouter);

import VueProgressBar from 'vue-progressbar'
Vue.use(VueProgressBar, {
  color: 'rgb(143, 255, 199)',
  failedColor: 'red',
  height: '2px'
})

import Swal from 'sweetalert2';
window.Swal = Swal;
const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
})
window.Toast = Toast;

import Form from 'vform';


window.Form = Form;

window.Fire = new Vue();

import Gate from "./Gate";
Vue.prototype.$gate = new Gate(window.user);

let routes = [
    { path: '/dashboard', component: require('./components/Dashboard.vue').default},
    { path: '/profile', component: require('./components/Profile.vue').default},
    { path: '/users', component: require('./components/Users.vue').default},
    {path:'*', component: require('./components/NotFound.vue').default}
  ]

  const router = new VueRouter({
    mode: 'history',
    routes // short for `routes: routes`
  })

  Vue.filter('upText', function(text){
      return text.charAt(0).toUpperCase() + text.slice(1)
  });

  Vue.filter('myDate',function(created){
      return moment(created).format('MMMM Do YYYY');
  });

  Vue.component('not-found', require('./components/NotFound.vue').default);

  Vue.component('pagination', require('laravel-vue-pagination'));

  Vue.component('Spinner', require('vue-simple-spinner'));


/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
    router,
    data: {
          search:''
    },
    methods:{
            searchUser: _.debounce(() => {
              Fire.$emit('searching');
            }, 1000),
      }
});
