<template lang="pug">
div(id="app")
  template(v-if="isAuthenticated")
    app-widget
    app-header
    router-view
    app-footer

  template(v-else)
    router-view
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  metaInfo: {
    titleTemplate: (titleChunk) => {
      return titleChunk ? `${titleChunk} | MediaDB` : 'MediaDB'
    },
    htmlAttrs: {
      lang: 'en'
    }
  },

  components: {
    AppHeader: () => import(/* webpackChunkName: "app-header" */ '@/components/layout/AppHeader'),
    AppFooter: () => import(/* webpackChunkName: "app-footer" */ '@/components/layout/AppFooter'),
    AppWidget: () => import(/* webpackChunkName: "app-widget" */ '@/components/layout/AppWidget')
  },

  computed: {
    ...mapGetters('user', [
      'isAuthenticated'
    ])
  }
}
</script>
