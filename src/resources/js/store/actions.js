export default {
  async createPaginate ({ commit, state }, payload) {
    const { id } = payload

    if (state.paginate[id] === undefined) {
      commit('setPaginate', payload)
    }
  },

  async resetPaginate ({ commit, state }, payload) {
    const { id } = payload

    if (state.paginate[id] !== undefined) {
      payload.props.initialized = new Date()
      payload.props.page = 1

      commit('resetPaginate', id)
      commit('setPaginate', payload)
    }
  },

  async resetPaginates ({ dispatch, state }) {
    for (const key of Object.keys(state.paginate)) {
      dispatch('resetPaginate', { id: key, props: {} })
    }
  },

  async paginate ({ commit, dispatch, getters }, id) {
    const paginate = getters.paginate(id)

    // Check paginate limits
    if (
      (paginate.meta.current_page > 0 && paginate.meta.last_page > 0) &&
      (paginate.meta.current_page >= paginate.meta.last_page)
    ) {
      return { meta: null }
    }

    const response = await dispatch(
      paginate.props.dispatcher, paginate.props, { root: true }
    )

    const { data, meta } = response

    // Fail-save paginate limit check
    if (meta.current_page > meta.last_page) {
      return { meta: null }
    }

    // Return as valid metadata
    commit('setPaginate', {
      id: id, data: data, meta: meta, props: { page: paginate.props.page + 1 }
    })

    return response
  },

  async destroyModal ({ commit }) {
    commit('setModal', { active: false })
  },

  async modal ({ commit }, payload) {
    commit('setModal', payload)
  }
}
