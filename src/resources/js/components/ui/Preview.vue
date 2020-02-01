<template lang="pug">
figure(class="image" @mouseover="togglePlay" @mouseout="togglePlay" v-touch:swipe.left="togglePlay" )
  video(
    ref="videoElement"
    :poster="options.poster || ''"
    loop
    muted
    playsinline
    preload="auto"
    disableRemotePlayback
    @contextmenu.prevent
  )
    source(ref="videoSource" :src="options.source" :type="options.mimetype")
</template>

<script>
import { playerHandler } from '@/components/mixins'

export default {
  mixins: [playerHandler]
}
</script>
