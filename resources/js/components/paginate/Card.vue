<template lang="pug">
a(class="card" :class="'card-' + type")
  template(v-if="type === 'media'")
    div(class="card-image")
      preview(:options="videoOptions")

    div(class="card-content")
      p(class="title") {{ data.name }}
      p(class="subtitle")
        | <router-link v-if="data.relationships.user" :to="{ name: 'user-view', params: { user: data.relationships.user.id } }">{{ data.relationships.user.name }}</router-link> •
        | {{ Number(data.properties.duration) | timestamp }} •
        | {{ Number(data.views) | approximate }} views
      tags(v-if="data.relationships.tags.length" :items="data.relationships.tags")

  template(v-else-if="type === 'collect'")
    div(class="card-image")
      figure(class="image")
        img(:src="data.placeholder" loading="lazy" :alt="data.name")

    div(class="card-content")
      p(class="title") {{ data.name }}
      p(class="subtitle")
        | <router-link v-if="data.relationships.user" :to="{ name: 'user-view', params: { user: data.relationships.user.id } }">{{ data.relationships.user.name }}</router-link> •
        | {{ Number(data.media) | approximate }} items •
        | {{ Number(data.views) | approximate }} views
      tags(v-if="data.relationships.tags.length" :items="data.relationships.tags")

  template(v-else-if="type === 'tag'")
    div(class="card-image")
      figure(class="image")
        img(:src="data.placeholder" loading="lazy" :alt="data.name")

    div(class="card-content")
      p(class="title") {{ data.name }}
      p(class="subtitle")
</template>

<script>
export default {
  components: {
    Preview: () => import(/* webpackChunkName: "preview" */ '@/components/ui/Preview'),
    Tags: () => import(/* webpackChunkName: "taglist" */ '@/components/ui/Taglist')
  },

  props: {
    data: {
      type: Object,
      required: true
    },

    type: {
      type: String,
      required: true
    }
  },

  computed: {
    videoOptions () {
      return {
        poster: this.data.placeholder,
        source: this.data.preview,
        mimetype: 'video/mp4'
      }
    }
  }
}
</script>
