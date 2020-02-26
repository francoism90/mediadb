<template lang="pug">
div
  b-dropdown(v-model="sortModel" aria-role="list")
    b-button(
      slot="trigger"
      type="is-text"
      size="is-normal"
      class="sorters"
      icon-right="chevron-down"
    ) {{ sortLabel }}

    b-dropdown-item(
      v-for="sorter in sorters"
      :key="sorter.key"
      :value="sorter.key"
      aria-role="listitem"
    ) {{ sorter.label }}
</template>

<script>
export default {
  props: {
    namespace: {
      type: String,
      required: true
    },

    sorters: {
      type: Array,
      required: true
    }
  },

  computed: {
    state () {
      return this.$store.state[this.namespace]
    },

    sortModel: {
      get () {
        return this.state.params.sort || this.sorters[0].key
      },

      set (value) {
        this.$store.dispatch(this.namespace + '/reset', {
          params: { sort: value }
        })
      }
    },

    sortLabel () {
      return this.sorters.find(x => x.key === this.sortModel).label
    }
  }
}
</script>
