<template lang="pug">
div
  b-field
    p(class="control")
      b-input(
        v-model.trim="queryFilter"
        type="search"
        minlength="1"
        maxlength="255"
        :has-counter="false"
        placeholder="Filter items"
      )

    p(class="control")
      b-button(@click.prevent="filterTags" icon-right="tag-multiple")

    p(v-if="filtersActive()" class="control")
      b-button(@click.prevent="resetFilters" icon-right="filter-remove")
</template>

<script>
import debounce from 'lodash/debounce'

export default {
  props: {
    namespace: {
      type: String,
      required: true
    },

    filter: {
      type: String,
      default: null
    },

    sorters: {
      type: Array,
      default: null
    },

    query: {
      type: String,
      default: null
    }
  },

  computed: {
    state () {
      return this.$store.state[this.namespace]
    },

    queryFilter: {
      get () {
        return this.state.params['filter[query]'] || this.query
      },

      set: debounce(function (value) {
        this.$store.dispatch(this.namespace + '/reset', {
          params: { 'filter[query]': value, sort: null }
        })
      }, 350)
    }
  },

  methods: {
    filtersActive () {
      return (
        this.state.params[this.filter] ||
        this.state.params['filter[query]'] ||
        this.sortActive()
      )
    },

    filterTags () {
      this.$store.dispatch('modal/open', {
        component: 'Tags',
        props: { namespace: this.namespace },
        escape: ['escape'],
        fullscreen: true
      })
    },

    resetFilters () {
      this.$store.dispatch(this.namespace + '/reset', {
        params: {
          [this.filter]: null,
          'filter[query]': null,
          sort: this.sorters[0].key
        }
      })
    },

    sortActive () {
      if (!this.state.params.sort || !this.sorters || !this.sorters.length) {
        return false
      }

      return this.state.params.sort !== this.sorters[0].key
    }
  }
}
</script>
