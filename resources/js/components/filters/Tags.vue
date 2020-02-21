<template lang="pug">
modal
  nav(class="level is-mobile")
    div(class="level-left")
      div(class="level-item")
        b-dropdown(v-model="typeFilter" aria-role="list")
          b-button(
            slot="trigger"
            type="is-text"
            size="is-normal"
            class="sorters"
            icon-right="chevron-down"
          ) {{ typeFilterLabel }}

          b-dropdown-item(
            v-for="type in types"
            :key="type.key"
            :value="type.key"
            aria-role="listitem"
          ) {{ type.label }}

  div(class="card tags" v-for="(tags, key, index) of items" :key="index")
    div(class="card-content")
      div(class="content is-flex")
        div(class="letter")
          h1(class="title is-6 is-uppercase") {{ key }}

        div(class="items")
          a(v-for="tag in tags" :key="tag.id" @click.prevent="setQuery(tag.slug)") {{ tag.name }}<br>
</template>

<script>
import tagsModule from '@/store/modules/tags'
import { mapGetters } from 'vuex'

export default {
  components: {
    Modal: () => import(/* webpackChunkName: "modal" */ '@/components/ui/Modal')
  },

  props: {
    namespace: {
      type: String,
      required: true
    },

    types: {
      type: Array,
      default: () => {
        return [
          { key: 'category', label: 'Categories' },
          { key: 'language', label: 'Languages' },
          { key: 'people', label: 'People' }
        ]
      }
    }
  },

  data () {
    return {
      items: {},
      apiRoute: {
        path: 'tags',
        params: {
          'filter[type]': this.types[0].key
        }
      }
    }
  },

  computed: {
    ...mapGetters('tags', [
      'getData'
    ]),

    typeFilter: {
      get () {
        return this.apiRoute.params['filter[type]']
      },

      set (value) {
        this.apiRoute.params['filter[type]'] = value
        this.setCollections()
      }
    },

    typeFilterLabel () {
      return this.types.find(x => x.key === this.typeFilter).label
    }
  },

  created () {
    if (!this.$store.state.tags) {
      this.$store.registerModule('tags', tagsModule)
    }

    this.setCollections()
  },

  methods: {
    async setCollections () {
      // Fetch tags
      await this.$store.dispatch('tags/fetch', this.apiRoute)

      // Create collections
      const collect = {}

      for (const item of this.getData) {
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

    setQuery (value) {
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
