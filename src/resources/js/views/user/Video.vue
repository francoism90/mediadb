<template lang="pug">
section(v-if="data.id")
  player(:id="data.id" :options="videoOptions")

  section(class="section is-large")
    div(class="container-fluid")
      div(class="columns is-variable is-8")
        div(class="column")
          hero(:data="data" :user-data="userData")
          actions(:data="data" :meta="meta" :user-data="userData")
          related(:data="data" :user-data="userData")

        div(class="column next")
          user(:data="data" :user-data="userData")
          collections(:data="data" :user-data="userData")
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
    Actions: () => import(/* webpackChunkName: "video-actions" */ '@/components/video/Actions'),
    Collections: () => import(/* webpackChunkName: "vide-collections" */ '@/components/video/Collections'),
    Hero: () => import(/* webpackChunkName: "video-hero" */ '@/components/video/Hero'),
    Player: () => import(/* webpackChunkName: "player-instance" */ '@/components/player/Instance'),
    Related: () => import(/* webpackChunkName: "video-related" */ '@/components/video/Related'),
    User: () => import(/* webpackChunkName: "video-user" */ '@/components/video/User')
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
