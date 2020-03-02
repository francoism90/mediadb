export const playerEventHandler = {
  data () {
    return {
      events: [
        'ended',
        'loadedmetadata',
        'pause',
        'play',
        'playing',
        'progress',
        'seeked',
        'seeking',
        'suspend',
        'timeupdate',
        'waiting'
      ]
    }
  },

  mounted () {
    if (this.player) {
      for (const event of this.events) {
        this.player.addEventListener(event, this.playerEvent)
      }
    }
  },

  beforeDestroy () {
    for (const event of this.events) {
      this.player.removeEventListener(event, this.playerEvent)
    }
  },

  methods: {
    playerEvent (event) {
      switch (event.type) {
        case 'loadedmetadata':
          this.setMedia({ duration: this.player.duration })
          break
        case 'progress':
          this.setMedia({ buffered: this.player.buffered })
          break
        case 'seeked':
        case 'seeking':
        case 'timeupdate':
          this.setMedia({ currentTime: this.player.currentTime })
          break
        case 'ended':
        case 'pause':
        case 'suspend':
        case 'waiting':
          this.setMedia({ playing: false })
          break
        case 'play':
        case 'playing':
          this.setMedia({ playing: true })
          break
      }
    }
  }
}

export const playerCallbackHandler = {
  methods: {
    playerCallback (payload) {
      switch (payload.type) {
        case 'togglePlay':
          if (this.player.readyState > 2 && this.player.paused === true) {
            this.player.play()
          } else {
            this.player.pause()
          }
          break
        case 'toggleFullscreen':
          this.toggleFullscreen()
          break
        case 'fastRewind':
          this.player.currentTime -= 10
          break
        case 'fastForward':
          this.player.currentTime += 10
          break
        case 'currentTime':
          this.player.currentTime = payload.value
          break
        case 'manager':
          this.player.pause()

          this.$store.dispatch('modal/open', {
            component: 'Media',
            class: 'manager',
            escape: ['escape'],
            fullscreen: true,
            props: { id: this.item.id }
          })
          break
        case 'download':
          this.player.pause()
          window.location.href = this.options.download || ''
          break
      }
    }
  }
}
