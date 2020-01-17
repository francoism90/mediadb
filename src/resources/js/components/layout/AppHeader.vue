<template lang="pug">
b-navbar(fixed-top)
  template(slot="brand")
    b-navbar-item(tag="router-link" :to="{ name: 'home' }" exact)
      span(class="has-text-grey has-text-weight-semibold is-uppercase") MediaDB

    b-navbar-item(class="divider is-hidden-touch")

  template(slot="start")
    b-navbar-item(tag="router-link" :to="{ name: 'home' }" exact class="is-uppercase") Browse

    b-navbar-dropdown(label="More" hoverable arrowless class="is-uppercase")
      b-navbar-item Collections
      b-navbar-item Users

  template(slot="end")
    b-navbar-dropdown(:label="$auth.user().name" hoverable arrowless class="is-uppercase")
      b-navbar-item My Collections
      b-navbar-item History

    b-navbar-item(class="divider is-hidden-touch")
    b-navbar-item(class="has-text-primary is-uppercase") Submit
</template>
