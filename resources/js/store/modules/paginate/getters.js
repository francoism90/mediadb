const getData = (state) => {
  return state.data
}

const getMeta = (state) => {
  return state.meta
}

const isReady = (state) => {
  return state.ready
}

export default {
  getData,
  getMeta,
  isReady
}
