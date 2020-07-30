import { InertiaApp } from '@inertiajs/inertia-vue'
import Vue from 'vue'
import route from 'ziggy';
import { Ziggy } from './ziggy';

require('bootstrap')
window.$ = window.jQuery = require('jquery')

Vue.use(InertiaApp)
Vue.mixin({
    methods: {
        route: (name, params, absolute) => route(name, params, absolute, Ziggy),
    },
});

const app = document.getElementById('app')

new Vue({
    render: h => h(InertiaApp, {
        props: {
            initialPage: JSON.parse(app.dataset.page),
            resolveComponent: name => require(`./Pages/${name}`).default,
        },
    }),
}).$mount(app)
