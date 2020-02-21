<template lang="pug">
b-navbar(fixed-top wrapper-class="container")
  template(slot="brand")
    b-navbar-item(class="has-text-grey-light has-text-weight-medium" tag="router-link" :to="{ name: 'home' }" exact) MediaDB

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
</template>

<script>
export default {
  data () {
    return {
      items: [
        {
          label: 'Favorites',
          route: 'upload'
        },
        {
          label: 'Watchlist',
          route: 'upload'
        },
        {
          label: 'Viewing Activity',
          route: 'history'
        },
        {
          label: 'Upload Video',
          route: 'upload'
        }
      ]
    }
  }
}
</script>
