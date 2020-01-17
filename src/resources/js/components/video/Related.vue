<template lang="pug">
section
  h1(class="heading") Related Videos
  infinite(id="related" :custom-class="customClass" dispatcher="media/fetch" :params="params")
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
      customClass: `
        iis-full-mobile
        is-half-tablet
        is-half-desktop
        is-one-third-widescreen
        is-one-quarter-fullhd
      `,
      params: {
        include: 'model,tags',
        related: this.data.id,
        size: 6,
        sort: null
      }
    }
  }
}
</script>
