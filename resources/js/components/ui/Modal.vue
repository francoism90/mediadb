<template lang="pug">
div(class="modal-container")
  section(class="section")
    div(class="container")
      header
        slot(name="header")
           b-button(
            type="is-text"
            class="modal-exit"
            icon-right="close"
            size="is-large"
            @click.prevent="closeModal"
          )

      main
        slot

      footer
        slot(name="footer")
</template>

<script>
export default {
  props: {
    namespace: {
      type: String,
      default: 'modal'
    }
  },

  methods: {
    closeModal () {
      return this.$store.dispatch(this.namespace + '/close')
    }
  }
}
</script>
