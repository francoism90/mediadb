import Vue from 'vue'

const create = ({ commit, state }, route = {}) => {
  commit('setInitialized', false)

  if (state.path === null) {
    commit('setApiRoute', route)
  }

  commit('setInitialized', true)
}

const fetch = async ({ commit, dispatch, state }, route = {}) => {
  if (Object.entries(route).length) {
    dispatch('create', route)
  }

  if (state.meta && state.meta.current_page >= state.meta.last_page) {
    return { meta: state.meta, data: state.data }
  }

  const response = await Vue.axios.get(state.path, { params: state.params })

  commit('setItems', response.data)
  commit('increasePage')
}

const reset = ({ commit }, route = {}) => {
  commit('setApiRoute', route)
  commit('resetItems')
  commit('resetPage')
}

export default {
  create,
  fetch,
  reset
}
