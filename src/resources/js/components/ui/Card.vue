<template lang="pug">
a(class="card")
  div(class="card-image")
    preview(:id="'preview_' + data.id" :options="videoOptions")

  div(class="card-content")
    p(class="title") {{ data.name }}
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
    }
  },

  computed: {
    videoOptions () {
      return {
        disableKeymap: true,
        poster: this.data.thumbnail,
        source: this.data.preview,
        mimetype: 'video/mp4'
      }
    }
  }
}
</script>
