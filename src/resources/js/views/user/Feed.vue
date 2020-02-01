<template lang="pug">
section(class="section")
  div(class="container")
    infinite(:module="paginate")
</template>

<script>
export default {
  metaInfo () {
    return {
      title: 'Browse'
    }
  },

  components: {
    Infinite: () => import(/* webpackChunkName: "infinite" */ '@/components/ui/Infinite')
  },

  data () {
    return {
      paginate: {
        id: 'feed',
        type: 'media',
        props: {
          dispatcher: 'media/fetch',
          include: 'model,tags',
          sort: 'recommended'
        },
        tagsProps: {
          feed: true,
          size: 0
        }
      }
    }
  },

  async created () {
    await this.$store.dispatch('createPaginate', this.paginate)
  }
}
</script>
