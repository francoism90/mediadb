import Vue from 'vue'

export default {
  setModal (state, payload) {
    Vue.set(state, 'modal', payload)
  },

  destroyPaginate (state, id) {
    delete state.paginate[id]
  },

  resetPaginate (state, id) {
    Vue.set(state.paginate[id], 'data', [])
    Vue.set(state.paginate[id], 'meta', {})
  },

  setPaginate (state, payload) {
    const { id, props = {}, data = [], meta = {} } = payload

    if (state.paginate[id] === undefined) {
      state.paginate[id] = Object.assign({}, state.paginate[id], state.paginateDefaults)
    }

    // Merge props with current module (if any)
    if (Object.entries(props).length) {
      const currentModule = state.paginate[id].props
      const moduleProps = { ...currentModule, ...props }

      Vue.set(state.paginate[id], 'props', moduleProps)
    }

    // Set metadata if exists
    if (meta) {
      Vue.set(state.paginate[id], 'meta', meta)
    }

    // Merge data with current (if any)
    if (data.length) {
      const currentData = state.paginate[id].data
      const paginateData = currentData.concat(data)

      Vue.set(state.paginate[id], 'data', paginateData)
    }
  }
}
