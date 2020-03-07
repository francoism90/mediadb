import App from './App.vue'
import Axios from 'axios'
import Vue from 'vue'
import VueRouter from 'vue-router'

import routes from './routes'
import store from './store'

/**
 * Disable production tip
 */

Vue.config.productionTip = false

/**
 * ui
 */

require('./ui')

/**
 * Axios
 */

Vue.axios = Axios.create({
  baseURL: '/api/',
  withCredentials: true,
  headers: {
    'X-Requested-With': 'XMLHttpRequest'
  }
})

/**
 * VueRouter
 */

Vue.use(VueRouter)

const router = new VueRouter({
  linkActiveClass: 'is-active',
  mode: 'history',
  routes: routes,
  scrollBehavior (to, from, savedPosition) {
    return savedPosition || { x: 0, y: 0 }
  }
})

router.beforeEach(async (to, from, next) => {
  // Fetch user info
  await store.dispatch('user/fetch')

  // Needs auth
  if (to.matched.some(record => record.meta.auth)) {
    const isAuthenticated = store.getters['user/isAuthenticated']

    if (!isAuthenticated) {
      next({ name: 'login', query: { redirect: to.fullPath } })
    } else {
      next()
    }
  } else {
    next()
  }
})

/**
 * Create the Vue application instance
 */

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  store,
  render: h => h(App)
})
