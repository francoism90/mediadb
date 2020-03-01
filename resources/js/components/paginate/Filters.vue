<template lang="pug">
div
  b-dropdown(v-model="filter" aria-role="list")
    b-button(
      slot="trigger"
      type="is-text"
      size="is-normal"
      icon-right="chevron-down"
    ) {{ label }}

    b-dropdown-item(
      v-for="item in items"
      :key="item.key"
      :value="item.key"
      aria-role="listitem"
    ) {{ item.label }}
</template>

<script>
export default {
  props: {
    namespace: {
      type: String,
      required: true
    },

    field: {
      type: String,
      required: true
    },

    items: {
      type: Array,
      required: true
    }
  },

  computed: {
    state () {
      return this.$store.state[this.namespace]
    },

    filter: {
      get () {
        return this.state.params[this.field] || this.items[0].key
      },

      set (value) {
        this.$store.dispatch(this.namespace + '/reset', {
          params: { [this.field]: value || null }
        })
      }
    },

    label () {
      return this.items.find(x => x.key === this.filter).label
    }
  }
}
</script>
