<template lang="pug">
div(id="app" v-if="$auth.ready()")
  template(v-if="$auth.check()")
    app-header
    router-view
    back-to-top
    app-footer

  template(v-else)
    router-view
</template>

<script>
export default {
  metaInfo: {
    title: 'Browse',
    titleTemplate: '%s | MediaDB',
    htmlAttrs: {
      lang: 'en'
    }
  },

  components: {
    AppHeader: () => import(/* webpackChunkName: "app-header" */ '@/components/layout/AppHeader'),
    AppFooter: () => import(/* webpackChunkName: "app-footer" */ '@/components/layout/AppFooter'),
    BackToTop: () => import(/* webpackChunkName: "app-to-top" */ '@/components/ui/BackToTop')
  }
}
</script>
