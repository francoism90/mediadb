const getUser = (state) => {
  return state.data
}

const isAuthenticated = (state) => {
  return (state.data && typeof state.data.id !== 'undefined')
}

const isReady = (state) => {
  return state.ready
}

export default {
  getUser,
  isAuthenticated,
  isReady
}
