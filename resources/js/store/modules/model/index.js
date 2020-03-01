import actions from './actions'
import getters from './getters'
import mutations from './mutations'

export default {
  namespaced: true,
  state () {
    return {
      path: null,
      params: {},
      data: {},
      meta: {}
    }
  },
  mutations,
  actions,
  getters
}
