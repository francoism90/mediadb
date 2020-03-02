<template lang="pug">
a(class="card" @click.prevent="pushRoute" @contextmenu.prevent="contextMenu" v-touch:swipe.right="contextMenu")
  div(class="card-image")
    preview(:options="videoOptions")

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
    Tags: () => import(/* webpackChunkName: "taglist" */ '@/components/ui/Taglist')
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
        poster: this.data.placeholder,
        source: this.data.preview,
        mimetype: 'video/mp4'
      }
    }
  },

  methods: {
    pushRoute () {
      this.$router.push({
        name: 'user-video',
        params: {
          id: this.data.id,
          slug: this.data.slug,
          user: this.data.relationships.user.id
        }
      })
    },

    contextMenu () {
      this.$store.dispatch('modal/open', {
        component: 'Media',
        class: 'manager',
        escape: ['escape'],
        fullscreen: true,
        props: { id: this.data.id }
      })
    }
  }
}
</script>
