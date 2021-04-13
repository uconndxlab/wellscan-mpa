import Vue from 'vue'
import VueRouter from 'vue-router'

import Home from '../views/Home.vue'
import Login from '../views/Login.vue';

import Inventory from '../views/Inventory.vue'
import SavedSnapshots from '../views/SavedSnapshots.vue'

import SingleReport from '../views/SingleReport.vue'
import SingleFood from '../views/SingleFood.vue'

Vue.use(VueRouter)

const routes = [
  {
    path: '/',
    name: 'Recently Scanned',
    component: Home
  },
  {
    path: '/about',
    name: 'About',
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () => import(/* webpackChunkName: "about" */ '../views/About.vue')
  },

  {
    path: '/savedSnaps',
    name: 'Saved Snapshots',
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: SavedSnapshots
  },

  {
    path: '/singleReport/:reportId',
    name: 'Single Snapshot',
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    props:true,
    component: SingleReport
  },

  {
    path: '/inventory',
    name: 'Inventory',
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: Inventory
  },
  {
    path:'/login',
    name:'Login',
    component:Login
  },

  {
    path: '/singleFood/:inventoryId',
    name: 'Single Food',
    component:SingleFood,
    props:true
  }
]

const router = new VueRouter({
  routes
})

export default router
