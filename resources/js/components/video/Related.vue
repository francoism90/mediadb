<template lang="pug">
section(class="section")
  div(class="container")
    h1(class="subtitle is-uppercase is-9") Related videos
    infinite(namespace="related" :api-route="apiRoute" component="Media")
</template>

<script>
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
        id: this.data.id,
        path: 'media',
        params: {
          include: 'tags,model',
          'filter[related]': this.data.id
        }
      }
    }
  }
}
</script>
