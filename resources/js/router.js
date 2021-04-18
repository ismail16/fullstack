import Vue from 'vue'
import Router from 'vue-router'
Vue.use(Router)

import fistPage from './components/pages/MyFirstVuePage.vue'
import NewRoutePage from './components/pages/NewRoutePage.vue'
import hooks from './components/pages/basic/hooks.vue'

//Project pages
import home from './components/pages/home.vue'
import tags from './admin/pages/tags.vue'
import category from './admin/pages/category.vue'

const routes = [

    {
        path: '/',
        component: home
    },

    {
        path: '/tags',
        component: tags
    },

    {
        path: '/category',
        component: category
    },











    {
        path: '/my-route',
        component: fistPage
    },


    
    {
        path: '/my-route',
        component: fistPage
    },
    {
        path: '/new-route',
        component: NewRoutePage
    },

    {
        path: '/hooks',
        component: hooks
    }
]

export default new Router({
    mode: 'history',
    routes
})