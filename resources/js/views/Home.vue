<template lang="pug">
section(class="section is-medium")
  div(class="container")
    nav(class="level")
      div(class="level-left")
        filters(:namespace="namespace" field="sort" :items="sorters" class="level-item")
        filters(:namespace="namespace" field="filter[type]" :items="types" class="level-item")

      div(class="level-right")
        query(:namespace="namespace" class="level-item")

    infinite(:namespace="namespace" :api-route="apiRoute" type="media")
</template>

<script>
import paginateModule from '@/store/modules/paginate'

export default {
  components: {
    Infinite: () => import(/* webpackChunkName: "paginate-infinite" */ '@/components/paginate/Infinite'),
    Filters: () => import(/* webpackChunkName: "paginate-filters" */ '@/components/paginate/Filters'),
    Query: () => import(/* webpackChunkName: "paginate-query" */ '@/components/paginate/Query')
  },

  data () {
    return {
      namespace: 'feed',
      apiRoute: {
        path: 'media',
        params: {
          include: 'model,tags'
        }
      },
      sorters: [
        { key: 'recommended', label: 'Recommended for You' },
        { key: 'trending', label: 'Trending' },
        { key: 'recent', label: 'Most recent' },
        { key: 'views', label: 'Most viewed' },
        { key: 'popular-week', label: 'Popular this week' },
        { key: 'popular-month', label: 'Popular this month' }
      ],
      types: [
        { key: '*', label: 'All Content' },
        { key: 'user', label: 'My Uploads' },
        { key: 'original', label: 'Original Videos' }
      ]
    }
  },

  created () {
    if (!this.$store.state.feed) {
      this.$store.registerModule('feed', paginateModule)
    }
  }
}
</script>
