<template lang="pug">
section(class="section is-medium")
  div(class="container")
    h1(class="title is-4") {{ data.name }}
    h2(class="subtitle")
      | <router-link :to="{ name: 'channel-view', params: { channel: channelData.id } }">{{ channelData.name }}</router-link> •
      | {{ Number(data.media) | approximate }} items •
      | {{ Number(data.views) | approximate }} views

    nav(class="level")
      div(class="level-left")
        filters(:namespace="namespace" field="sort" :items="sorters" class="level-item")

      div(class="level-right")
        query(:namespace="namespace" class="level-item")

    infinite(namespace="channel_media" :api-route="apiRoute" component="Media")
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
    Infinite: () => import(/* webpackChunkName: "paginate-infinite" */ '@/components/paginate/Infinite'),
    Filters: () => import(/* webpackChunkName: "paginate-filters" */ '@/components/paginate/Filters'),
    Query: () => import(/* webpackChunkName: "paginate-query" */ '@/components/paginate/Query')
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

  data () {
    return {
      namespace: 'channel_media',
      apiRoute: {
        id: this.id,
        path: 'media',
        params: {
          include: 'model,tags',
          'filter[collect]': this.id
        }
      },
      sorters: [
        { key: 'recommended', label: 'Recommended for You' },
        { key: 'trending', label: 'Trending' },
        { key: 'recent', label: 'Most recent' },
        { key: 'views', label: 'Most viewed' },
        { key: 'popular-week', label: 'Popular this week' },
        { key: 'popular-month', label: 'Popular this month' }
      ]
    }
  },

  computed: {
    ...mapGetters('collect', {
      data: 'getData'
    })
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
    if (!this.$store.state.collect) {
      this.$store.registerModule('collect', modelModule)
    }

    if (!this.$store.state.channel_media) {
      this.$store.registerModule('channel_media', paginateModule)
    }
  },

  methods: {
    async fetch (id) {
      await this.$store.dispatch('collect/fetch', {
        path: 'collect/' + id
      })
    }
  }
}
</script>
