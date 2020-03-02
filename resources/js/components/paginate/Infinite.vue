<template lang="pug">
section(v-if="state.ready" :key="namespace" class="items")
  div(class="columns is-mobile is-variable is-multiline")
    div(
      class="column"
      :class="columnClass"
      v-for="(item, index) in state.data"
      :key="index"
      :is="component"
      v-bind="{ data: item, namespace: namespace, paginate: paginate }"
    )

  infinite-loading(:identifier="identifier" @infinite="infiniteHandler")
    span(slot="no-more")
    span(slot="no-results")
</template>

<script>
export default {
  components: {
    Collection: () => import(/* webpackChunkName: "types-collect" */ '@/components/paginate/types/Collection'),
    Media: () => import(/* webpackChunkName: "types-media" */ '@/components/paginate/types/Media'),
    Profile: () => import(/* webpackChunkName: "types-profile" */ '@/components/paginate/types/Profile'),
    Tagger: () => import(/* webpackChunkName: "types-tagger" */ '@/components/paginate/types/Tagger')
  },

  props: {
    namespace: {
      type: String,
      required: true
    },

    apiRoute: {
      type: Object,
      required: true
    },

    paginate: {
      type: String,
      default: null
    },

    component: {
      type: String,
      required: true
    },

    columnClass: {
      type: String,
      default: `
        is-full-mobile
        is-half-tablet
        is-one-third-desktop
        is-one-third-widescreen
        is-one-third-fullhd
      `
    }
  },

  data () {
    return {
      identifier: +new Date()
    }
  },

  computed: {
    state () {
      return this.$store.state[this.namespace]
    }
  },

  created () {
    this.$store.dispatch(this.namespace + '/create', this.apiRoute)
  },

  mounted () {
    this.$store.subscribeAction((action) => {
      if (action.type === this.namespace + '/reset') {
        this.identifier += 1
      }
    })
  },

  methods: {
    reset () {
      return this.$store.dispatch(this.namespace + '/reset')
    },

    async infiniteHandler ($state) {
      await this.$store.dispatch(this.namespace + '/fetch')

      if (
        this.state.meta &&
        this.state.meta.current_page < this.state.meta.last_page
      ) {
        return $state.loaded()
      }

      return $state.complete()
    }
  }
}
</script>
