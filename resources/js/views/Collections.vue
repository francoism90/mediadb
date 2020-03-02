<template lang="pug">
section(class="section is-medium")
  div(class="container")
    nav(class="level")
      div(class="level-left")
        filters(:namespace="namespace" field="sort" :items="sorters" class="level-item")
        filters(:namespace="namespace" field="filter[type]" :items="types" class="level-item")

      div(class="level-right")
        query(:namespace="namespace" class="level-item")

    infinite(
      :namespace="namespace"
      :api-route="apiRoute"
      :column-class="columnClass"
      component="Collection"
    )
</template>

<script>
import paginateModule from '@/store/modules/paginate'

export default {
  metaInfo () {
    return {
      title: 'Collections'
    }
  },

  components: {
    Infinite: () => import(/* webpackChunkName: "paginate-infinite" */ '@/components/paginate/Infinite'),
    Filters: () => import(/* webpackChunkName: "paginate-filters" */ '@/components/paginate/Filters'),
    Query: () => import(/* webpackChunkName: "paginate-query" */ '@/components/paginate/Query')
  },

  data () {
    return {
      namespace: 'collects',
      apiRoute: {
        path: 'collect',
        params: {
          include: 'tags,user'
        }
      },
      columnClass: `
        is-full-mobile
        is-one-quarter-tablet
        is-one-quarter-desktop
        is-one-quarter-widescreen
        is-one-quarter-fullhd
      `,
      sorters: [
        { key: 'recommended', label: 'Recommended for You' },
        { key: 'trending', label: 'Trending' },
        { key: 'recent', label: 'Most recent' },
        { key: 'views', label: 'Most viewed' },
        { key: 'popular-week', label: 'Popular this week' },
        { key: 'popular-month', label: 'Popular this month' }
      ],
      types: [
        { key: '*', label: 'All Collections' },
        { key: 'user', label: 'My Collections' }
      ]
    }
  },

  created () {
    if (!this.$store.state.collects) {
      this.$store.registerModule('collects', paginateModule)
    }
  }
}
</script>
