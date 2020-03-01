<template lang="pug">
section(:key="data.id" class="section")
  div(class="container")
    h1(class="subtitle is-uppercase is-9") Related videos
    infinite(namespace="next" :api-route="apiRoute" :custom-class="paginateClass" type="media")
</template>

<script>
import paginateModule from '@/store/modules/paginate'

export default {
  components: {
    Infinite: () => import(/* webpackChunkName: "infinite" */ '@/components/paginate/Infinite')
  },

  props: {
    data: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      apiRoute: {
        path: 'media',
        params: {
          include: 'tags,model',
          'filter[related]': this.data.id
        }
      },
      paginateClass: `
        is-full-mobile
        is-half-tablet
        is-one-third-desktop
        is-one-quarter-widescreen
        is-one-quarter-fullhd
      `
    }
  },

  created () {
    if (!this.$store.state.next) {
      this.$store.registerModule('next', paginateModule)
    }
  },

  beforeDestroy () {
    this.$store.unregisterModule('next')
  }
}
</script>
