<template lang="pug">
section(v-if="!isAuthenticated" class="auth")
  main
    h1(class="title is-3") Log In
    div(class="box")
      form(@submit.prevent='login()')
        b-field(label="Email address")
          b-input(
            required
            type="email"
            minlength="10"
            maxlength="255"
            name="email"
            placeholder="Your email address",
            :has-counter="false"
            v-model.trim='body.email'
          )

        b-field(label="Password")
          b-input(
            required
            type="password",
            minlength="6"
            maxlength="50"
            name="password"
            placeholder="Your password",
            :has-counter="false"
            v-model.trim='body.password'
          )

        b-field
          b-button(native-type="submit") Log In
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  data () {
    return {
      body: {
        email: null,
        password: null,
        remember: true
      }
    }
  },

  computed: {
    ...mapGetters('user', [
      'isAuthenticated'
    ])
  },

  methods: {
    async login () {
      try {
        await this.$store.dispatch('user/login', this.body)

        this.$router.push(this.$route.query.redirect || '/')
      } catch (e) {
        alert(e || 'Unable to login. Please try again later.')
      }
    }
  }
}
</script>
