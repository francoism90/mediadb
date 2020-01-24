<template lang="pug">
b-navbar(fixed-top wrapper-class="container")
  template(slot="brand")
    b-navbar-item(class="has-text-grey-light has-text-weight-medium" tag="router-link" :to="{ name: 'home' }" exact) MediaDB
    b-navbar-item(class="divider is-hidden-desktop")
    b-navbar-item(class="is-hidden-desktop")
      b-icon(@click="openSearch()" icon="magnify")

  template(slot="burger")
    b-navbar-item(class="navbar-touch is-hidden-desktop")
      b-dropdown(aria-role="list")
        b-icon(slot="trigger" icon="menu")

        b-dropdown-item(
          v-for="item in items"
          :key="item.label"
          aria-role="listitem"
          has-link
        )
          router-link(:to="{ name: item.route }" exact) {{ item.label }}

  template(slot="end")
    b-navbar-item(
      v-for="item in items"
      :key="item.label"
      tag="router-link"
      :to="{ name: item.route }"
      exact
    ) {{ item.label }}

    b-navbar-item(class="divider is-hidden-touch")
    b-navbar-item(@click="openSearch()" class="navbar-search is-hidden-touch")
      b-icon(icon="magnify")
</template>

<script>
export default {
  data () {
    return {
      items: [
        {
          label: 'Browse',
          route: 'home'
        },
        {
          label: 'Collections',
          route: 'collections'
        },
        {
          label: 'Subscriptions',
          route: 'subscriptions'
        },
        {
          label: 'Upload Content',
          route: 'upload'
        }
      ]
    }
  },

  methods: {
    openSearch () {
      this.$store.dispatch('modal', {
        active: true,
        component: 'ModalSearch',
        class: 'search',
        fullscreen: true
      })
    }
  }
}
</script>
