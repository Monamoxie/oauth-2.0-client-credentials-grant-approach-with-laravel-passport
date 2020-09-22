import Vue from 'vue'
require('./bootstrap');

Vue.component(
    'passport-clients',
    require('./components/passport/Clients.vue').default
);

 
const app = new Vue({
    el: '#credentials'
})
