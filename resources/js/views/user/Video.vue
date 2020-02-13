<template lang="pug">
section(v-if="ready" :key="data.id")
  player(:options="playerOptions")
  hero(:data="data")
  info(:data="data" :user-data="userData")
  related(:data="data")
</template>

<script>
import modelModule from '@/store/modules/model'
import { mapActions, mapState } from 'vuex'

export default {
  metaInfo () {
    if (this.ready) {
      return {
        title: this.data.name
      }
    }
  },

  components: {
    Hero: () => import(/* webpackChunkName: "video-hero" */ '@/components/video/Hero'),
    Info: () => import(/* webpackChunkName: "video-info" */ '@/components/video/Info'),
    Related: () => import(/* webpackChunkName: "video-related" */ '@/components/video/Related'),
    Player: () => import(/* webpackChunkName: "watch-player" */ '@/components/watch/Player')
  },

  props: {
    userData: {
      type: Object,
      required: true
    },

    userMeta: {
      type: Object,
      default: null
    }
  },

  computed: {
    ...mapState('video', [
      'ready',
      'data',
      'meta'
    ]),

    playerOptions () {
      return {
        item: this.data,
        autoplay: true,
        download: this.data.download || false,
        height: this.data.properties.height || '720',
        width: this.data.properties.width || '1280',
        poster: this.data.thumbnail || '',
        source: this.data.stream_url || '',
        type: 'manifest'
      }
    }
  },

  beforeRouteEnter (to, from, next) {
    next(vm => {
      vm.fetch({ path: 'media/' + to.params.id })
      next()
    })
  },

  beforeRouteUpdate (to, from, next) {
    this.fetch({ path: 'media/' + to.params.id })
    next()
  },

  created () {
    if (!this.$store.state.video) {
      this.$store.registerModule('video', modelModule)
    }
  },

  beforeDestroy () {
    this.$store.unregisterModule('video')
  },

  methods: {
    ...mapActions('video', [
      'fetch'
    ])
  }
}
</script>
