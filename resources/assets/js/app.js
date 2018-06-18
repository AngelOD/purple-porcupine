
import "./bootstrap";

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import WorldDomination from './components/WorldDomination.vue';

Vue.component('world-domination', WorldDomination);

const app = new Vue({
    el: '#app'
});
