<template lang="pug">
section(:key="module.id")
  filters(v-if="hasFilters" :key="identifier" :module-id="module.id" :tags-props="module.tagsProps")

  div(class="columns is-variable is-multiline")
    div(class="column" :class="customClass" v-for="(item, index) in items" :key="index" @click="route(item)" @contextmenu.prevent="contextHandler(item)" v-touch:swipe.right="swipeHandler(item)")
      card(:data="item")

  infinite-loading(:identifier="identifier" @infinite="infiniteHandler")
</template>

<script>
import { mapActions, mapGetters } from 'vuex'

export default {
  components: {
    Card: () => import(/* webpackChunkName: "card" */ '@/components/ui/Card'),
    Filters: () => import(/* webpackChunkName: "filters" */ '@/components/ui/Filters')
  },

  props: {
    module: {
      type: Object,
      required: true
    },

    customClass: {
      type: String,
      default: `
        is-full-mobile
        is-half-tablet
        is-one-third-desktop
        is-one-third-widescreen
        is-one-third-fullhd
      `
    },

    hasFilters: {
      type: Boolean,
      default: true
    }
  },

  data () {
    return {
      items: [],
      identifier: +new Date(),
      filterChange: [
        'resetPaginate'
      ]
    }
  },

  computed: {
    ...mapGetters([
      'itemContextMenu',
      'itemRoute'
    ]),

    paginateProps () {
      return this.$store.state.paginate[this.module.id]
    }
  },

  created () {
    this.items = this.paginateProps.data || []
  },

  mounted () {
    this.$store.subscribeAction((action) => {
      if (this.filterChange.includes(action.type)) {
        if (action.payload.id === this.module.id) {
          this.items = []
          this.identifier += 1
        }
      }
    })
  },

  methods: {
    ...mapActions([
      'paginate',
      'modal',
      'destroyModal'
    ]),

    async infiniteHandler ($state) {
      const { meta } = await this.paginate(this.module.id)

      if (meta && meta.current_page <= meta.last_page) {
        this.items = this.paginateProps.data

        return $state.loaded()
      }

      return $state.complete()
    },

    swipeHandler (item) {
      const fn = (direction, event) => {
        this.contextHandler(item)
      }

      return fn
    },

    route (item) {
      const itemProps = this.itemRoute(item, this.module.type)

      if (itemProps) {
        this.$router.push(itemProps)

        if (itemProps.meta && itemProps.meta.hasModal) {
          this.destroyModal()
        }
      }
    },

    contextHandler (item) {
      const itemProps = this.itemContextMenu(item, this.module.type)

      if (itemProps) {
        this.modal(itemProps)
      }
    }
  }
}
</script>
