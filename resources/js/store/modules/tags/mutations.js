import Vue from 'vue'
import uniqBy from 'lodash/uniqBy'

export default {
  resetItems (state) {
    state.data = []
    state.meta = {}
  },

  setItems (state, payload) {
    const { data = [], meta = {} } = payload

    Vue.set(state, 'data', data)
    Vue.set(state, 'meta', meta)
    Vue.set(state, 'filtered', data)
  },

  setFiltered (state, payload = null) {
    state.filtered = state.data.filter((option) => {
      return option.name
        .toString()
        .toLowerCase()
        .indexOf(payload.toLowerCase()) >= 0
    })
  },

  setSelected (state, payload) {
    const tags = payload.length ? uniqBy(payload, 'id') : []

    Vue.set(state, 'selected', tags)
  }
}
