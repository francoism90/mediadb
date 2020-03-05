import Vue from 'vue'
import Vuex from 'vuex'

import actions from './actions'
import getters from './getters'
import mutations from './mutations'
import state from './state'

import modal from './modules/modal'
import model from './modules/model'

Vue.use(Vuex)

const modules = {
  modal,
  model
}

const store = new Vuex.Store({
  modules,
  actions,
  getters,
  mutations,
  state
})

export default store
