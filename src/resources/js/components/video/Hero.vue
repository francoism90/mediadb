<template lang="pug">
section(class="media-actions")
  article(class="media")
    div(class="media-content")
      h1(class="title") {{ data.name }}
      h2(class="subtitle")
        | <span class="is-inline-block">{{ String(data.created_at) | datestamp }}</span> •
        | <span class="is-inline-block">{{ Number(10) | approximate }} likes</span> •
        | <span class="is-inline-block">{{ Number(data.views) | approximate }} views</span>

  section(v-if="data.relationships.tags.length" class="tags")
    tags(:items="data.relationships.tags")
</template>

<script>
export default {
  components: {
    Tags: () => import(/* webpackChunkName: "tags" */ '@/components/ui/Tags')
  },

  props: {
    data: {
      type: Object,
      required: true
    },

    meta: {
      type: Object,
      default: null
    },

    userData: {
      type: Object,
      required: true
    },

    userMeta: {
      type: Object,
      default: null
    }
  }
}
</script>
