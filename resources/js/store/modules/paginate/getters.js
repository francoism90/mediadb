const getData = (state) => {
  return state.data
}

const getMeta = (state) => {
  return state.meta
}

const getSelected = (state) => {
  return state.selected
}

const isReady = (state) => {
  return state.ready
}

const isLoading = (state) => {
  return state.loading
}

export default {
  getData,
  getMeta,
  getSelected,
  isReady,
  isLoading
}
