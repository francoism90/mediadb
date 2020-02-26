<template lang="pug">
div(v-if="getThumbnail" class="tooltip is-flex has-text-centered" :style="wrapper")
  span(class="is-inline-flex has-background-black-ter") {{ Number(data.label || 0) | timestamp }}
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  props: {
    data: {
      type: Object,
      required: true
    }
  },

  computed: {
    ...mapGetters('watch', [
      'getThumbnail'
    ]),

    wrapper () {
      return {
        marginLeft: (this.data.position - 80) + 'px',
        backgroundImage: `url(${this.getThumbnail})`
      }
    }
  },

  async mounted () {
    await this.$store.dispatch('watch/thumbnail', this.data.time || 1000)
  }
}
</script>
