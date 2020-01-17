import Vue from 'vue'
import Vuex from 'vuex'

import actions from './actions'
import getters from './getters'
import mutations from './mutations'
import state from './state'

import media from './media'
import tags from './tags'
import user from './user'

Vue.use(Vuex)

const modules = {
  media,
  tags,
  user
}

const store = new Vuex.Store({
  modules,
  actions,
  getters,
  mutations,
  state
})

export default store
