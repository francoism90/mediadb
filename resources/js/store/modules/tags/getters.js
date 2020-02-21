const getData = (state) => {
  return state.data
}

const getMeta = (state) => {
  return state.meta
}

const getFiltered = (state) => {
  return state.filtered
}

const getSelected = (state) => {
  return state.selected
}

export default {
  getData,
  getMeta,
  getFiltered,
  getSelected
}
