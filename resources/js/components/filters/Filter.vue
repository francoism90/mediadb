<template lang="pug">
div
  b-dropdown(v-model="filterModel" aria-role="list")
    b-button(
      slot="trigger"
      type="is-text"
      size="is-normal"
      class="sorters"
      icon-right="chevron-down"
    ) {{ filterLabel }}

    b-dropdown-item(
      v-for="filter in filters"
      :key="filter.key"
      :value="filter.key"
      aria-role="listitem"
    ) {{ filter.label }}
</template>

<script>
export default {
  props: {
    namespace: {
      type: String,
      required: true
    },

    filter: {
      type: String,
      required: true
    },

    filters: {
      type: Array,
      required: true
    }
  },

  computed: {
    state () {
      return this.$store.state[this.namespace]
    },

    filterModel: {
      get () {
        return this.state.params[this.filter] || this.filters[0].key
      },

      set (value) {
        this.$store.dispatch(this.namespace + '/reset', {
          params: { [this.filter]: value }
        })
      }
    },

    filterLabel () {
      return this.filters.find(x => x.key === this.filterModel).label
    }
  }
}
</script>
