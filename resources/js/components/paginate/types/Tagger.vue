<template lang="pug">
a(class="card card-letter" @click.prevent="pushQuery")
  div(class="card-content")
    div(class="media")
      div(class="media-left has-background-grey is-size-5")
        span {{ data.name.charAt(0) }}

      div(class="media-content")
        p(class="title") {{ data.name }}
        p(class="subtitle")
          | <span v-if="data.collect >= 0">{{ Number(data.collect) | approximate }} items</span>
          | <span v-else-if="data.media >= 0">{{ Number(data.media) | approximate }} items</span>
</template>

<script>
export default {
  props: {
    data: {
      type: Object,
      required: true
    },

    paginate: {
      type: String,
      required: true
    }
  },

  methods: {
    pushQuery () {
      this.$store.dispatch(this.paginate + '/reset', {
        params: {
          'filter[query]': '#' + this.data.slug
        }
      })

      this.$store.dispatch('modal/close')
    }
  }
}
</script>
