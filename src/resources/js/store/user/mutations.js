export default {
  setUser (state, payload) {
    state.data = Object.assign({}, this.data, payload.data)
    state.meta = Object.assign({}, this.meta, payload.meta)
  }
}
