export default {
  async paginate ({ commit, dispatch, getters, state }, dispatcher) {
    const params = getters.paginateParams

    if (!params.page) {
      commit('setPaginateParams', {
        page: 1
      })
    }

    if ((!params.query && !params.related) && !params.sort) {
      commit('setPaginateParams', {
        sort: state.paginateSorters[0].key
      })
    }

    const response = await dispatch(
      dispatcher, getters.paginateParams, { root: true }
    )

    commit('setPaginateData', response)
    commit('increasePaginate')

    return response
  }
}
