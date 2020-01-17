<template lang="pug">
section(class="section")
  div(class="container-fluid")
    filters(:id="id")
    infinite(:id="id" :dispatcher="dispatcher" :params="params")
</template>

<script>
export default {
  components: {
    Filters: () => import(/* webpackChunkName: "filters" */ '@/components/ui/InfiniteFilters'),
    Infinite: () => import(/* webpackChunkName: "infinite" */ '@/components/ui/Infinite')
  },

  data () {
    return {
      id: 'feed',
      dispatcher: 'media/fetch',
      params: {
        include: 'model,tags'
      }
    }
  }
}
</script>
