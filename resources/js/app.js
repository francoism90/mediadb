import Vue from 'vue'

import Approx from 'approximate-number'
import Axios from 'axios'
import BackToTop from 'vue-backtotop'
import Buefy from 'buefy'
import InfiniteLoading from 'vue-infinite-loading'
import Moment from 'moment'
import Shortkey from 'vue-shortkey'
import Vue2TouchEvents from 'vue2-touch-events'
import VueAuth from '@websanova/vue-auth'
import VueMeta from 'vue-meta'
import VueRouter from 'vue-router'
import VueTimers from 'vue-timers'

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
 * Register plugins
 */

Vue.use(Buefy, {
  defaultNoticeQueue: false,
  defaultTrapFocus: true
})

Vue.use(BackToTop)
Vue.use(VueTimers)

Vue.use(Vue2TouchEvents, {
  touchHoldTolerance: 300,
  longTapTimeInterval: 300
})

Vue.use(InfiniteLoading, {
  props: { spinner: 'spiral' }
})

Vue.use(VueMeta, {
  refreshOnceOnNavigation: true
})

Vue.use(Shortkey, {
  prevent: ['input', 'textarea']
})

/**
 * Register filters
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
