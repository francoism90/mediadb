<template lang="pug">
nav(class="level")
  div(class="level-left")
    filters(v-if="filters" :namespace="namespace" :filter="filter" :filters="filters" class="level-item")
    sorters(v-if="sorters" :namespace="namespace" :sorters="sorters" class="level-item")

  div(class="level-right")
    query(:namespace="namespace" :query="query" :filter="filter" :sorters="sorters" class="level-item")
</template>

<script>
export default {
  components: {
    Filters: () => import(/* webpackChunkName: "filters-filter" */ '@/components/filters/Filter'),
    Sorters: () => import(/* webpackChunkName: "filters-sorters" */ '@/components/filters/Sorters'),
    Query: () => import(/* webpackChunkName: "filters-query" */ '@/components/filters/Query')
  },

  props: {
    namespace: {
      type: String,
      required: true
    },

    filter: {
      type: String,
      default: null
    },

    filters: {
      type: Array,
      default: null
    },

    sorters: {
      type: Array,
      default: () => {
        return [
          { key: 'recommended', label: 'Recommended for You' },
          { key: 'trending', label: 'Trending' },
          { key: 'recent', label: 'Most recent' },
          { key: 'views', label: 'Most viewed' },
          { key: 'popular-week', label: 'Popular this week' },
          { key: 'popular-month', label: 'Popular this month' }
        ]
      }
    },

    query: {
      type: String,
      default: null
    }
  }
}
</script>
