import get from 'lodash/get'

export default {
  paginateData: (state) => {
    return get(state.paginateData, state.paginateId, [])
  },

  paginateMeta: (state) => {
    return get(state.paginateMeta, state.paginateId, {})
  },

  paginateParams: (state) => {
    return get(state.paginateParams, state.paginateId, {})
  }
}
