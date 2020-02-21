import { defaultState } from './helpers'

export default {
  resetModal (state) {
    state = Object.assign({}, state, defaultState)
  },

  setActive (state, payload) {
    state.active = payload
  },

  setModal (state, payload) {
    for (const [key, value] of Object.entries(payload)) {
      state[key] = value
    }
  }
}
