import Vue from 'vue'

export default {
  async all ({ commit, dispatch }, module = null) {
    const response = await dispatch('fetch')

    commit('setTags', response)

    return response
  },

  async fetch ({ state }, params = {}) {
    const response = await Vue.axios.get('tag', {
      params: {
        include: params.include || null,
        'filter[feed]': params.feed || null,
        'filter[query]': params.query || null,
        sort: params.sort || null,
        'page[number]': params.page || null,
        'page[size]': params.size || 0
      }
    })

    return response.data
  },

  async filtered ({ commit, dispatch, rootState }, id) {
    const needsDataId = ['collection', 'user']

    const response = await dispatch('fetch', {
      [id]: needsDataId.includes(id) ? rootState[id].data.id : true
    })

    commit('setTags', response)

    return response
  }
}
