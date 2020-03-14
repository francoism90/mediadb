<template lang="pug">

</template>

<script>
export default {
  components: {
    Collection: () => import(/* webpackChunkName: "collect-manager" */ '@/components/collect/Manager'),
    Media: () => import(/* webpackChunkName: "media-manager" */ '@/components/media/Manager'),
    Tags: () => import(/* webpackChunkName: "tags-query" */ '@/components/tags/Query')
  }
}
</script>
