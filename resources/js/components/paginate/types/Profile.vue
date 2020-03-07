<template lang="pug">
a(class="card" @click.prevent="pushRoute")
  div(class="card-image")
    figure(class="image")
      img(:src="data.placeholder" loading="lazy" :alt="data.name" height="128")

  div(class="card-content")
    p(class="title") {{ data.name }}
    p(class="subtitle")
      | {{ Number(data.media) | approximate }} items •
      | {{ Number(data.views) | approximate }} views
</template>

<script>
export default {
  components: {
    Tags: () => import(/* webpackChunkName: "taglist" */ '@/components/ui/Taglist')
  },

  props: {
    data: {
      type: Object,
      required: true
    }
  },

  methods: {
    pushRoute () {
      this.$router.push({
        name: 'channel-view',
        params: {
          channel: this.data.id
        }
      })
    }
  }
}
</script>
