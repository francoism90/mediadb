import { get, isEmpty, uniqBy } from 'lodash'

export const formErrorHandler = {
  data () {
    return {
      errorMessage: null,
      formErrors: {},
      response: null,
      validStatuses: [200, 201]
    }
  },

  methods: {
    async submit (key, params = null) {
      this.errorMessage = null
      this.formErrors = {}
      this.response = null

      try {
        this.response = await this.$store.dispatch(key, params)
      } catch (error) {
        const { data } = error.response

        this.errorMessage = data.message
        this.formErrors = data.errors
      }

      return { success: this.isSuccess(), data: this.response }
    },

    error (field) {
      return get(this.formErrors, field)
    },

    firstError (field) {
      return get(this.formErrors, `${field}[0]`)
    },

    fieldType (field, errorClass = 'is-danger') {
      return this.error(field) ? errorClass : ''
    },

    isSuccess () {
      if (!isEmpty(this.errorMessage) || !isEmpty(this.formErrors) || isEmpty(this.response)) {
        return false
      }

      return this.validStatuses.includes(this.response.status)
    }
  }
}

export const formTagsHandler = {
  data () {
    return {
      tagsSelected: [],
      tagsFiltered: [],
      tagsItems: []
    }
  },

  methods: {
    setTagItems (tags) {
      this.tagsItems = tags
      this.tagsFiltered = tags
    },

    setSelectedTags (tags) {
      this.tagsSelected = tags.length ? uniqBy(tags, 'id') : []
    },

    getFilteredTags (text) {
      this.tagsFiltered = this.tagsItems.filter((option) => {
        return option.name
          .toString()
          .toLowerCase()
          .indexOf(text.toLowerCase()) >= 0
      })
    }
  }
}
