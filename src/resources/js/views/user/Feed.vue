<template lang="pug">
section(class="section")
  div(class="container")
    infinite(:module="paginate")
</template>

<script>
export default {
  components: {
    Infinite: () => import(/* webpackChunkName: "infinite" */ '@/components/ui/Infinite')
  },

  data () {
    return {
      paginate: {
        id: 'feed',
        props: {
          dispatcher: 'media/fetch',
          include: 'model,tags',
          sort: 'recommended'
        }
      }
    }
  },

  created () {
    this.$store.dispatch('createPaginate', this.paginate)
  }
}
</script>
