import actions from './actions'
import getters from './getters'
import mutations from './mutations'

export default {
  namespaced: true,
  state () {
    return {
      item: {},
      callback: {},
      source: null,
      type: 'manifest',
      autoplay: false,
      buffered: null,
      currentTime: 0,
      duration: 0,
      fullscreen: false,
      height: '100%',
      width: '100%',
      muted: false,
      playing: false,
      poster: null,
      preload: 'metadata'
    }
  },
  mutations,
  actions,
  getters
}
