const open = ({ commit }, params = {}) => {
  commit('setModal', params)
  commit('setActive', true)
}

const close = ({ commit }) => {
  commit('setActive', false)
}

export default {
  open,
  close
}
