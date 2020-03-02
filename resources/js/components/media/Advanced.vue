<template lang="pug">
section
  form(class="column is-paddingless is-two-thirds-tablet")
    b-field(label="Danger Zone")
      b-message(type="is-danger")
        p Once you delete a video, there is no going back. Please be certain.<br>
        b-button(@click.prevent="remove" type="is-danger") Delete this video
</template>

<script>
import { formErrorHandler } from '@/components/mixins/form'

export default {
  mixins: [formErrorHandler],

  props: {
    item: {
      type: Object,
      required: true
    }
  },

  methods: {
    remove () {
      this.$buefy.dialog.confirm({
        title: this.item.name,
        message: 'Are you sure you want to delete this video?',
        type: 'is-danger',
        onConfirm: async () => {
          const { success } = await this.submit('media_manager/remove', {
            path: 'media/' + this.item.id
          })

          if (success) {
            this.$buefy.toast.open({
              message: `${this.item.name} was successfully deleted.`,
              type: 'is-success'
            })
          }
        }
      })
    }
  }
}
</script>
