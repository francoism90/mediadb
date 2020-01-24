<template lang="pug">
back-to-top(visibleoffset="300" bottom="1rem" right="1rem")
  b-button(icon-right="chevron-up" size="is-normal")
</template>

<script>
import BackToTop from 'vue-backtotop'

export default {
  components: {
    BackToTop
  }
}
</script>
