import Vue from 'vue'
import { isArray, isInteger, isString } from 'lodash'

export default {
  async fetch ({ rootState }, params = {}) {
    const response = await Vue.axios.get('media', {
      params: {
        include: isString(params.include) ? params.include : null,
        'filter[related]': isString(params.related) ? params.related : null,
        'filter[query]': isString(params.query) ? params.query || 'null' : null,
        'filter[tags]': isArray(params.tags) ? params.tags.join() : null,
        sort: isString(params.sort) ? params.sort : null,
        'page[number]': isInteger(params.page) ? params.page : 1,
        'page[size]': isInteger(params.size) ? params.size : 9
      }
    })

    return response.data
  },

  async find ({ state }, id) {
    const response = await Vue.axios.get('media/' + id)

    return response.data
  },

  async get ({ commit, dispatch, state }, id) {
    if (state.data.id !== id) {
      commit('destroyPaginate', 'related', { root: true })
      commit('setMedia', { data: {}, meta: {} })
    }

    const response = await dispatch('find', id)

    commit('setMedia', response)

    return response
  },

  async update ({ dispatch }, model) {
    const response = await Vue.axios.put('media/' + model.id, model)

    dispatch('resetPaginates', null, { root: true })

    return response
  },

  async delete ({ dispatch }, id) {
    const response = await Vue.axios.delete('media/' + id)

    dispatch('resetPaginates', null, { root: true })

    return response
  }
}
