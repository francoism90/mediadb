<template lang="pug">
main(:key="item.id" ref="fullscreen" v-shortkey="getKeyBindings" @shortkey="keyHandler" @mousemove="showControls" @mouseleave="hideControls" :class="classes" :style="styles")
  video(
    ref="videoElement"
    playsinline
    crossorigin
    :poster="poster"
    :preload="preload"
    :muted="muted"
    :height="height"
    :width="width"
    @canplay="playerCallback({ type: 'togglePlay' })"
    @click.prevent="playerCallback({ type: 'togglePlay' })"
    @dblclick.prevent="toggleFullscreen"
  )

  transition(name="fade")
    keep-alive
      controls(v-show="controlsActive")
</template>

<script>
import playerModule from '@/store/modules/player'
import { fullscreenHandler } from '@/components/mixins/fullscreen'
import { formErrorHandler } from '@/components/mixins/form'
import { playerCallbackHandler, playerEventHandler } from '@/components/mixins/player'
import { contextHandler } from '@/components/mixins/model'
import { mapActions, mapState, mapGetters, mapMutations } from 'vuex'
import { Player } from 'shaka-player'

export default {
  timers: {
    hideControls: { time: 3000, autostart: true }
  },

  components: {
    Controls: () => import(/* webpackChunkName: "watch-controls" */ '@/components/watch/Controls')
  },

  mixins: [
    contextHandler,
    formErrorHandler,
    fullscreenHandler,
    playerCallbackHandler,
    playerEventHandler
  ],

  props: {
    options: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      media: null,
      controlsActive: true
    }
  },

  computed: {
    ...mapState('watch', [
      'item',
      'autoplay',
      'height',
      'muted',
      'poster',
      'preload',
      'source',
      'width'
    ]),

    ...mapGetters('watch', [
      'getShakaOptions',
      'getKeyBindings'
    ]),

    player () {
      return this.$refs.videoElement
    },

    classes () {
      return { player: true, 'is-fullscreen': this.isFullscreen }
    },

    styles () {
      return { cursor: this.controlsActive ? 'auto' : 'none' }
    }
  },

  watch: {
    isFullscreen: function (value) {
      this.setMedia({ fullscreen: value })
    }
  },

  created () {
    if (!this.$store.state.watch) {
      this.$store.registerModule('watch', playerModule)
    }

    this.create(this.options)
  },

  mounted () {
    this.initPlayer()

    this.$store.subscribeAction((action) => {
      if (action.type === 'watch/callback') {
        this.playerCallback(action.payload)
      }
    })
  },

  async beforeDestroy () {
    await this.media.detach()
    await this.media.destroy()

    this.$store.unregisterModule('watch')
  },

  methods: {
    ...mapActions('watch', [
      'create',
      'push'
    ]),

    ...mapMutations('watch', [
      'setMedia'
    ]),

    async initPlayer () {
      if (!Player.isBrowserSupported()) {
        alert('Browser is not supported')
      }

      try {
        this.media = new Player(this.player)
        this.media.configure(this.getShakaOptions)

        await this.media.load(this.source)
      } catch (e) {
        console.error(e)
      }
    },

    hideControls () {
      this.controlsActive = false
    },

    showControls () {
      this.controlsActive = true
      this.$timer.restart('hideControls')
    },

    keyHandler (event) {
      switch (event.srcKey) {
        case 'contextMenu':
          this.openContextMenu(this.item, 'media')
          break
        case 'snapshot':
          this.createSnapshot()
          break
        case 'toggleFullscreen':
          this.toggleFullscreen()
          break
        case 'togglePlay':
          this.playerCallback({ type: 'togglePlay' })
          break
      }
    },

    async createSnapshot () {
      const { success } = await this.submit('watch/push', {
        path: 'media/' + this.item.id,
        body: { snapshot: this.player.currentTime }
      })

      if (success) {
        this.$buefy.toast.open({
          message: `${this.item.name} was successfully updated.`,
          type: 'is-success'
        })
      }
    }
  }
}
</script>
