<template lang="pug">
nav(class="controls")
  div(ref="slider" @mousemove="onSeekerHover" @mouseleave="onSeekerLeave")
    thumbnail(v-if="showThumb" :key="thumbnail.time" :data="thumbnail")
    b-slider(
      ref="seeker"
      rounded
      lazy
      type="is-primary"
      :value="Number(durationPct)"
      :tooltip="false"
      :style="{ background: `linear-gradient(90deg, hsl(0, 0%, 86%) ${bufferedPct}%, hsla(0, 0%, 71%, 0.6) ${bufferRemainingPct}%)` }"
      @change="setCurrentTime"
    )

  div(class="level is-mobile")
    div(class="level-left")
      div(class="level-item")
        b-button(
          size="is-normal"
          type="is-text"
          class="has-text-white"
          :icon-right="playing ? 'pause' : 'play'"
          @click.prevent="callback({ type: 'togglePlay' })"
        )

        b-button(
          size="is-normal"
          type="is-text"
          class="has-text-white"
          icon-right="rewind-10"
          @click.prevent="callback({ type: 'fastRewind' })"
        )

        b-button(
          size="is-normal"
          type="is-text"
          class="has-text-white"
          icon-right="fast-forward-10"
          @click.prevent="callback({ type: 'fastForward' })"
        )

        b-button(
          size="is-normal"
          type="is-text"
          class="has-text-white"
        ) {{ Number(currentTime) | timestamp }} / {{ Number(duration) | timestamp }}

    div(class="level-right")
      div(class="level-item")
        b-button(
          size="is-normal"
          type="is-text"
          class="has-text-white"
          icon-right="settings"
          @click.prevent="callback({ type: 'toggleFullscreen' })"
        )

        b-button(
          size="is-normal"
          type="is-text"
          class="has-text-white"
          :icon-right="fullscreen ? 'arrow-collapse' : 'arrow-expand'"
          @click.prevent="callback({ type: 'toggleFullscreen' })"
        )
</template>

<script>
import { mapState, mapActions } from 'vuex'

export default {
  components: {
    Thumbnail: () => import(/* webpackChunkName: "watch-thumbnail" */ '@/components/watch/Thumbnail')
  },

  data () {
    return {
      showThumb: false,
      thumbnail: {}
    }
  },

  computed: {
    ...mapState('watch', [
      'buffered',
      'currentTime',
      'duration',
      'fullscreen',
      'muted',
      'playing'
    ]),

    bufferedPct () {
      if (!this.buffered || !this.buffered.length) {
        return 0
      }

      const r = this.buffered
      r.start(0)

      const end = r.end(0)

      return Math.round((end / this.duration) * 100)
    },

    bufferRemainingPct () {
      return Math.round(100 - this.bufferedPct)
    },

    durationPct () {
      return Number((this.currentTime / this.duration) * 100).toFixed(3)
    }
  },

  methods: {
    ...mapActions('watch', [
      'callback'
    ]),

    getTimeByPct (percent = 0) {
      return this.duration * (percent / 100)
    },

    setCurrentTime (percent) {
      this.callback({ type: 'currentTime', value: this.getTimeByPct(percent) })
    },

    onSeekerHover (event) {
      const sliderWidth = this.$refs.slider.clientWidth
      const sliderOffsetLeft = this.$refs.slider.getBoundingClientRect().left
      const position = event.clientX - sliderOffsetLeft
      const percent = (position) / sliderWidth * 100
      const time = Math.ceil((this.duration * percent) * 10)

      this.thumbnail = {
        label: this.getTimeByPct(percent),
        percent: percent,
        position: position,
        time: time
      }

      this.showThumb = true
    },

    onSeekerLeave () {
      this.showThumb = false
    }
  }
}
</script>
