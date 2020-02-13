const getShakaOptions = () => {
  return {
    bufferingGoal: 30,
    jumpLargeGaps: true,
    rebufferingGoal: 15
  }
}

const getKeyBindings = () => {
  return {
    contextMenu: ['c'],
    snapshot: ['s'],
    togglePlay: ['space'],
    toggleFullscreen: ['f']
  }
}

export default {
  getShakaOptions,
  getKeyBindings
}
