<template lang="pug">
section(:key="module.id")
  filters(v-if="hasFilters" :key="identifier" :id="module.id")

  div(class="columns is-variable is-multiline")
    div(class="column" :class="customClass" v-for="(item, index) in items" :key="index")
      card(:data="item" :module-id="module.id")

  infinite-loading(:identifier="identifier" @infinite="infiniteHandler")
</template>

<script>
export default {
  components: {
    Card: () => import(/* webpackChunkName: "card" */ '@/components/ui/Card'),
    Filters: () => import(/* webpackChunkName: "infinite-filters" */ '@/components/ui/InfiniteFilters')
  },

  props: {
    module: {
      type: Object,
      required: true
    },

    customClass: {
      type: String,
      default: `
        is-full-mobile
        is-half-tablet
        is-one-third-desktop
        is-one-third-widescreen
        is-one-third-fullhd
      `
    },

    hasFilters: {
      type: Boolean,
      default: true
    }
  },

  data () {
    return {
      items: [],
      identifier: +new Date(),
      filterChange: [
        'resetPaginate'
      ]
    }
  },

  computed: {
    paginate () {
      return this.$store.state.paginate[this.module.id]
    }
  },

  created () {
    this.items = this.paginate.data
  },

  mounted () {
    this.$store.subscribeAction((action) => {
      if (this.filterChange.includes(action.type)) {
        if (action.payload.id === this.module.id) {
          this.items = []
          this.identifier += 1
        }
      }
    })
  },

  methods: {
    async infiniteHandler ($state) {
      const { meta } = await this.$store.dispatch('paginate', this.module.id)

      if (meta && meta.current_page <= meta.last_page) {
        this.items = this.paginate.data

        return $state.loaded()
      }

      return $state.complete()
    }
  }
}
</script>
