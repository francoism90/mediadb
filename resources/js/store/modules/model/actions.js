import Vue from 'vue'

const fetch = async ({ commit }, params = {}) => {
  const { path = null, routeParams = {} } = params

  commit('setInitialized', false)

  const response = await Vue.axios.get(path, routeParams)

  commit('setItem', response.data)
  commit('setInitialized', true)
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
  remove,
  update
}
