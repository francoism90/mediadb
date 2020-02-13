<template lang="pug">
router-view(v-if="ready" :user-data="data" :user-meta="meta")
</template>

<script>
import modelModule from '@/store/modules/model'
import { mapActions, mapState } from 'vuex'

export default {
  computed: {
    ...mapState('user', [
      'ready',
      'data',
      'meta'
    ])
  },

  beforeRouteEnter (to, from, next) {
    next(vm => {
      vm.fetch({ path: 'user/' + to.params.user })
      next()
    })
  },

  beforeRouteUpdate (to, from, next) {
    this.fetch({ path: 'user/' + to.params.user })
    next()
  },

  created () {
    if (!this.$store.state.user) {
      this.$store.registerModule('user', modelModule)
    }
  },

  beforeDestroy () {
    this.$store.unregisterModule('user')
  },

  methods: {
    ...mapActions('user', [
      'fetch'
    ])
  }
}
</script>
