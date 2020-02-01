<template lang="pug">
section
  to-top

  b-modal(
      :active.sync="modal.active || false"
      :can-cancel="modal.escape || ['escape']"
      :custom-class="modal.class || null"
      :full-screen="modal.fullscreen || false"
      :animation="modal.animation || 'fade'"
      :aria-role="modal.role || null"
      :has-modal-card="modal.card || false"
      :width="modal.width || 960"
      trap-focus
    )
      component(:is="modal.component" v-bind="modal.props || {}")
</template>

<script>
export default {
  components: {
    ToTop: () => import(/* webpackChunkName: "top" */ '@/components/ui/Top'),
    ModalMedia: () => import(/* webpackChunkName: "modal-search" */ '@/components/modal/Media'),
    ModalSearch: () => import(/* webpackChunkName: "modal-search" */ '@/components/modal/Search')
  },

  computed: {
    modal () {
      return this.$store.state.modal
    }
  }
}
</script>
