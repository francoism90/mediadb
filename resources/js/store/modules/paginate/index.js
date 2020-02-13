import actions from './actions'
import getters from './getters'
import mutations from './mutations'

export default {
  namespaced: true,
  state () {
    return {
      ready: false,
      data: [],
      meta: {},
      path: null,
      params: {
        'page[number]': 1,
        'page[size]': 9
      }
    }
  },
  mutations,
  actions,
  getters
}
