import uniqBy from 'lodash/uniqBy'

export default {
  resetTags (state) {
    state.data = []
    state.meta = {}
  },

  setTags (state, payload) {
    state.data = Object.assign([], this.data, payload.data)
    state.meta = Object.assign({}, this.meta, payload.meta)
  },

  setActiveTags (state, tags) {
    state.active = uniqBy(tags, 'id')
  }
}
