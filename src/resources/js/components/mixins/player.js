import { Player } from 'shaka-player'

export const playerHandler = {
  props: {
    id: {
      type: String,
      required: true
    },

    options: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      mediaSource: null,
      playerConfig: {
        streaming: {
          bufferingGoal: 30,
          jumpLargeGaps: true,
          rebufferingGoal: 15
        }
      },
      playerShortkeys: {
        'create-thumbnail': ['s'],
        'toggle-play': ['space']
      }
    }
  },

  computed: {
    player () {
      return this.$refs.videoElement || null
    },

    source () {
      return this.$refs.videoSource || null
    },

    poster () {
      return this.options.poster || ''
    },

    preload () {
      return this.options.preload || 'metadata'
    },

    ratio () {
      return {
        width: this.options.width ? this.options.width + 'px' : '100%',
        height: this.options.height ? this.options.height + 'px' : '100%'
      }
    },

    isPlayerReady () {
      return this.player.readyState > 2
    }
  },

  mounted () {
    if (this.player && this.options.manifest) {
      this.initPlayer()
    }

    this.playerEventListener()
  },

  async beforeDestroy () {
    if (this.mediaSource) {
      await this.mediaSource.detach()
      await this.mediaSource.destroy()
    }

    if (this.options.source) {
      this.player.pause()
      this.player.removeAttribute('src')

      this.source.removeAttribute('src')
      this.source.removeAttribute('type')

      this.player.remove()
    }

    this.$eventHub.$off(this.id)
  },

  methods: {
    async initPlayer () {
      if (!Player.isBrowserSupported()) {
        alert('Browser is not supported')
      }

      try {
        this.mediaSource = new Player(this.player)
        this.mediaSource.configure(this.playerConfig)

        await this.mediaSource.load(this.options.manifest)
      } catch (e) {
        console.error(e)
      }
    },

    playerCallback (event) {
      if (event.srcKey && this.options.disableKeymap === true) {
        return
      }

      this.$eventHub.$emit(this.id, event.srcKey ? event.srcKey : event)
    },

    playerEvent () {
      this.playerCallback({
        buffered: this.player.buffered,
        current: this.player.currentTime,
        duration: this.player.duration,
        muted: this.player.muted,
        paused: this.player.paused,
        readyState: this.player.readyState
      })
    },

    playerEventListener () {
      this.$eventHub.$on(this.id, (event) => {
        try {
          const key = typeof event === 'object' ? event.key : event

          switch (key) {
            case 'toggle-play':
              this.togglePlay()
              break

            case 'toggle-fullscreen':
              this.toggleFullscreen()
              break

            case 'fast-rewind':
              this.player.currentTime -= 10
              break

            case 'fast-foward':
              this.player.currentTime += 10
              break

            case 'current-time':
              this.player.currentTime = event.time
              break

            case 'create-thumbnail':
              this.$store.dispatch('media/update', {
                id: this.id,
                snapshot: this.player.currentTime
              })

              this.$buefy.notification.open({
                duration: 5000,
                message: 'The thumbnail will be updated as soon as possible!',
                type: 'is-success',
                position: 'is-top',
                queue: false
              })
              break

            case 'download':
              this.player.pause()

              window.location.href = this.options.download || ''
              break
          }
        } catch (e) {}
      })
    },

    playable () {
      if (this.options.autoplay === true) {
        this.player.play()
      } else {
        this.player.pause()
      }
    },

    togglePlay () {
      if (this.isPlayerReady && this.player.paused === true) {
        this.player.play()
      } else {
        this.player.pause()
      }
    }
  }
}
