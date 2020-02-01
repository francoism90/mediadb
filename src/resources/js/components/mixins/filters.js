import { mapActions, mapState } from 'vuex'

export const filtersHandler = {
  props: {
    moduleId: {
      type: [Number, String],
      required: true
    }
  },

  computed: {
    ...mapState({
      sorters: state => state.sorters,
      taggers: state => state.tags.taggers
    }),

    paginateModule () {
      return this.$store.state.paginate[this.moduleId]
    },

    filterSort: {
      get () {
        return this.paginateModule.props.sort || this.sorters[0].key
      },

      set (value) {
        this.resetPaginate({ id: this.moduleId, props: { sort: value } })
      }
    },

    filterTags: {
      get () {
        return this.paginateModule.props.tags || []
      },

      set (value) {
        this.resetPaginate({ id: this.moduleId, props: { tags: value } })
      }
    },

    hasFilterSort () {
      return this.filterSort !== this.sorters[0].key
    },

    hasFilterTags () {
      return this.filterTags.length
    },

    filterSortLabel () {
      return this.sorters.find(x => x.key === this.filterSort).label
    }
  },

  methods: {
    ...mapActions({
      resetPaginate: 'resetPaginate'
    }),

    resetFilters () {
      this.resetPaginate({ id: this.moduleId, props: { sort: null, tags: null } })
    }
  }
}
