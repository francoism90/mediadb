<template lang="pug">
section(v-if="data.id" :key="data.id")
  player(:options="playerOptions")
  hero(:data="data")
  info(:data="data" :channel-data="channelData")
  related(:data="data")
</template>

<script>
import modelModule from '@/store/modules/model'
import paginateModule from '@/store/modules/paginate'
import { mapGetters } from 'vuex'

export default {
  metaInfo () {
    return {
      title: this.data.name
    }
  },

  components: {
    Hero: () => import(/* webpackChunkName: "video-hero" */ '@/components/video/Hero'),
    Info: () => import(/* webpackChunkName: "video-info" */ '@/components/video/Info'),
    Related: () => import(/* webpackChunkName: "video-related" */ '@/components/video/Related'),
    Player: () => import(/* webpackChunkName: "watch-player" */ '@/components/watch/Player')
  },

  props: {
    id: {
      type: String,
      required: true
    },

    channelData: {
      type: Object,
      required: true
    },

    channelMeta: {
      type: Object,
      default: null
    }
  },

  computed: {
    ...mapGetters('video', {
      data: 'getData',
      meta: 'getMeta'
    }),

    playerOptions () {
      return {
        item: this.data,
        autoplay: true,
        download: this.meta.download_url || false,
        height: this.data.properties.height || '720',
        width: this.data.properties.width || '1280',
        poster: this.data.placeholder || '',
        source: this.meta.stream_url || '',
        type: 'manifest'
      }
    }
  },

  beforeRouteEnter (to, from, next) {
    next(vm => {
      vm.fetch(to.params.id)
      next()
    })
  },

  beforeRouteUpdate (to, from, next) {
    this.fetch(to.params.id)
    next()
  },

  created () {
    if (!this.$store.state.video) {
      this.$store.registerModule('video', modelModule)
    }

    if (!this.$store.state.related) {
      this.$store.registerModule('related', paginateModule)
    }
  },

  methods: {
    async fetch (id) {
      await this.$store.dispatch('video/fetch', {
        path: 'media/' + id
      })
    }
  }
}
</script>
