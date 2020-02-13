<template lang="pug">
nav(class="level")
  div(class="level-left")
    div(class="level-item")
      b-dropdown(v-model="sortFilter" aria-role="list")
        b-button(
          slot="trigger"
          type="is-text"
          size="is-normal"
          class="sorters"
          icon-right="chevron-down"
        ) {{ sortFilterLabel }}

        b-dropdown-item(
          v-for="sorter in sorters"
          :key="sorter.key"
          :value="sorter.key"
          aria-role="listitem"
        ) {{ sorter.label }}

  div(class="level-right")
    div(class="level-item")
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
          b-button(@click.prevent="showTags" icon-right="tag-multiple")
        p(v-if="queryFilter" class="control")
          b-button(@click.prevent="resetFilter" icon-right="filter-remove")
</template>

<script>
import debounce from 'lodash/debounce'

export default {
  props: {
    namespace: {
      type: String,
      required: true
    },

    sorters: {
      type: Array,
      default: () => {
        return [
          { key: 'recommended', label: 'Recommended for You' },
          { key: 'trending', label: 'Trending' },
          { key: 'recent', label: 'Most recent' },
          { key: 'views', label: 'Most viewed' },
          { key: 'popular-week', label: 'Popular this week' },
          { key: 'popular-month', label: 'Popular this month' }
        ]
      }
    }
  },

  computed: {
    state () {
      return this.$store.state[this.namespace]
    },

    sortFilter: {
      get () {
        return this.state.params.sort || this.sorters[0].key
      },

      set (value) {
        this.setFilter({ params: { sort: value } })
      }
    },

    sortFilterActive () {
      return this.filterSort !== this.sorters[0].key
    },

    sortFilterLabel () {
      return this.sorters.find(x => x.key === this.sortFilter).label
    },

    queryFilter: {
      get () {
        return this.state.params['filter[query]'] || ''
      },

      set: debounce(function (value) {
        this.setFilter({ params: { 'filter[query]': value, sort: null } })
      }, 350)
    }
  },

  methods: {
    showTags () {
      this.$store.dispatch('modal/open', {
        component: 'Tags',
        props: { namespace: this.namespace },
        escape: ['escape'],
        fullscreen: true
      })
    },

    resetFilter () {
      this.$store.dispatch(this.namespace + '/reset', {
        params: {
          'filter[query]': null,
          sort: this.sorters[0].key
        }
      })
    },

    setFilter (params) {
      this.$store.dispatch(this.namespace + '/reset', params)
    }
  }
}
</script>
