import Vue from 'vue'

const fetch = ({ commit, dispatch }, route = {}) => {
  commit('resetState')
  commit('setApiRoute', route)

  // Get model data
  dispatch('refresh')
}

const refresh = async ({ commit, state }) => {
  const response = await Vue.axios.get(state.path, { params: state.params })

  commit('setModel', response.data)
}

const remove = async ({ state }, params = {}) => {
  const { path = null, body = {} } = params

  const response = await Vue.axios.delete(path, body)

  return response
}

const update = async ({ state }, params = {}) => {
  const { path = null, body = {} } = params

  const response = await Vue.axios.put(path, body)

  return response
}

export default {
  fetch,
  refresh,
  remove,
  update
}
