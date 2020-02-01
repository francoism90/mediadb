import { filter, includes, uniqBy } from 'lodash'

export const tagsHandler = {
  props: {
    preSelectedTags: {
      type: Array,
      default: function () {
        return []
      }
    },

    tagsProps: {
      type: Object,
      default: function () {
        return { size: 0 }
      }
    }
  },

  data () {
    return {
      tagItems: [],
      tagsFiltered: [],
      tagsSelected: []
    }
  },

  methods: {
    setSelectedTags (tags) {
      if (tags.length) {
        this.tagsSelected = uniqBy(tags, 'id')
      } else {
        this.tagsSelected = []
      }
    },

    getFilteredTags (text) {
      this.tagsFiltered = this.tagItems.filter((option) => {
        return option.name
          .toString()
          .toLowerCase()
          .indexOf(text.toLowerCase()) >= 0
      })
    },

    getTagsByType (type) {
      return filter(this.tagItems, ['type', type])
    },

    getTagsOfType (type, tags) {
      const matches = filter(this.getTagsByType(type), function (tag) {
        return includes(tags, tag.id)
      })

      return matches
    }
  },

  async created () {
    const { data } = await this.$store.dispatch('tags/fetch', this.tagsProps)

    this.tagItems = data
    this.tagsFiltered = data
  },

  mounted () {
    this.setSelectedTags(this.preSelectedTags)
  }
}
