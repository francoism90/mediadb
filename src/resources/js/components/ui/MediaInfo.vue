<template lang="pug">
section(:key="data.id" class="card")
  div(class="card-image")
    player(:id="'info_' + data.id" :has-controls="controls" :options="videoOptions")

  div(class="card-content")
    p(class="title") {{ data.original_name }}
    p(v-if="data.properties" class="subtitle")
      | {{ Number(data.properties.duration) | timestamp }} •
      | {{ Number(data.views) | approximate }} views •
      | {{ Number(10) | approximate }} likes
</template>

<script>
export default {
  components: {
    Player: () => import(/* webpackChunkName: "player-instance" */ '@/components/player/Instance')
  },

  props: {
    data: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      controls: [
        'slider',
        'togglePlay',
        'currentTime',
        'toggleFullscreen'
      ]
    }
  },

  computed: {
    videoOptions () {
      return {
        disableKeymap: true,
        poster: this.data.thumbnail,
        height: 192,
        manifest: this.data.stream_url
      }
    }
  }
}
</script>
