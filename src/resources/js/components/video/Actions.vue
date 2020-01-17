<template lang="pug">
section
  hr

  div(class="level")
    div(class="level-left")
      div(class="level-item")
        article(class="media")
          figure(class="media-left is-hidden-touch")
            p(class="image is-64x64")
              img(:src="userData.thumbnail")

          div(class="media-content")
            p
              | <router-link :to="{ name: 'user-view', params: { user: userData.id }}">{{ userData.name }}</router-link><br>
              | <span class="is-inline-block">{{ Number(0) | approximate }} subscribers</span>

    div(class="level-right")
      div(class="level-item")
        b-field
          p(class="control")
            b-tooltip(label="Subscribe" type="is-black")
              b-button(icon-right="bell-outline")

          p(class="control")
            b-tooltip(label="Like Video" type="is-black")
              b-button(icon-left="thumb-up-outline")

          p(class="control")
            b-tooltip(label="Add to Collection" type="is-black")
              b-button(icon-right="plus")

      div(v-if="userData.id === $auth.user().id" class="level-item")
        b-field
          p(class="control")
            b-tooltip(label="Manage Video" type="is-black")
              b-button(@click="openManager()" icon-right="file-document-box")

          p(class="control")
            b-tooltip(label="Create Thumbnail" type="is-black")
              b-button(@click="callback('create-thumbnail')" icon-right="image")

  section(v-if="data.description")
    p {{ data.description }}

  hr
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
