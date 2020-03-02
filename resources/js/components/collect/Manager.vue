<template lang="pug">
modal(v-if="data.id" :key="data.id")
  h1(class="title is-4") {{ data.name }}
  h2(class="subtitle")
    | <router-link v-if="data.relationships.user" :to="{ name: 'user-view', params: { user: data.relationships.user.id } }">{{ data.relationships.user.name }}</router-link> •
    | {{ Number(data.media) | approximate }} items •
    | {{ Number(data.views) | approximate }} views

  b-collapse(class="card" v-for="(item, index) of items" :key="index" :open="isOpen === index" @open="isOpen = index")
    div(slot="trigger" slot-scope="props" class="card-header" role="button")
      div(class="card-header-title is-inline-block")
        h1(class="title is-5") {{ item.title }}
        h2(class="subtitle") {{ item.description }}
      a(class="card-header-icon")
        b-icon(:icon="props.open ? 'chevron-down' : 'chevron-up'")

    div(class="card-content")
      div(class="content" :is="item.component" v-bind="{ item: data }")
</template>

<script>
import modelModule from '@/store/modules/model'
import { mapGetters } from 'vuex'

export default {
  components: {
    Modal: () => import(/* webpackChunkName: "modal" */ '@/components/ui/Modal'),
    Details: () => import(/* webpackChunkName: "collect-details" */ '@/components/collect/Details'),
    Items: () => import(/* webpackChunkName: "collect-items" */ '@/components/collect/Items'),
    Advanced: () => import(/* webpackChunkName: "collect-advanced" */ '@/components/collect/Advanced')
  },

  props: {
    id: {
      type: String,
      required: true
    }
  },

  data () {
    return {
      isOpen: 0,
      items: [
        {
          title: 'Details',
          description: 'Update the name, tags and description.',
          component: 'Details'
        },
        {
          title: 'Items',
          description: 'Add and remove collection items.',
          component: 'Items'
        },
        {
          title: 'Advanced',
          description: 'Visibility, export and remove.',
          component: 'Advanced'
        }
      ]
    }
  },

  computed: {
    ...mapGetters('collect_manager', {
      data: 'getData',
      meta: 'getMeta'
    })
  },

  created () {
    if (!this.$store.state.collect_manager) {
      this.$store.registerModule('collect_manager', modelModule)
    }

    this.fetch()
  },

  methods: {
    async fetch () {
      await this.$store.dispatch('collect_manager/fetch', {
        path: 'collect',
        params: {
          include: 'media,tags,user',
          'filter[id]': this.id
        }
      })
    }
  }
}
</script>
