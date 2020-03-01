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

    b-field(label="Collections" :type="fieldType('collect')" :message="firstError('collect')")
      b-taginput(
        v-model="collect"
        class="is-clearfix"
        :data="collectFiltered"
        maxtags="25"
        maxlength="255"
        autocomplete
        placeholder="Add collection"
        field="name"
        :allow-new="true"
        :loading="collectLoading"
        @typing="fetchCollect"
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
        collect: [],
        tags: []
      }
    }
  },

  computed: {
    ...mapGetters({
      collectFiltered: 'collectinput/getData',
      collectLoading: 'collectinput/isLoading',
      collectSelected: 'collectinput/getSelected',
      tagsFiltered: 'taginput/getData',
      tagsLoading: 'taginput/isLoading',
      tagsSelected: 'taginput/getSelected'
    }),

    collect: {
      get () {
        return this.collectSelected
      },

      set (value) {
        this.$store.commit('collectinput/setSelected', value)
      }
    },

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
    this.prepareCollect()
    this.prepareTags()
  },

  beforeDestroy () {
    this.$store.unregisterModule('collectinput')
    this.$store.unregisterModule('taginput')
  },

  methods: {
    prepareCollect () {
      if (!this.$store.state.collectinput) {
        this.$store.registerModule('collectinput', paginateModule)
      }

      this.$store.dispatch('collectinput/create', {
        path: 'collect',
        params: { 'filter[type]': 'user' }
      })
      this.$store.commit('collectinput/setSelected', this.item.usercollect || [])
    },

    prepareTags () {
      if (!this.$store.state.taginput) {
        this.$store.registerModule('taginput', paginateModule)
      }

      this.$store.dispatch('taginput/create', { path: 'tags' })
      this.$store.commit('taginput/setSelected', this.item.relationships.tags || [])
    },

    fetchCollect: debounce(async function (name) {
      this.$store.dispatch('collectinput/reset', {
        params: {
          'filter[query]': name,
          'page[size]': 25
        }
      })

      await this.$store.dispatch('collectinput/fetch')
    }, 400),

    fetchTags: debounce(async function (name) {
      this.$store.dispatch('taginput/reset', {
        params: {
          'filter[query]': name,
          'page[size]': 25
        }
      })

      await this.$store.dispatch('taginput/fetch')
    }, 400),

    async update () {
      // Add final selections to body
      this.body.collect = this.collect
      this.body.tags = this.tags

      const { success = false } = await this.submit('manager/update', {
        path: 'media/' + this.item.id,
        body: this.body
      })

      if (success) {
        await this.$store.dispatch('manager/refresh')

        this.$buefy.toast.open({
          message: `${this.body.name} was successfully updated.`,
          type: 'is-success'
        })
      }
    }
  }
}
</script>
