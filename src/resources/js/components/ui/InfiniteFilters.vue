<template lang="pug">
section(:key="id" class="filters")
  nav(class="level")
    div(class="level-left")
      div(class="level-item")
          b-dropdown(v-model="sort" aria-role="list")
            b-button(
              slot="trigger"
              type="is-text"
              :class="{ 'is-active': hasSort }"
              icon-right="chevron-down"
            ) {{ sortLabel }}

            b-dropdown-item(
              v-for="sorter in sorters"
              :key="sorter.key"
              :value="sorter.key"
              aria-role="listitem"
            ) {{ sorter.label }}

          b-button(v-if="hasSort || hasTags" class="is-hidden-desktop" type="is-text" icon-right="filter-remove" @click="resetFilters()")
          b-button(v-else class="is-hidden-desktop" type="is-text" icon-right="refresh" @click="forceReload()")

      div(class="level-item")
        b-dropdown(
          v-for="type in types"
          :key="type.key"
          v-if="tagType(type.key).length"
          v-model="tags"
          multiple
          aria-role="list"
        )
          b-button(
            slot="trigger"
            type="is-text"
            :class="{ 'is-active': tagTypeActive(type.key) }"
            icon-right="chevron-down"
          ) {{ type.label }}

          b-dropdown-item(
            v-for="tag in tagType(type.key)"
            :key="tag.id"
            :value="tag.id"
            aria-role="listitem"
          ) {{ tag.name }}

    div(class="level-right is-hidden-touch")
      div(class="level-item")
        b-button(v-if="hasSort || hasTags" type="is-text" icon-right="filter-remove" @click="resetFilters()")
        b-button(v-else type="is-text" icon-right="refresh" @click="forceReload()")
</template>

<script>
import { mapActions, mapGetters, mapState } from 'vuex'

export default {
  props: {
    id: {
      type: [Number, String],
      required: true
    }
  },

  computed: {
    ...mapGetters({
      tagType: 'tags/type'
    }),

    ...mapState({
      sorters: state => state.sorters,
      types: state => state.tags.types
    }),

    paginate () {
      return this.$store.state.paginate[this.id]
    },

    sort: {
      get () {
        return this.paginate.props.sort || this.sorters[0].key
      },

      set (value) {
        this.resetPaginate({ id: this.id, props: { sort: value } })
      }
    },

    tags: {
      get () {
        return this.paginate.props.tags || []
      },

      set (value) {
        this.resetPaginate({ id: this.id, props: { tags: value } })
      }
    },

    hasSort () {
      return this.sort !== this.sorters[0].key
    },

    hasTags () {
      return this.tags.length
    },

    sortLabel () {
      return this.sorters.find(x => x.key === this.sort).label
    }
  },

  async created () {
    await this.fetchTags(this.id)
  },

  methods: {
    ...mapActions({
      fetchTags: 'tags/filtered',
      resetPaginate: 'resetPaginate'
    }),

    forceReload () {
      const start = this.paginate.props.initialized
      const end = new Date()
      const secs = Math.floor((end - start) / 1000 % 60)

      if (secs > 1) {
        this.resetPaginate({ id: this.id, props: { initialized: new Date() } })
      }
    },

    resetFilters () {
      this.resetPaginate({ id: this.id, props: { sort: null, tags: null } })
    },

    tagTypeActive (type) {
      const tagsOfType = this.tagType(type)

      for (const tag of tagsOfType) {
        if (this.tags.includes(tag.id)) {
          return true
        }
      }

      return false
    }
  }
}
</script>
