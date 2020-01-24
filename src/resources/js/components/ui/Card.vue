<template lang="pug">
article(class="card")
  a(@click="route()" class="card-image")
    figure(class="image")
      preview(:animation="data.preview" animation-type="video/mp4" :thumbnail="data.thumbnail")

  div(v-if="showContent" class="card-content")
    p(class="title is-6") {{ data.name }}
    p(class="subtitle")
      | <router-link v-if="data.relationships.user" :to="{ name: 'user-view', params: { user: data.relationships.user.id } }">{{ data.relationships.user.name }}</router-link> •
      | {{ Number(data.properties.duration) | timestamp }} •
      | {{ Number(data.views) | approximate }} views
    tags(v-if="data.relationships.tags.length" :items="data.relationships.tags")
</template>

<script>
export default {
  components: {
    Preview: () => import(/* webpackChunkName: "preview" */ '@/components/ui/Preview'),
    Tags: () => import(/* webpackChunkName: "tags" */ '@/components/ui/Tags')
  },

  props: {
    data: {
      type: Object,
      required: true
    },

    showContent: {
      type: Boolean,
      default: true
    },

    moduleId: {
      type: [Number, String],
      required: true
    }
  },

  data () {
    return {
      hasModal: ['search']
    }
  },

  methods: {
    route () {
      switch (this.moduleId) {
        default:
          this.$router.push({
            name: 'user-video',
            params: {
              id: this.data.id,
              slug: this.data.slug,
              user: this.data.relationships.user.id
            }
          })
      }

      if (this.hasModal.includes(this.moduleId)) {
        this.$store.dispatch('destroyModal')
      }
    }
  }
}
</script>
