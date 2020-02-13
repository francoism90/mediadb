export default {
  setActive (state, payload) {
    state.active = payload
  },

  setModal (state, payload) {
    const mergeState = { ...state, ...payload }

    for (const [key, value] of Object.entries(mergeState)) {
      state[key] = value
    }
  }
}
