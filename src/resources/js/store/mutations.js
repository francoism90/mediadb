import Vue from 'vue'

export default {
  destroyPaginates (state) {
    state.paginateData = Object.assign({}, state.paginateData, {})
    state.paginateMeta = Object.assign({}, state.paginateMeta, {})
    state.paginateParams = Object.assign({}, state.paginateParams, {})
  },

  increasePaginate (state) {
    state.paginateParams[state.paginateId].page++
  },

  resetPaginate (state) {
    Vue.set(state.paginateData, state.paginateId, [])
    Vue.set(state.paginateMeta, state.paginateId, {})

    state.paginateParams[state.paginateId].page = 1
  },

  resetPaginateId (state, id) {
    Vue.set(state.paginateData, id, [])
    Vue.set(state.paginateMeta, id, {})
    Vue.set(state.paginateParams, id, {})
  },

  setPaginateId (state, value) {
    state.paginateId = value
  },

  setPaginateParams (state, payload) {
    const paginateParams = state.paginateParams[state.paginateId] || {}

    const params = { ...paginateParams, ...payload }

    Vue.set(state.paginateParams, state.paginateId, params)
  },

  setPaginateData (state, payload) {
    let { data, meta } = payload

    const paginateData = state.paginateData[state.paginateId]

    if (paginateData) {
      data = paginateData.concat(data)
    }

    Vue.set(state.paginateData, state.paginateId, data)
    Vue.set(state.paginateMeta, state.paginateId, meta)
  }
}
