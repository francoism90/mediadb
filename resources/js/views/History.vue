<template lang="pug">
section(class="section is-medium")
  div(class="container")
    filters(
      namespace="history"
      filter="filter[viewed_at]"
      :filters="filters()"
      :sorters="null"
    )
    infinite(namespace="history" :api-route="apiRoute" type="media")
</template>

<script>
import paginateModule from '@/store/modules/paginate'
import moment from 'moment'

export default {
  metaInfo () {
    return {
      title: 'Viewing Activity'
    }
  },

  components: {
    Filters: () => import(/* webpackChunkName: "filters" */ '@/components/filters/Level'),
    Infinite: () => import(/* webpackChunkName: "infinite" */ '@/components/ui/Infinite')
  },

  data () {
    return {
      apiRoute: {
        path: 'media',
        params: {
          include: 'model,tags',
          'filter[viewed_at]': moment().format('YYYY-MM-DD')
        }
      }
    }
  },

  created () {
    if (!this.$store.state.history) {
      this.$store.registerModule('history', paginateModule)
    }
  },

  beforeDestroy () {
    this.$store.unregisterModule('history')
  },

  methods: {
    filters () {
      const dates = []

      for (let i = 0; i < 14; i++) {
        const date = moment()

        // Count backwards
        date.subtract(i, 'day')

        // Push as filter
        dates.push({
          key: date.format('YYYY-MM-DD'),
          label: date.format('dddd, D MMMM Y')
        })
      }

      return dates
    }
  }
}
</script>
