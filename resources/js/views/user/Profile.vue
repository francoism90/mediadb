<template lang="pug">
section(class="section is-medium")
  div(class="container")
    h1(class="title is-4") {{ userData.name }}
    h2(class="subtitle")
      | Joined {{ String(userData.created_at) | datestamp }} •
      | {{ Number(userData.media) | approximate }} uploads •
      | {{ Number(userData.views) | approximate }} views

    infinite(namespace="user_media" :api-route="apiRoute" component="Media")
</template>

<script>
import paginateModule from '@/store/modules/paginate'

export default {
  metaInfo () {
    return {
      title: this.userData.name
    }
  },

  components: {
    Infinite: () => import(/* webpackChunkName: "paginate-infinite" */ '@/components/paginate/Infinite'),
    Filters: () => import(/* webpackChunkName: "paginate-filters" */ '@/components/paginate/Filters'),
    Query: () => import(/* webpackChunkName: "paginate-query" */ '@/components/paginate/Query')
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

  data () {
    return {
      namespace: 'user_media',
      apiRoute: {
        id: this.userData.id,
        path: 'media',
        params: {
          include: 'model,tags',
          'filter[user]': this.userData.id
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
