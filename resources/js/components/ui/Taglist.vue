<template lang="pug">
b-taglist
  b-tag(
    v-for="item in items"
    :key="item.id"
    :type="getTypeClass(item.type || 'default')"
  ) {{ item.name }}
</template>

<script>
export default {
  props: {
    items: {
      type: Array,
      default: null
    }
  },

  data () {
    return {
      typeClass: {
        category: 'is-dark',
        default: 'is-info',
        language: 'is-link',
        people: 'is-primary'
      }
    }
  },

  methods: {
    getTypeClass (type) {
      return this.typeClass[type.toLowerCase()]
    }
  }
}
</script>
