<template lang="pug">
router-view(v-if="data.id" :user-data="data" :user-meta="meta")
</template>

<script>
import modelModule from '@/store/modules/model'
import { mapActions, mapGetters } from 'vuex'

export default {
  computed: {
    ...mapGetters('user', {
      data: 'getData',
      meta: 'getMeta'
    })
  },

  beforeRouteEnter (to, from, next) {
    next(vm => {
      vm.fetch({
        path: 'user',
        params: { 'filter[id]': to.params.user }
      })
      next()
    })
  },

  beforeRouteUpdate (to, from, next) {
    this.fetch({
      path: 'user',
      params: { 'filter[id]': to.params.user }
    })
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
