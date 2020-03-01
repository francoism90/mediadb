<template lang="pug">
section(v-if="data.id" :key="data.id")
  player(:options="playerOptions")
  hero(:data="data")
  info(:data="data" :user-data="userData")
  related(:data="data")
</template>

<script>
import modelModule from '@/store/modules/model'
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
    ...mapGetters('video', {
      data: 'getData',
      meta: 'getMeta'
    }),

    playerOptions () {
      return {
        item: this.data,
        autoplay: true,
        download: this.data.download || false,
        height: this.data.properties.height || '720',
        width: this.data.properties.width || '1280',
        poster: this.data.placeholder || '',
        source: this.data.stream || '',
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
  },

  beforeDestroy () {
    this.$store.unregisterModule('video')
  },

  methods: {
    async fetch (id) {
      await this.$store.dispatch('video/fetch', {
        path: 'media',
        params: {
          append: 'download_url,stream_url',
          include: 'tags',
          'filter[id]': id
        }
      })
    }
  }
}
</script>
