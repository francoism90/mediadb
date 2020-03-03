<template lang="pug">
a(class="card" @click.prevent="pushQuery")
  div(class="card-image")
    figure(class="image")
      img(:src="data.placeholder" loading="lazy" :alt="data.name")

  div(class="card-content")
    p(class="title is-capitalized") {{ data.name }}
    p(class="subtitle")
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
