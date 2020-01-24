import { filter, find } from 'lodash'

export default {
  active: (state) => {
    return state.active
  },

  type: (state) => (type) => {
    return filter(state.data, ['type', type])
  }
}
