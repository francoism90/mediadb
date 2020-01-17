<template lang="pug">
section(v-if="items.length")
  h1(class="heading") Featured in collections
  b-carousel(:autoplay="false" icon-size="is-medium" :indicator="false" :pause-info="false" v-model="active")
    b-carousel-item(v-for="(item, i) in items" :key="i")
      card(:data="item" :show-content="false")
</template>

<script>
import { mapActions } from 'vuex'

export default {
  components: {
    Card: () => import(/* webpackChunkName: "card" */ '@/components/ui/Card')
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
      active: 0,
      items: []
    }
  },

  async created () {
    const response = await this.fetch({
      include: 'model',
      collection: this.data.id
    })

    this.items = response.data
  },

  methods: {
    ...mapActions({
      fetch: 'media/fetch'
    })
  }
}
</script>
