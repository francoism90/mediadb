<template lang="pug">
section(class="filters")
  nav(:key="id" class="level")
    div(class="level-left")
      div(class="level-item")
          b-dropdown(v-model="sortFilter" aria-role="list")
            b-button(
              slot="trigger"
              type="is-text"
              icon-right="chevron-down"
            ) {{ sortLabel }}

            b-dropdown-item(
              v-for="sorter in sorters"
              :key="sorter.key"
              :value="sorter.key"
              aria-role="listitem"
            ) {{ sorter.label }}

      template(v-if="filtersOpen")
        div(class="level-item")
          b-dropdown(
            v-for="type in tagTypes"
            :key="type.key"
            v-if="tagType(type.key).length"
            v-model="tagFilter"
            multiple
            aria-role="list"
          )
            b-button(
              slot="trigger"
              type="is-text"
              :class="{ 'is-dark': tagTypeActive(type.key) }"
              icon-right="chevron-down"
            ) {{ type.label }}

            b-dropdown-item(
              v-for="tag in tagType(type.key)"
              :key="tag.id"
              :value="tag.id"
              aria-role="listitem"
            ) {{ tag.name }}

    div(class="level-right")
      div(class="level-item")
        b-field
          b-input(
            v-debounce:600ms="resetPaginate"
            v-model.lazy="queryFilter"
            type="search"
            icon="magnify"
            class="query"
            placeholder="Search"
          )

          p(class="control")
            b-tooltip(label="Toggle Filters" type="is-dark" position="is-bottom")
              b-button(@click="filtersOpen = !filtersOpen" icon-right="filter")
</template>

<script>
import { mapActions, mapGetters, mapMutations, mapState } from 'vuex'

export default {
  props: {
    id: {
      type: [Number, String],
      required: true
    }
  },

  data () {
    return {
      filtersOpen: false,
      tagTypes: [
        {
          key: 'Genre',
          label: 'Genres'
        },
        {
          key: 'Person',
          label: 'Persons'
        },
        {
          key: 'Language',
          label: 'Languages'
        }
      ]
    }
  },

  computed: {
    ...mapGetters({
      meta: 'paginateMeta',
      parameters: 'paginateParams',
      tagType: 'tags/type'
    }),

    ...mapState({
      sorters: state => state.paginateSorters
    }),

    sortFilter: {
      get () {
        return this.parameters.sort || this.sorters[0].key
      },

      set (value) {
        this.setPaginateParams({ sort: value })
        this.resetPaginate()
      }
    },

    tagFilter: {
      get () {
        return this.parameters.tags || []
      },

      set (value) {
        this.setPaginateParams({ tags: value })
        this.resetPaginate()
      }
    },

    queryFilter: {
      get () {
        return this.parameters.query || null
      },

      set (value) {
        this.setPaginateParams({ sort: null, query: value })
      }
    },

    sortActive () {
      return this.sortFilter !== this.sorters[0].key
    },

    sortLabel () {
      return this.sorters.find(x => x.key === this.sortFilter).label
    }
  },

  async created () {
    await this.fetchTags(this.id)
  },

  methods: {
    ...mapActions({
      fetchTags: 'tags/filtered'
    }),

    ...mapMutations([
      'resetPaginate',
      'setPaginateParams'
    ]),

    tagTypeActive (type) {
      const tags = this.tagType(type)

      for (const tag of tags) {
        if (this.tagFilter.includes(tag.id)) {
          return true
        }
      }

      return false
    }
  }
}
</script>
