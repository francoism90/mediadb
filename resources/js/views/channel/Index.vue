<template lang="pug">
router-view(v-if="data.id" :channel-data="data" :channel-meta="meta")
</template>

<script>
import modelModule from '@/store/modules/model'
import { mapGetters } from 'vuex'

export default {
  computed: {
    ...mapGetters('channel', {
      data: 'getData',
      meta: 'getMeta'
    })
  },

  beforeRouteEnter (to, from, next) {
    next(vm => {
      vm.fetch(to.params.channel)
      next()
    })
  },

  beforeRouteUpdate (to, from, next) {
    this.fetch(to.params.channel)
    next()
  },

  created () {
    if (!this.$store.state.channel) {
      this.$store.registerModule('channel', modelModule)
    }
  },

  methods: {
    async fetch (id) {
      await this.$store.dispatch('channel/fetch', {
        path: 'user/' + id
      })
    }
  }
}
</script>
