/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');




window.Vue = require('vue');

import vSelect, { VueSelect } from 'vue-select'

import Axios from 'axios';
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))
$.ajaxSetup({
  headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

Vue.component('flash', require('./components/Flash.vue').default);

Vue.component('part-test', require('./components/PartTest.vue').default);
// Vue.component('vue-multiselect', require('./components/Multiselect.vue').default);
Vue.component('my-select', require('./components/Select.vue').default);

Vue.component('group', require('./components/Group.vue').default);

Vue.component('profile', require('./components/Profile.vue').default);

Vue.component('quiz-picture', require('./components/QuizPicture.vue').default);

Vue.component('answer-picture', require('./components/AnswerPicture.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app'    
});
