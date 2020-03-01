import Vue from 'vue'

export default {
  setApiRoute (state, payload) {
    const { path = null, params = {} } = payload

    const currentParams = state.params || {}
    const finalParams = { ...currentParams, ...params }

    state.path = path || state.path
    state.params = finalParams
  },

  setItem (state, payload) {
    const { data = {}, meta = {} } = payload

    Vue.set(state, 'data', data)
    Vue.set(state, 'meta', meta)
  }
}
