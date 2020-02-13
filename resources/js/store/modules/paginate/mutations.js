export default {
  setInitialized (state, payload) {
    state.ready = payload
  },

  setApiRoute (state, payload) {
    const { path = null, params = {} } = payload

    const currentParams = state.params || {}
    const finalParams = { ...currentParams, ...params }

    state.path = path || state.path
    state.params = finalParams
  },

  resetItems (state) {
    state.data = []
    state.meta = {}
  },

  setItems (state, payload) {
    const { data = [], meta = {} } = payload

    if (meta) {
      state.meta = meta

      if (data.length && meta.current_page <= meta.last_page) {
        const moduleData = state.data.concat(data)

        state.data = moduleData
      }
    }
  },

  resetPage (state) {
    state.params['page[number]'] = 1
  },

  increasePage (state) {
    state.params['page[number]']++
  }
}
