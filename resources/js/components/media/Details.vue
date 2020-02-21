<template lang="pug">
section()
  form(class="column is-paddingless is-two-thirds-tablet")
    b-field(label="Name" :type="fieldType('name')" :message="firstError('name')")
      b-input(
        type="input"
        v-model.trim="body.name"
        required
        minlength="1"
        maxlength="255"
        placeholder="Name"
      )

    b-field(label="Tags" :type="fieldType('tags')" :message="firstError('tags')")
      b-taginput(
        v-model="tags"
        class="is-clearfix"
        :data="tagsFiltered"
        maxtags="25"
        maxlength="255"
        autocomplete
        placeholder="Add a tag"
        field="name"
        @typing="setTagsFiltered"
      )
        template(v-slot:default="props")
          span {{ props.option.name }}

    b-field(label="Description" :type="fieldType('description')" :message="firstError('description')")
      b-input(
        v-model.trim="body.description"
        type="textarea"
        custom-class="has-fixed-size"
        rows="5"
        minlength="0"
        maxlength="1024"
        placeholder="Enter markdown"
      )

    b-field
      b-button(@click.prevent="update" type="is-primary") Save changes
</template>

<script>
import { formErrorHandler } from '@/components/mixins/form'
import { mapActions, mapGetters, mapMutations } from 'vuex'
import tagsModule from '@/store/modules/tags'

export default {
  mixins: [formErrorHandler],

  props: {
    item: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      body: {
        name: this.item.name,
        description: this.item.description,
        tags: []
      }
    }
  },

  computed: {
    ...mapGetters({
      tagsFiltered: 'taginput/getFiltered',
      tagsSelected: 'taginput/getSelected'
    }),

    tags: {
      get () {
        return this.tagsSelected
      },

      set (value) {
        this.setTagsSelected(value)
      }
    }
  },

  created () {
    this.prepareTags()
  },

  beforeDestroy () {
    this.$store.unregisterModule('taginput')
  },

  methods: {
    ...mapActions({
      fetchTags: 'taginput/fetch'
    }),

    ...mapMutations({
      setTagsFiltered: 'taginput/setFiltered',
      setTagsSelected: 'taginput/setSelected'
    }),

    async prepareTags () {
      if (!this.$store.state.taginput) {
        this.$store.registerModule('taginput', tagsModule)
      }

      await this.fetchTags({ path: 'tags' })

      this.setTagsSelected(this.item.relationships.tags || [])
    },

    async update () {
      // Attach tags model
      this.body.tags = this.tags

      const { success = false } = await this.submit('manager/update', {
        path: 'media/' + this.item.id,
        body: this.body
      })

      if (success) {
        await this.$store.dispatch('manager/fetch', { path: 'media/' + this.item.id })

        this.$buefy.toast.open({
          message: `${this.body.name} was successfully updated.`,
          type: 'is-success'
        })
      }
    }
  }
}
</script>
