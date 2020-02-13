<template lang="pug">
modal
  div(class="card tags" v-for="(tags, key, index) of items" :key="index")
    div(class="card-content")
      div(class="content is-flex")
        div(class="letter")
          h1(class="title is-6 is-uppercase") {{ key }}

        div(class="items")
          a(v-for="tag in tags" :key="tag.id" @click.prevent="setFilter(tag.slug)") {{ tag.name }}<br>
</template>

<script>
import paginateModule from '@/store/modules/paginate'
import { mapState } from 'vuex'

export default {
  components: {
    Modal: () => import(/* webpackChunkName: "modal" */ '@/components/ui/Modal')
  },

  props: {
    namespace: {
      type: String,
      required: true
    }
  },

  data () {
    return {
      items: {},
      apiRoute: {
        path: 'tags',
        params: {
          'page[size]': 0
        }
      }
    }
  },

  computed: {
    ...mapState('tags', [
      'data'
    ])
  },

  async created () {
    if (!this.$store.state.tags) {
      this.$store.registerModule('tags', paginateModule)
    }

    await this.$store.dispatch('tags/fetch', this.apiRoute)

    this.setCollections()
  },

  methods: {
    setCollections () {
      const collect = {}

      for (const item of this.data) {
        let key = item.name.toLowerCase().charAt(0)

        // Use '#' as key for non alpha (numbers, special, etc.)
        if (/^[a-z]*$/.test(key) === false) {
          key = '#'
        }

        // Add to items
        const items = collect[key] || []
        items.push(item)

        collect[key] = items
      }

      this.items = Object.assign({}, this.tags, collect)
    },

    setFilter (value) {
      this.$store.dispatch(this.namespace + '/reset', {
        params: {
          'filter[query]': '#' + value
        }
      })

      this.$store.dispatch('modal/close')
    }
  }
}
</script>
