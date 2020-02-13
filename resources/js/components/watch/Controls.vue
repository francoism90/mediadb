<template lang="pug">
nav(class="controls")
  b-slider(
    rounded
    lazy
    type="is-primary"
    :value="durationPct"
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
      return Math.round((this.currentTime / this.duration) * 100)
    }
  },

  methods: {
    ...mapActions('watch', [
      'callback'
    ]),

    setCurrentTime (pct) {
      this.callback({ type: 'currentTime', value: this.duration * (pct / 100) })
    }
  }
}
</script>
