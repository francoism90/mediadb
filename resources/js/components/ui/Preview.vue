<template lang="pug">
figure(class="image" @mouseover="togglePreview" @mouseout="togglePreview" v-touch:swipe.left="togglePreview")
  video(
    ref="videoElement"
    preload="auto"
    playsinline
    crossorigin
    loop
    muted
    disableRemotePlayback
    :poster="options.poster || ''"
    @contextmenu.prevent
  )
    source(ref="videoSource" :src="options.source" :type="options.mimetype")
</template>

<script>
import { playerCallbackHandler } from '@/components/mixins/player'

export default {
  mixins: [playerCallbackHandler],

  props: {
    options: {
      type: Object,
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
    togglePreview () {
      return this.playerCallback({ type: 'togglePlay' })
    }
  }
}
</script>
