<template lang="pug">
section(v-if="state.ready" :key="namespace" class="items")
  div(class="columns is-variable is-multiline")
    div(
      class="column"
      :class="columnClass"
      v-for="(item, index) in state.data"
      :key="index"
      @click.prevent="pushRoute(item, type)"
      @contextmenu.prevent="openContextMenu(item, type)"
      v-touch:swipe.right="openContextMenuSwipe(item, type)"
    )
      card(:data="item")

  infinite-loading(:identifier="identifier" @infinite="infiniteHandler")
    span(slot="no-more")
    span(slot="no-results")
</template>

<script>
import { contextHandler, routeHandler } from '@/components/mixins/model'

export default {
  components: {
    Card: () => import(/* webpackChunkName: "card" */ '@/components/ui/Card')
  },

  mixins: [contextHandler, routeHandler],

  props: {
    namespace: {
      type: String,
      required: true
    },

    apiRoute: {
      type: Object,
      required: true
    },

    type: {
      type: String,
      default: null
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
      items: [],
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
