import Vue from 'vue'
import defaultState from './state'

export default {
  resetState (state) {
    state = Object.assign(state, defaultState())
  },

  setReady (state, payload) {
    state.ready = payload
  },

  setUser (state, payload) {
    const { data = {} } = payload

    // Set user data
    Vue.set(state, 'data', data)
  }
}
