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
        maxtags="15"
        maxlength="255"
        autocomplete
        placeholder="Add tag"
        field="name"
        :allow-new="true"
        :loading="tagsLoading"
        @typing="fetchTags"
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
import { mapGetters } from 'vuex'
import debounce from 'lodash/debounce'
import paginateModule from '@/store/modules/paginate'

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

  data () {
    return {
      body: {
        name: this.data.name,
        description: this.data.description,
        tags: []
      }
    }
  },

  computed: {
    ...mapGetters({
      tagsFiltered: 'taginput/getData',
      tagsLoading: 'taginput/isLoading',
      tagsSelected: 'taginput/getSelected'
    }),

    tags: {
      get () {
        return this.tagsSelected
      },

      set (value) {
        this.$store.commit('taginput/setSelected', value)
      }
    }
  },

  created () {
    this.prepareTags()
  },

  methods: {
    prepareTags () {
      if (!this.$store.state.taginput) {
        this.$store.registerModule('taginput', paginateModule)
      }

      this.$store.dispatch('taginput/create', { path: 'tags' })
      this.$store.commit('taginput/setSelected', this.data.relationships.tags || [])
    },

    fetchTags: debounce(async function (name) {
      this.$store.dispatch('taginput/reset', {
        params: {
          'filter[query]': name,
          'page[size]': 25
        }
      })

      await this.$store.dispatch('taginput/fetch')
    }, 350),

    async update () {
      // Add final selection to body
      this.body.tags = this.tags

      const { success = false } = await this.submit('collect_manager/update', {
        path: 'collect/' + this.data.id,
        body: this.body
      })

      if (success) {
        await this.$store.dispatch('collect_manager/refresh')

        this.$buefy.toast.open({
          message: `${this.data.name} was successfully updated.`,
          type: 'is-success'
        })
      }
    }
  }
}
</script>
