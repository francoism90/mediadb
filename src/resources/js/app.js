import Vue from 'vue'

import Approx from 'approximate-number'
import Axios from 'axios'
import Buefy from 'buefy'
import InfiniteLoading from 'vue-infinite-loading'
import Moment from 'moment'
import VueAuth from '@websanova/vue-auth'
import VueDebounce from 'vue-debounce'
import VueMeta from 'vue-meta'
import VueRouter from 'vue-router'

import App from './App.vue'
import routes from './routes'
import store from './store'

/**
 * Disable production tip
 */

Vue.config.productionTip = false

/**
 * Create axios instance
 */

const token = document.head.querySelector('meta[name="csrf-token"]')

Vue.axios = Axios.create({
  baseURL: '/api/',
  headers: {
    'X-Requested-With': 'XMLHttpRequest',
    'X-CSRF-TOKEN': token.content || ''
  }
})

/**
 * Create the router instance
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

Vue.router = router

/**
 * Register global event bus
 */
Vue.prototype.$eventHub = new Vue()

/**
 * Register Plugins
 */

Vue.use(Buefy)
Vue.use(VueDebounce)

Vue.use(VueMeta, {
  refreshOnceOnNavigation: true
})

Vue.use(InfiniteLoading, {
  props: {
    spinner: 'waveDots'
  },
  slots: {
    noMore: '',
    noResults: ''
  }
})

/**
 * Register UI components
 */

require('typeface-roboto')
require('typeface-noto-sans')

/**
 * Register Filters
 */

Vue.filter('approximate', function (value) {
  return Approx(value)
})

Vue.filter('timestamp', function (value) {
  return Moment
    .utc(value * 1000)
    .format('HH:mm:ss')
    .replace(/^0(?:0:0?)?/, '')
})

Vue.filter('datestamp', function (value) {
  return Moment(value).format('D MMMM Y')
})

/**
 * Create the Vue application instance
 */

Vue.use(VueAuth, {
  auth: require('@websanova/vue-auth/drivers/auth/bearer.js'),
  http: require('@websanova/vue-auth/drivers/http/axios.1.x.js'),
  router: require('@websanova/vue-auth/drivers/router/vue-router.2.x.js')
})

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  store,
  render: h => h(App)
})
