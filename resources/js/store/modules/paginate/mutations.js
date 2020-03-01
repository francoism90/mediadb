import Vue from 'vue'
import { find, uniqBy } from 'lodash'

export default {
  setInitialized (state, payload) {
    state.ready = payload
  },

  setApiRoute (state, payload) {
    const { id = null, path = null, params = {} } = payload

    const currentParams = state.params || {}
    const finalParams = { ...currentParams, ...params }

    state.id = id || state.id
    state.path = path || state.path
    state.params = finalParams
  },

  setLoading (state, payload) {
    state.loading = payload
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
  },

  setSelected (state, entries) {
    const items = []

    for (const entry of entries) {
      const pushItem = typeof entry === 'string' ? { id: entry, name: entry } : entry

      items.push(pushItem)
    }

    Vue.set(state, 'selected', items.length ? uniqBy(items, 'id') : [])
  }
}
