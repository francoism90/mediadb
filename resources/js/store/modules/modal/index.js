import actions from './actions'
import getters from './getters'
import mutations from './mutations'

export default {
  namespaced: true,
  state () {
    return {
      active: false,
      escape: ['escape', 'x', 'outside'],
      component: null,
      props: {},
      class: null,
      fullscreen: false,
      animation: 'fade',
      role: null,
      modalCard: false,
      width: 960
    }
  },
  mutations,
  actions,
  getters
}
