export default {
  paginate: (state) => (id) => {
    return state.paginate[id] || false
  }
}
