export const fullscreenHandler = {
  data () {
    return {
      isFullscreen: false,
      fullscreenListeners: [
        'fullscreenchange',
        'mozfullscreenchange',
        'MSFullscreenChange',
        'webkitfullscreenchange'
      ]
    }
  },

  computed: {
    element () {
      return this.$refs.fullscreen
    }
  },

  mounted () {
    for (const fullscreenListener of this.fullscreenListeners) {
      document.addEventListener(fullscreenListener, this.fullscreenChange)
    }
  },

  beforeDestroy () {
    for (const fullscreenListener of this.fullscreenListeners) {
      document.removeEventListener(fullscreenListener, this.fullscreenChange)
    }
  },

  methods: {
    fullscreenChange (event) {
      this.isFullscreen = this.fullscreenStatus()

      return event
    },

    toggleFullscreen () {
      if (this.isFullscreen) {
        return this.exitFullscreen()
      }

      return this.requestFullscreen()
    },

    fullscreenStatus () {
      if (
        document.fullscreen ||
        document.mozFullScreen ||
        document.fullscreenElement ||
        document.msFullscreenElement ||
        document.webkitIsFullScreen
      ) {
        return true
      } else {
        return false
      }
    },

    requestFullscreen () {
      if (this.element.requestFullscreen) {
        this.element.requestFullscreen()
      } else if (this.element.webkitRequestFullscreen) {
        this.element.webkitRequestFullscreen()
      } else if (this.element.mozRequestFullScreen) {
        this.element.mozRequestFullScreen()
      } else if (this.element.msRequestFullscreen) {
        this.element.msRequestFullscreen()
      } else {
        console.error('Fullscreen API is not supported.')
      }
    },

    exitFullscreen () {
      if (document.exitFullscreen) {
        document.exitFullscreen()
      } else if (document.webkitExitFullscreen) {
        document.webkitExitFullscreen()
      } else if (document.mozCancelFullScreen) {
        document.mozCancelFullScreen()
      } else if (document.msExitFullscreen) {
        document.msExitFullscreen()
      } else {
        console.error('Fullscreen API is not supported.')
      }
    }
  }
}
