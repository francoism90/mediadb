<template lang="pug">
div(class="modal-card")
  header(class="modal-card-head")
    p(class="modal-card-title") {{ data.name }}

  section(class="modal-card-body")
    b-field(label="Name" :type="fieldType('name')" :message="firstError('name')")
      b-input(
        v-model.trim="body.name"
        type="text"
        :has-counter="false"
        minlength="1"
        maxlength="255"
        placeholder="Name"
      )

    b-field(label="Tags" :addons="false" :type="fieldType('tags')" :message="firstError('tags')")
      b-taginput(
        v-model="tags"
        :data="filteredTags"
        autocomplete
        :open-on-focus="true"
        maxtags="15"
        :has-counter="false"
        type="is-dark"
        field="name"
        placeholder="Add a tag"
        @typing="getFilteredTags"
      )
        template(slot-scope="props")
          <strong>{{ props.option.type }}</strong>: {{ props.option.name }}

    b-field(label="Description" :type="fieldType('description')" :message="firstError('description')")
      b-input(
        v-model.trim="body.description"
        type="textarea"
        maxlength="1024"
        :has-counter="false"
        placeholder="Enter markdown"
      )

    b-field(label="Status" :addons="false" :type="fieldType('status')" :message="firstError('status')")
      b-field
        b-radio(v-model="body.status" native-value="public") Public

      b-field
        b-radio(v-model="body.status" native-value="private") Private

  footer(class="modal-card-foot")
    b-button(@click="update()" icon-left="check-bold" type="is-primary") Update
    b-button(@click="destroy()" icon-left="trash-can" type="is-danger") Delete
</template>

<script>
import { mapActions, mapGetters, mapMutations, mapState } from 'vuex'
import { formHandler } from '@/components/mixins'

export default {
  components: {
    Tags: () => import(/* webpackChunkName: "tags" */ '@/components/ui/Tags')
  },

  mixins: [formHandler],

  props: {
    data: {
      type: Object,
      required: true
    },

    meta: {
      type: Object,
      default: null
    },

    userData: {
      type: Object,
      required: true
    },

    userMeta: {
      type: Object,
      default: null
    }
  },

  data () {
    return {
      filteredTags: [],
      body: {
        id: this.data.id,
        name: this.data.name,
        description: this.data.description,
        status: this.data.status,
        tags: []
      }
    }
  },

  computed: {
    ...mapGetters({
      tagsActive: 'tags/active',
      tagType: 'tags/type'
    }),

    ...mapState({
      tagItems: state => state.tags.data
    }),

    tags: {
      get () {
        return this.tagsActive
      },

      set (tags) {
        this.setActiveTags(tags)
      }
    }
  },

  async mounted () {
    await this.fetchTags()

    this.filteredTags = this.tagItems

    if (this.data.relationships.tags.length) {
      this.setActiveTags(this.data.relationships.tags)
    }
  },

  methods: {
    ...mapActions({
      fetchTags: 'tags/all',
      delete: 'media/delete',
      get: 'media/get'
    }),

    ...mapMutations({
      setActiveTags: 'tags/setActiveTags'
    }),

    getFilteredTags (text) {
      this.filteredTags = this.tagItems.filter((option) => {
        return option.name
          .toString()
          .toLowerCase()
          .indexOf(text.toLowerCase()) >= 0
      })
    },

    async update () {
      // Attach selected tags to body
      this.body.tags = this.tags || []

      await this.submit('media/update', this.body)

      if (this.isValid()) {
        // Refresh model
        this.get(this.data.id)

        this.$parent.close()
      }
    },

    destroy () {
      this.$buefy.dialog.confirm({
        title: this.data.name,
        message: 'Are you sure you want to delete this item?',
        type: 'is-danger',
        onConfirm: () => {
          this.confirmDelete()
        }
      })
    },

    async confirmDelete () {
      await this.delete(this.data.id)

      this.$router.push({ name: 'home' })
      this.$parent.close()
    }
  }
}
</script>
