import { get, isEmpty } from 'lodash'

export const formHandler = {
  data () {
    return {
      errorMessage: null,
      formErrors: {},
      statusCode: 200,
      validStatuses: [200, 201]
    }
  },

  methods: {
    async submit (key, params = null) {
      this.formErrors = {}
      this.errorMessage = null
      this.statusCode = 200

      try {
        return await this.$store.dispatch(key, params)
      } catch (error) {
        const { data } = error.response

        this.errorMessage = data.message
        this.formErrors = data.errors
        this.statusCode = data.statusCode
      }
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

    isValid () {
      return isEmpty(this.formErrors) && this.validStatuses.includes(this.statusCode)
    }
  }
}
