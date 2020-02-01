<template lang="pug">
section(:key="data.id" class="section")
  div(class="container")
    h1(class="subtitle is-uppercase is-9") Up Next
    infinite(:module="paginate" :custom-class="paginate.customClass" :has-filters="false")
</template>

<script>
export default {
  components: {
    Infinite: () => import(/* webpackChunkName: "infinite" */ '@/components/ui/Infinite')
  },

  props: {
    data: {
      type: Object,
      required: true
    },

    userData: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      paginate: {
        id: 'related',
        type: 'media',
        props: {
          dispatcher: 'media/fetch',
          include: 'model,tags',
          related: this.data.id,
          sort: null
        },
        customClass: `
          is-full-mobile
          is-half-tablet
          is-one-third-desktop
          is-one-quarter-widescreen
          is-one-quarter-fullhd
        `
      }
    }
  },

  async created () {
    await this.$store.dispatch('createPaginate', this.paginate)
  }
}
</script>
