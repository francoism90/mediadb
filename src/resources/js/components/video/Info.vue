<template lang="pug">
section(class="section media-info")
  div(class="container")
    nav(class="level is-mobile")
      div(class="level-item has-text-centered")
        a
          p(class="heading")
            b-icon(icon="thumb-up-outline")
          p(class="title is-7") Like

      div(class="level-item has-text-centered")
        a
          p(class="heading")
            b-icon(icon="plus")
          p(class="title is-7") Save

      div(class="level-item has-text-centered")
        a(@click="callback('download')")
          p(class="heading")
            b-icon(icon="download")
          p(class="title is-7") Download

      div(class="level-item has-text-centered is-hidden-mobile")
        a(@click="callback('create-thumbnail')")
          p(class="heading")
            b-icon(icon="image")
          p(class="title is-7") Snapshot

      div(class="level-item has-text-centered")
        a(@click="openManager()")
          p(class="heading")
            b-icon(icon="file-document-box")
          p(class="title is-7") Edit

    nav(class="level is-mobile is-marginless media-subscribe")
      div(class="level-left")
        div(class="level-item")
          article(class="media")
            figure(class="media-left")
              p(class="image is-64x64")
                img(:src="userData.thumbnail")

            div(class="media-content")
              p
                | <router-link :to="{ name: 'user-view', params: { user: userData.id }}">{{ userData.name }}</router-link><br>
                | <span class="is-inline-block">{{ Number(0) | approximate }} subscribers</span>

      div(class="level-right")
        div(class="level-item is-marginless is-hidden-tablet")
          b-button(icon-right="bell-outline")

        div(class="level-item is-hidden-mobile")
          b-button(icon-left="bell-outline") Subscribe

    section(class="media-description")
      p(v-if="data.description") {{ data.description }}
</template>

<script>
export default {
  props: {
    data: {
      type: Object,
      required: true
    },

    meta: {
      type: Object,
      default: null
    },

    userData: {
      type: Object,
      required: true
    },

    userMeta: {
      type: Object,
      default: null
    }
  },

  methods: {
    callback (event) {
      this.$eventHub.$emit(this.data.id, event)
    },

    openManager () {
      const modalComponent = () => import(
        /* webpackChunkName: "media-manager" */ '@/components/media/Manager'
      )

      this.$buefy.modal.open({
        component: modalComponent,
        parent: this,
        props: {
          data: this.data,
          userData: this.data
        },
        hasModalCard: true,
        trapFocus: true
      })
    }
  }
}
</script>
