import Vue from 'vue'
import Vuex from 'vuex'

import actions from './actions'
import getters from './getters'
import mutations from './mutations'
import state from './state'

Vue.use(Vuex)

const modules = {
  //
}

const store = new Vuex.Store({
  modules,
  actions,
  getters,
  mutations,
  state
})

export default store
