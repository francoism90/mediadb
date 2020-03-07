<template lang="pug">
section(class="section media-info")
  div(class="container")
    nav(class="level is-mobile")
      div(class="level-item has-text-centered")
        a
          p(class="heading")
            b-icon(icon="thumb-up-outline")
          p(class="title is-7") Like

      div(class="level-item has-text-centered")
        a
          p(class="heading")
            b-icon(icon="flag")
          p(class="title is-7") Report

      div(class="level-item has-text-centered")
        a(@click.prevent="callback({ type: 'download' })")
          p(class="heading")
            b-icon(icon="download")
          p(class="title is-7") Download

      div(class="level-item has-text-centered")
        a(@click.prevent="callback({ type: 'manager' })")
          p(class="heading")
            b-icon(icon="file-document-box")
          p(class="title is-7") Manage

    nav(class="level is-mobile is-marginless media-subscribe")
      div(class="level-left")
        div(class="level-item")
          article(class="media")
            figure(class="media-left")
              p(class="image is-64x64")
                img(:src="channelData.placeholder" loading="lazy" height="40" width="64" :alt="channelData.name")

            div(class="media-content")
              p
                | <router-link :to="{ name: 'channel-view', params: { channel: channelData.id }}">{{ channelData.name }}</router-link><br>
                | <span class="is-inline-block">{{ Number(0) | approximate }} subscribers</span>

      div(class="level-right")
        div(class="level-item is-marginless is-hidden-tablet")
          b-button(icon-right="bell-outline")

        div(class="level-item is-hidden-mobile")
          b-button(icon-left="bell-outline") Subscribe

    section(class="media-description")
      p(v-if="data.description") {{ data.description }}
</template>

<script>
import { mapActions } from 'vuex'

export default {
  props: {
    data: {
      type: Object,
      required: true
    },

    channelData: {
      type: Object,
      required: true
    }
  },

  methods: {
    ...mapActions('watch', [
      'callback'
    ])
  }
}
</script>
