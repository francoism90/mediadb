<template lang="pug">
section(class="section is-medium")
  div(class="container")
    h1(class="title is-4") {{ channelData.name }}
    h2(class="subtitle")
      | Joined {{ String(channelData.created_at) | datestamp }} •
      | {{ Number(channelData.media) | approximate }} uploads •
      | {{ Number(channelData.views) | approximate }} views

    nav(class="level")
      div(class="level-left")
        filters(:namespace="namespace" field="sort" :items="sorters" class="level-item")

      div(class="level-right")
        query(:namespace="namespace" class="level-item")

    infinite(namespace="user_media" :api-route="apiRoute" component="Media")
</template>

<script>
import paginateModule from '@/store/modules/paginate'

export default {
  metaInfo () {
    return {
      title: this.channelData.name
    }
  },

  components: {
    Infinite: () => import(/* webpackChunkName: "paginate-infinite" */ '@/components/paginate/Infinite'),
    Filters: () => import(/* webpackChunkName: "paginate-filters" */ '@/components/paginate/Filters'),
    Query: () => import(/* webpackChunkName: "paginate-query" */ '@/components/paginate/Query')
  },

  props: {
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
      namespace: 'user_media',
      apiRoute: {
        id: this.channelData.id,
        path: 'media',
        params: {
          include: 'model,tags',
          'filter[user]': this.channelData.id
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

  created () {
    if (!this.$store.state.user_media) {
      this.$store.registerModule('user_media', paginateModule)
    }
  }
}
</script>
