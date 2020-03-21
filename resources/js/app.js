import VueAxios from 'vue-axios';
import Vue from "vue";
import App from "./App.vue";
import axios from 'axios';

require('./bootstrap');

Vue.config.productionTip = false;
Vue.use(VueAxios, axios);

new Vue({
    render: h => h(App)
}).$mount("#app");
