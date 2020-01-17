<template lang="pug">
b-taglist
  b-tag(
    v-for="item in items"
    :key="item.id"
    :size="size"
    :type="getTypeClass(item.type)"
  ) {{ item.name }}
</template>

<script>
export default {
  props: {
    items: {
      type: Array,
      default: null
    },

    size: {
      type: String,
      default: null
    }
  },

  data () {
    return {
      typeClass: {
        genre: 'is-dark',
        language: 'is-link',
        person: 'is-primary'
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
