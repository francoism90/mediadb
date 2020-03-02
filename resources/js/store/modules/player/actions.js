import Vue from 'vue'

const create = ({ commit }, params = {}) => {
  commit('resetState')
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

const thumbnail = async ({ commit, state }, time = 1000) => {
  const response = await Vue.axios.get(`asset/thumbnail/${state.item.id}/${time}`)
  const { meta = {} } = response.data

  commit('setMedia', { thumbnail: meta.thumbnail })
}

export default {
  callback,
  create,
  push,
  thumbnail
}
