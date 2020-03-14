<template lang="pug">
a(class="box" @click.prevent="logout")
  article(class="media")
    div(class="media-left")
      b-icon(icon="logout-variant")
    div(class="media-content")
      h1(class="title") Log Out
      p(class="subtitle") A white box to contain other elements
</template>

<script>
export default {
  methods: {
    async logout () {
      try {
        await this.$store.dispatch('user/logout')

        this.$router.push({ name: 'login' })
      } catch (e) {
        alert(e || 'Unable to logout. Please try again later.')
      }
    }
  }
}
</script>
