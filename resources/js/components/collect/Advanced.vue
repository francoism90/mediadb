<template lang="pug">
section
  form(class="column is-paddingless is-two-thirds-tablet")
    b-field(label="Danger Zone")
      b-message(type="is-danger")
        p Once you delete a collection, there is no going back. Please be certain.<br>
        b-button(@click.prevent="remove" type="is-danger") Delete this collection
</template>

<script>
import { formErrorHandler } from '@/components/mixins/form'

export default {
  mixins: [formErrorHandler],

  props: {
    data: {
      type: Object,
      required: true
    },

    meta: {
      type: Object,
      required: true
    }
  },

  methods: {
    remove () {
      this.$buefy.dialog.confirm({
        title: this.data.name,
        message: 'Are you sure you want to delete this collection?',
        type: 'is-danger',
        onConfirm: async () => {
          const { success } = await this.submit('collect_manager/remove', {
            path: 'collect/' + this.data.id
          })

          if (success) {
            this.$buefy.toast.open({
              message: `${this.data.name} was successfully deleted.`,
              type: 'is-success'
            })
          }
        }
      })
    }
  }
}
</script>
