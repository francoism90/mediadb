import Vue from 'vue'

export default {
  setInitialized (state, payload) {
    state.ready = payload
  },

  setItem (state, payload) {
    const { data = {}, meta = {} } = payload

    Vue.set(state, 'data', data)
    Vue.set(state, 'meta', meta)
  }
}
