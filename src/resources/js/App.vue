<template lang="pug">
div(id="app" v-if="$auth.ready()")
  template(v-if="$auth.check()")
    app-header
    app-widget
    router-view
    app-footer

  template(v-else)
    router-view
</template>

<script>
export default {
  metaInfo: {
    title: '',
    titleTemplate: '%s | MediaDB',
    htmlAttrs: {
      lang: 'en'
    }
  },

  components: {
    AppHeader: () => import(/* webpackChunkName: "app-header" */ '@/components/layout/AppHeader'),
    AppFooter: () => import(/* webpackChunkName: "app-footer" */ '@/components/layout/AppFooter'),
    AppWidget: () => import(/* webpackChunkName: "app-widget" */ '@/components/layout/AppWidget')
  }
}
</script>
