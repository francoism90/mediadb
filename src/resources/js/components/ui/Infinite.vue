<template lang="pug">
section
  div(class="columns is-variable is-multiline")
    div(class="column" :class="customClass" v-for="(item, index) in items" :key="index")
      card(:data="item" :paginate-id="id")

  infinite-loading(:identifier="identifier" @infinite="infiniteHandler")
</template>

<script>
import { mapActions, mapGetters, mapMutations } from 'vuex'

export default {
  components: {
    Card: () => import(/* webpackChunkName: "card" */ '@/components/ui/Card')
  },

  props: {
    id: {
      type: [Number, String],
      required: true
    },

    dispatcher: {
      type: String,
      required: true
    },

    params: {
      type: Object,
      default: null
    },

    customClass: {
      type: String,
      default: `
        is-full-mobile
        is-half-tablet
        is-one-third-desktop
        is-one-quarter-widescreen
        is-one-fifth-fullhd
      `
    }
  },

  data () {
    return {
      identifier: +new Date(),
      filterChange: [
        'resetPaginate'
      ]
    }
  },

  computed: {
    ...mapGetters({
      items: 'paginateData'
    })
  },

  created () {
    this.setPaginateId(this.id)
    this.setPaginateParams(this.params)
  },

  mounted () {
    this.$store.subscribe((mutation, state) => {
      if (this.filterChange.includes(mutation.type)) {
        if (state.paginateId === this.id) {
          this.identifier += 1
        }
      }
    })
  },

  methods: {
    ...mapActions([
      'paginate'
    ]),

    ...mapMutations([
      'setPaginateId',
      'setPaginateParams'
    ]),

    async infiniteHandler ($state) {
      const { meta } = await this.paginate(this.dispatcher)

      if (meta.current_page < meta.last_page) {
        return $state.loaded()
      }

      return $state.complete()
    }
  }
}
</script>
