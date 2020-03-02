<template lang="pug">
router-view(v-if="data.id" :user-data="data" :user-meta="meta")
</template>

<script>
import modelModule from '@/store/modules/model'
import { mapGetters } from 'vuex'

export default {
  computed: {
    ...mapGetters('user', {
      data: 'getData',
      meta: 'getMeta'
    })
  },

  beforeRouteEnter (to, from, next) {
    next(vm => {
      vm.fetch(to.params.user)
      next()
    })
  },

  beforeRouteUpdate (to, from, next) {
    this.fetch(to.params.user)
    next()
  },

  created () {
    if (!this.$store.state.user) {
      this.$store.registerModule('user', modelModule)
    }
  },

  methods: {
    async fetch (id) {
      await this.$store.dispatch('user/fetch', {
        path: 'user',
        params: { 'filter[id]': id }
      })
    }
  }
}
</script>
