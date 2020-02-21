import Vue from 'vue'

const fetch = async ({ commit }, route = {}) => {
  const { path = null, params = {} } = route

  const response = await Vue.axios.get(path, { params: params })

  commit('setItems', response.data)
}

export default {
  fetch
}
