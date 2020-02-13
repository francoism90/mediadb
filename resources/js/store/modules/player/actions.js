import Vue from 'vue'

const create = ({ commit }, params = {}) => {
  commit('setMedia', params)
}

const push = async ({ state }, params = {}) => {
  const { path = null, body = null } = params

  const response = await Vue.axios.put(path, body || state.item)

  return response
}

const callback = ({ commit }, params = {}) => {
  commit('setCallback', params)
}

export default {
  create,
  push,
  callback
}
