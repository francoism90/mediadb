<template lang="pug">
div
  b-field
    b-input(
      v-model.trim="queryFilter"
      type="search"
      minlength="1"
      maxlength="255"
      :has-counter="false"
      placeholder="Filter items"
    )

    p(v-if="tags" class="control")
      b-button(@click.prevent="filterTags" icon-right="tag-multiple")
</template>

<script>
import debounce from 'lodash/debounce'

export default {
  props: {
    namespace: {
      type: String,
      required: true
    },

    filter: {
      type: Array,
      default: null
    },

    tags: {
      type: Boolean,
      default: true
    },

    query: {
      type: String,
      default: null
    }
  },

  computed: {
    state () {
      return this.$store.state[this.namespace]
    },

    queryFilter: {
      get () {
        return this.state.params['filter[query]'] || this.query
      },

      set: debounce(function (value) {
        this.$store.dispatch(this.namespace + '/reset', {
          params: {
            'filter[query]': value || null,
            sort: null
          }
        })
      }, 350)
    }
  },

  methods: {
    filterTags () {
      this.$store.dispatch('modal/open', {
        component: 'Tags',
        props: { paginate: this.namespace },
        escape: ['escape'],
        fullscreen: true
      })
    }
  }
}
</script>
