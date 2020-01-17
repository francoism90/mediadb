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
      }
    }
  },

  mounted () {
    if (Player.isBrowserSupported()) {
      this.initPlayer()
    } else {
      alert('Browser is not supported')
    }
  },

  beforeDestroy () {
    this.mediaSource.unload()
    this.mediaSource.destroy()

    this.$eventHub.$off(this.id)
  },

  methods: {
    async initPlayer () {
      try {
        this.mediaSource = new Player(this.player)
        this.mediaSource.configure(this.playerConfig)

        await this.mediaSource.load(this.options.manifest)
      } catch (e) {
        console.error(e)
      }
    },

    playerEvent () {
      this.$eventHub.$emit(this.id, this.player)
    },

    autoplay () {
      if (this.options.autoplay && this.options.autoplay === false) {
        return this.player.pause()
      }

      return this.player.play()
    },

    togglePlay () {
      if (this.player.paused === true) {
        return this.player.play()
      }

      return this.player.pause()
    }
  }
}
