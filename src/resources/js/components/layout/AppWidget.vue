<template lang="pug">
section
  back-to-top

  b-modal(
      :active.sync="modal.active || false"
      :can-cancel="modal.escape || ['escape']"
      :custom-class="modal.class || null"
      :full-screen="modal.fullscreen || false"
      :animation="modal.animation || 'fade'"
      aria-modal
      aria-role="dialog"
      trap-focus
    )
      component(v-bind:is="modal.component || false")
</template>

<script>
export default {
  components: {
    BackToTop: () => import(/* webpackChunkName: "back-to-top" */ '@/components/ui/BackToTop'),
    ModalSearch: () => import(/* webpackChunkName: "modal-search" */ '@/components/modal/Search')
  },

  computed: {
    modal () {
      return this.$store.state.modal
    }
  }
}
</script>
