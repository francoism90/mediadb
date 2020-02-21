<template lang="pug">
section(class="hero is-small")
  div(class="hero-body")
    div(class="container")
      h1(class="title") {{ data.name }}
      h2(class="subtitle")
        | <span class="is-inline-block">{{ String(data.created_at) | datestamp }}</span> •
        | <span class="is-inline-block">{{ Number(10) | approximate }} likes</span> •
        | <span class="is-inline-block">{{ Number(data.views) | approximate }} views</span>
      tags(:items="data.relationships.tags")
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
  }
}
</script>
