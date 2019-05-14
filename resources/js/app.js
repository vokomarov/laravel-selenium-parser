import Vue from 'vue'
import ExampleComponent from './components/ExampleComponent'

Vue.component('example-component', ExampleComponent);

const app = new Vue({
    el: '#app',
});

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {}