<template lang="pug">
section(v-if="data.id")
  player(:id="data.id" :options="videoOptions")
  hero(:data="data" :user-data="userData")
  info(:data="data" :meta="meta" :user-data="userData")
  next(:data="data" :user-data="userData")
</template>

<script>
import { mapActions, mapGetters, mapState } from 'vuex'

export default {
  metaInfo () {
    if (this.data && this.data.name) {
      return {
        title: this.data.name
      }
    }
  },

  components: {
    Hero: () => import(/* webpackChunkName: "video-hero" */ '@/components/video/Hero'),
    Info: () => import(/* webpackChunkName: "video-info" */ '@/components/video/Info'),
    Next: () => import(/* webpackChunkName: "video-next" */ '@/components/video/Next'),
    Player: () => import(/* webpackChunkName: "player-instance" */ '@/components/player/Instance')
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
    ...mapState({
      data: state => state.media.data,
      meta: state => state.media.meta
    }),

    ...mapGetters({
      videoOptions: 'media/videoOptions'
    })
  },

  beforeRouteEnter (to, from, next) {
    next(vm => {
      vm.get(to.params.id)
      next()
    })
  },

  beforeRouteUpdate (to, from, next) {
    this.get(to.params.id)
    next()
  },

  methods: {
    ...mapActions({
      get: 'media/get'
    })
  }
}
</script>
