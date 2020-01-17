import Vue from 'vue'

export default {
  async fetch ({ state }, params = {}) {
    const response = await Vue.axios.get('media', {
      params: {
        include: params.include || null,
        'filter[collection]': params.collection || null,
        'filter[random]': params.random || null,
        'filter[related]': params.related || null,
        'filter[query]': params.query || null,
        'filter[tags]': Array.isArray(params.tags) ? params.tags.join() : null || null,
        sort: params.sort || null,
        'page[number]': params.page || 1,
        'page[size]': params.size || 8
      }
    })

    return response.data
  },

  async get ({ commit, state }, id) {
    if (state.data.id !== id) {
      commit('resetPaginateId', 'related', { root: true })
      commit('setMedia', { data: {}, meta: {} })
    }

    const response = await Vue.axios.get('media/' + id)

    commit('setMedia', response.data)
  },

  async update ({ commit }, model) {
    const response = await Vue.axios.put('media/' + model.id, model)

    commit('destroyPaginates', null, { root: true })

    return response.data
  },

  async delete ({ commit }, id) {
    const response = await Vue.axios.delete('media/' + id)

    commit('destroyPaginates', null, { root: true })

    return response.data
  }
}
