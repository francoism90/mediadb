<template lang="pug">
video(
  ref="videoElement"
  muted
  preload="none"
  crossorigin
  playsinline
  loop
  :poster="thumbnail"
  @contextmenu.prevent
  @mouseover="togglePlay()"
  @mouseout="togglePlay()"
  @mousedown="togglePlay()"
  @mouseup="togglePlay()"
)
  source(ref="videoSource" :src="animation" :type="animationType")
</template>

<script>
export default {
  props: {
    animation: {
      type: String,
      default: null
    },

    animationType: {
      type: String,
      default: null
    },

    thumbnail: {
      type: String,
      required: true
    }
  },

  computed: {
    player () {
      return this.$refs.videoElement
    },

    source () {
      return this.$refs.videoSource
    }
  },

  beforeDestroy () {
    this.player.pause()
    this.player.removeAttribute('src')

    this.source.removeAttribute('src')
    this.source.removeAttribute('type')

    this.player.remove()
  },

  methods: {
    togglePlay () {
      if (this.player.paused) {
        return this.player.play()
      }

      return this.player.pause()
    }
  }
}
</script>
