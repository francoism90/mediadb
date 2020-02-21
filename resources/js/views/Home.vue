<template lang="pug">
section(class="section is-medium")
  div(class="container")
    filters(namespace="feed")
    infinite(namespace="feed" :api-route="apiRoute" type="media")
</template>

<script>
import paginateModule from '@/store/modules/paginate'

export default {
  components: {
    Filters: () => import(/* webpackChunkName: "filters" */ '@/components/filters/Level'),
    Infinite: () => import(/* webpackChunkName: "infinite" */ '@/components/ui/Infinite')
  },

  data () {
    return {
      apiRoute: {
        path: 'media',
        params: {
          include: 'model,tags',
          sort: 'recommended'
        }
      }
    }
  },

  created () {
    if (!this.$store.state.feed) {
      this.$store.registerModule('feed', paginateModule)
    }
  }
}
</script>
