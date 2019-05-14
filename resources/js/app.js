import Vue from 'vue'
import ExampleComponent from './components/ExampleComponent'

Vue.component('example-component', ExampleComponent);

const app = new Vue({
    el: '#app',
});
