<template lang="pug">
section(v-if="userData.id")
  p {{ userData.id }}
</template>

<script>
export default {
  metaInfo () {
    if (this.userData && this.userData.name) {
      return {
        title: this.userData.name
      }
    }
  },

  props: {
    userData: {
      type: Object,
      required: true
    },

    userMeta: {
      type: Object,
      default: null
    }
  }
}
</script>
