import actions from './actions'
import getters from './getters'
import mutations from './mutations'

export default {
  namespaced: true,
  state () {
    return {
      ready: false,
      loading: false,
      id: null,
      path: null,
      params: {
        'page[number]': 1,
        'page[size]': 9
      },
      data: [],
      meta: {},
      selected: []
    }
  },
  mutations,
  actions,
  getters
}
