<template lang="pug">
nav(:key="id" class="controls")
  b-slider(
    v-show="showElement('slider')"
    rounded
    lazy
    type="is-primary"
    :value="Number(durationPct())"
    :tooltip="false"
    :style="{ background: `linear-gradient(90deg, hsl(0, 0%, 86%) ${bufferedPct()}%, hsla(0, 0%, 71%, 0.6) ${bufferRemainingPct()}%)` }"
    @change="setCurrentTime"
  )

  div(class="level is-mobile")
    div(class="level-left")
      div(class="level-item")
        b-button(v-show="showElement('togglePlay')" size="is-normal" type="is-text" class="has-text-white" :icon-right="paused ? 'play' : 'pause'" @click.native="callback('toggle-play')")
        b-button(v-show="showElement('fastRewind')" size="is-normal" type="is-text" class="has-text-white" icon-right="rewind-10" @click.native="callback('fast-rewind')")
        b-button(v-show="showElement('fastForward')" size="is-normal" type="is-text" class="has-text-white" icon-right="fast-forward-10" @click.native="callback('fast-foward')")
        b-button(v-show="showElement('currentTime')" size="is-normal" type="is-text" class="has-text-white") {{ Number(current) | timestamp }} / {{ Number(duration) | timestamp }}

    div(Class="level-right")
      div(class="level-item")
        b-button(v-show="showElement('settings')" size="is-normal" type="is-text" class="has-text-white" icon-right="settings" @click.native="callback('toggle-fullscreen')")
        b-button(v-show="showElement('toggleFullscreen')"  size="is-normal" type="is-text" class="has-text-white" :icon-right="isFullscreen ? 'arrow-collapse' : 'arrow-expand'" @click.native="callback('toggle-fullscreen')")
</template>

<script>
import { fullscreenHandler } from '@/components/mixins'

export default {
  mixins: [fullscreenHandler],

  props: {
    id: {
      type: String,
      required: true
    },

    elements: {
      type: Array,
      required: true
    }
  },

  data () {
    return {
      buffered: null,
      current: 0,
      duration: 0,
      paused: true,
      muted: false,
      readyState: 0
    }
  },

  mounted () {
    this.$eventHub.$on(this.id, (event) => {
      Object.assign(this.$data, {
        buffered: event.buffered || this.buffered,
        current: event.current || this.current,
        duration: event.duration || this.duration,
        paused: event.paused,
        muted: event.muted,
        readyState: event.readyState || this.readyState
      })
    })
  },

  methods: {
    showElement (key) {
      return this.elements.includes(key)
    },

    callback (event) {
      this.$eventHub.$emit(this.id, event)
    },

    bufferedPct () {
      if (this.readyState < 1 || !this.buffered.length) {
        return 0
      }

      const r = this.buffered
      r.start(0)

      const end = r.end(0)

      return (end / this.duration) * 100
    },

    bufferRemainingPct () {
      return 100 - this.bufferedPct()
    },

    durationPct () {
      return ((this.current / this.duration) * 100)
    },

    setCurrentTime (pct) {
      this.current = this.duration * (pct / 100)

      this.callback({
        key: 'current-time',
        time: this.current
      })
    }
  }
}
</script>
