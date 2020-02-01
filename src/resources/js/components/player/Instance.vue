<template lang="pug">
main(v-if="id" :key="id" ref="fullscreen" @mousemove="showControls" @mouseleave="hideControls" :class="[customClass, fullscreenClass]" :style="styles" v-shortkey="playerShortkeys" @shortkey="playerCallback")
  video(
    ref="videoElement"
    playsinline
    crossorigin
    :poster="poster"
    :style="ratio"
    :preload="preload"
    @canplay="playable"
    @click="togglePlay"
    @dblclick="toggleFullscreen"
    @ended="playerEvent"
    @loadedmetadata="playerEvent"
    @pause="playerEvent"
    @play="playerEvent"
    @progress="playerEvent"
    @timeupdate="playerEvent"
  )

  transition(name="fade")
    keep-alive
      controls(v-show="controlsActive" :id="id" :elements="hasControls")
</template>

<script>
import { fullscreenHandler, playerHandler } from '@/components/mixins'

export default {
  timers: {
    hideControls: { time: 3000, autostart: true }
  },

  components: {
    Controls: () => import(/* webpackChunkName: "player-controls" */ '@/components/player/Controls')
  },

  mixins: [fullscreenHandler, playerHandler],

  props: {
    customClass: {
      type: Object,
      default: function () {
        return { player: true }
      }
    },

    hasControls: {
      type: Array,
      default: function () {
        return [
          'slider',
          'togglePlay',
          'fastRewind',
          'fastForward',
          'currentTime',
          'settings',
          'toggleFullscreen'
        ]
      }
    }
  },

  data () {
    return {
      controlsActive: true
    }
  },

  computed: {
    fullscreenClass () {
      return { 'is-fullscreen': this.isFullscreen }
    },

    styles () {
      return { cursor: this.controlsActive ? 'auto' : 'none' }
    }
  },

  methods: {
    hideControls () {
      this.controlsActive = false
    },

    showControls () {
      this.controlsActive = true
      this.$timer.restart('hideControls')
    }
  }
}
</script>
