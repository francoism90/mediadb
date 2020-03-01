<template lang="pug">
modal
  nav(class="level")
    div(class="level-left")
      filters(:namespace="namespace" field="sort" :items="sorters" class="level-item")
      filters(:namespace="namespace" field="filter[type]" :items="types" class="level-item")

    div(class="level-right")
      query(:namespace="namespace" :tags="false" class="level-item")

  infinite(
    :namespace="namespace"
    :api-route="apiRoute"
    :column-class="columnClass"
    :paginate="paginate"
    type="tag"
  )
</template>

<script>
import paginateModule from '@/store/modules/paginate'

export default {
  components: {
    Modal: () => import(/* webpackChunkName: "modal" */ '@/components/ui/Modal'),
    Infinite: () => import(/* webpackChunkName: "paginate-infinite" */ '@/components/paginate/Infinite'),
    Filters: () => import(/* webpackChunkName: "paginate-filters" */ '@/components/paginate/Filters'),
    Query: () => import(/* webpackChunkName: "paginate-query" */ '@/components/paginate/Query')
  },

  props: {
    paginate: {
      type: String,
      required: true
    }
  },

  data () {
    return {
      namespace: 'tags',
      apiRoute: {
        path: 'tags'
      },
      columnClass: `
        is-half-mobile
        is-one-third-tablet
        is-one-quarter-desktop
        is-one-quarter-widescreen
        is-one-quarter-fullhd
      `,
      sorters: [
        { key: 'name', label: 'Alphabetically' },
        { key: 'recommended', label: 'Recommended for You' }
      ],
      types: [
        { key: '*', label: 'All Types' },
        { key: 'category', label: 'Categories' },
        { key: 'language', label: 'Languages' },
        { key: 'people', label: 'People' }
      ]
    }
  },

  created () {
    if (!this.$store.state.tags) {
      this.$store.registerModule('tags', paginateModule)
    }
  }
}
</script>
