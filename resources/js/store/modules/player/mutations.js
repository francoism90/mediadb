import defaultState from './state'

export default {
  resetState (state) {
    state = Object.assign(state, defaultState())
  },

  setMedia (state, payload) {
    const mergeState = { ...state, ...payload }

    for (const [key, value] of Object.entries(mergeState)) {
      state[key] = value
    }
  },

  setCallback (state, payload) {
    state.callback = payload
  }
}
