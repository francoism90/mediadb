export default {
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
