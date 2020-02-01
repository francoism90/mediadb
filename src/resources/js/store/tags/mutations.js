import Vue from 'vue'

export default {
  setTags (state, payload) {
    const { data, meta } = payload

    Vue.set(state, 'data', data)
    Vue.set(state, 'meta', meta)
  }
}
