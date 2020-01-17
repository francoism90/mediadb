<template lang="pug">
router-view(:user-data="data" :user-meta="meta")
</template>

<script>
import { mapActions, mapState } from 'vuex'

export default {
  computed: {
    ...mapState({
      data: state => state.user.data,
      meta: state => state.user.meta
    })
  },

  beforeRouteEnter (to, from, next) {
    next(vm => {
      vm.get(to.params.user)
      next()
    })
  },

  beforeRouteUpdate (to, from, next) {
    this.get(to.params.user)
    next()
  },

  methods: {
    ...mapActions({
      get: 'user/get'
    })
  }
}
</script>
