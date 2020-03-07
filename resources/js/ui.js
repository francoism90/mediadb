import Approx from 'approximate-number'
import BackToTop from 'vue-backtotop'
import Buefy from 'buefy'
import InfiniteLoading from 'vue-infinite-loading'
import Moment from 'moment'
import Shortkey from 'vue-shortkey'
import Vue from 'vue'
import Vue2TouchEvents from 'vue2-touch-events'
import VueMeta from 'vue-meta'
import VueTimers from 'vue-timers'

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
  swipeTolerance: 50,
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
