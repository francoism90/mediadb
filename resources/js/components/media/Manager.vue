<template lang="pug">
modal(v-if="ready" :key="data.id")
  h1(class="title is-4") {{ data.name }}
  h2(class="subtitle")
    | <router-link v-if="data.relationships.user" :to="{ name: 'user-view', params: { user: data.relationships.user.id } }">{{ data.relationships.user.name }}</router-link> •
    | {{ Number(data.properties.duration) | timestamp }} •
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
import { mapActions, mapState } from 'vuex'

export default {
  components: {
    Modal: () => import(/* webpackChunkName: "modal" */ '@/components/ui/Modal'),
    Details: () => import(/* webpackChunkName: "media-details" */ '@/components/media/Details'),
    Visibility: () => import(/* webpackChunkName: "media-details" */ '@/components/media/Visibility'),
    Advanced: () => import(/* webpackChunkName: "media-details" */ '@/components/media/Advanced')
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
          description: 'Update the name, tags, description and collections.',
          component: 'Details'
        },
        {
          title: 'Visibility',
          description: 'Choose the visibility level.',
          component: 'Visibility'
        },
        {
          title: 'Elements',
          description: 'Subtitles, recording date and location, license.',
          component: 'Visibility'
        },
        {
          title: 'Advanced',
          description: 'Housekeeping, export, remove, archive.',
          component: 'Advanced'
        }
      ]
    }
  },

  computed: {
    ...mapState('manager', [
      'ready',
      'data'
    ])
  },

  async created () {
    if (!this.$store.state.manager) {
      this.$store.registerModule('manager', modelModule)
    }

    await this.fetch({ path: 'media/' + this.id })
  },

  beforeDestroy () {
    this.$store.unregisterModule('manager')
  },

  methods: {
    ...mapActions('manager', [
      'fetch'
    ])
  }
}
</script>
