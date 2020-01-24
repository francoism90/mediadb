<template lang="pug">
main
  section(class="section search-form")
    b-field(grouped position="is-right" :addons="false")
      b-button(type="is-text" icon-right="close" size="is-large" @click="$store.dispatch('destroyModal')")

    b-field(label="Search")
      b-input(
        ref="search"
        v-model.trim="query"
        :has-counter="false"
        custom-class="has-text-weight-medium is-size-2"
        expanded
        minlength="1"
        maxlength="255"
        name="query"
        placeholder="Enter keywords"
        type="search"
      )

  section(class="section search-results")
    infinite(:module="mediaPaginate" :custom-class="mediaPaginate.customClass" :has-filters="false")
</template>

<script>
import debounce from 'lodash/debounce'

export default {
  components: {
    Infinite: () => import(/* webpackChunkName: "infinite" */ '@/components/ui/Infinite')
  },

  data () {
    return {
      mediaPaginate: {
        id: 'search',
        props: {
          dispatcher: 'media/fetch',
          include: 'model,tags',
          sort: null,
          query: ''
        },
        customClass: `
          is-full-mobile
          is-half-tablet
          is-one-third-desktop
          is-one-quarter-widescreen
          is-one-quarter-fullhd
        `
      }
    }
  },

  computed: {
    paginate () {
      return this.$store.state.paginate[this.mediaPaginate.id]
    },

    query: {
      get () {
        return this.paginate.props.query || ''
      },

      set: debounce(function (value) {
        this.$store.dispatch('resetPaginate', {
          id: this.mediaPaginate.id, props: { query: value }
        })
      }, 600)
    }
  },

  created () {
    this.$store.dispatch('createPaginate', this.mediaPaginate)
  },

  mounted () {
    this.$refs.search.focus()
  }
}
</script>
