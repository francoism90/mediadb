<template lang="pug">
section(class="section is-medium")
  div(class="container")
    h1(class="title is-4") {{ data.name }}
    div(class="columns is-variable is-multiline")
      logout(class="column is-6")
</template>

<script>
import { mapState } from 'vuex'

export default {
  metaInfo () {
    return {
      title: 'My Account'
    }
  },

  components: {
    Logout: () => import(/* webpackChunkName: "logout" */ '@/components/account/Logout')
  },

  computed: {
    ...mapState('user', [
      'data'
    ])
  }
}
</script>
