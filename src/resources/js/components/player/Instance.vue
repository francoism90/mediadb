<template lang="pug">
main(v-if="options.manifest" :key="id" ref="fullscreen" @mousemove="showHideControls()" @mouseleave="showHideControls()" :style="{ cursor: mouseActive ? 'auto' : 'none' }" class="player" :class="{ 'is-fullscreen': isFullscreen }")
  video(
    ref="videoElement"
    playsinline
    :poster="options.poster || ''"
    :style="{ width: options.width + 'px' || '1280px' }"
    @canplay="autoplay()"
    @click="togglePlay()"
    @dblclick="toggleFullscreen()"
    @ended="playerEvent()"
    @loadedmetadata="playerEvent()"
    @pause="playerEvent()"
    @play="playerEvent()"
    @progress="playerEvent()"
    @timeupdate="playerEvent()"
  )

  transition(name="fade")
    keep-alive
      play(v-show="mouseActive" :id="id")
      controls(v-show="mouseActive" :id="id")
</template>

<script>
import { fullscreenHandler, playerHandler } from '@/components/mixins'

export default {
  components: {
    Controls: () => import(/* webpackChunkName: "player-controls" */ '@/components/player/Controls')
  },

  mixins: [fullscreenHandler, playerHandler],

  data () {
    return {
      mouseActive: true,
      mouseTimer: null
    }
  },

  computed: {
    player () {
      return this.$refs.videoElement
    }
  },

  mounted () {
    this.eventListener()
  },

  methods: {
    showHideControls () {
      clearTimeout(this.mouseTimer)

      this.mouseActive = true

      if (this.options.keepControls) {
        return
      }

      this.mouseTimer = setTimeout(() => {
        this.mouseActive = false
      }, this.options.transactionTime || 2500)
    },

    eventListener () {
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

              window.location.href = this.options.download
              break
          }
        } catch (e) {}
      })
    }
  }
}
</script>
