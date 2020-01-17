import Vue from 'vue'

export default {
  async get ({ commit, state }, id) {
    if (state.data.id !== id) {
      commit('setUser', { data: {}, meta: {} })
    }

    const response = await Vue.axios.get('user/' + id)

    commit('setUser', response.data)
  }
}
