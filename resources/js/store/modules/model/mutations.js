import Vue from 'vue'
import defaultState from './state'

export default {
  resetState (state) {
    state = Object.assign(state, defaultState())
  },

  setApiRoute (state, payload) {
    const { path = null, params = {} } = payload

    const currentParams = state.params || {}
    const finalParams = { ...currentParams, ...params }

    state.path = path || state.path
    state.params = finalParams
  },

  setModel (state, payload) {
    const { data = {}, meta = {} } = payload

    Vue.set(state, 'data', data)
    Vue.set(state, 'meta', meta)
  }
}
