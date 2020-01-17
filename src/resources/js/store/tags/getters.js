import { filter } from 'lodash'

export default {
  active: (state) => {
    return state.active
  },

  type: (state) => (key) => {
    return filter(state.data, ['type', key])
  }
}
