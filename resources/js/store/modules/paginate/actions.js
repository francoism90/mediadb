import Vue from 'vue'

const create = ({ commit, dispatch, state }, route = {}) => {
  const { id = null } = route

  if (state.path === null || state.id !== id) {
    dispatch('reset', route)
  }

  commit('setInitialized', true)
}

const fetch = async ({ commit, state }) => {
  if (state.meta && state.meta.current_page >= state.meta.last_page) {
    return { meta: state.meta, data: state.data }
  }

  commit('setLoading', true)

  const response = await Vue.axios.get(state.path, { params: state.params })

  commit('setItems', response.data)
  commit('increasePage')
  commit('setLoading', false)
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
