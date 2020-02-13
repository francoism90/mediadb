<template lang="pug">
section(class="auth")
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
export default {
  data () {
    return {
      body: {
        username: null,
        password: null
      },
      rememberMe: true,
      fetchUser: true
    }
  },

  methods: {
    login () {
      const redirect = this.$auth.redirect()

      this.$auth.login({
        data: this.body,
        rememberMe: this.rememberMe,
        redirect: { name: redirect ? redirect.from.name : 'home' },
        fetchUser: this.fetchUser,
        error: function () {
          this.$buefy.notification.open({
            duration: 5000,
            message: 'Incorrect e-mail address/password given.',
            type: 'is-danger',
            position: 'is-top',
            queue: false
          })
        }
      })
    }
  }
}
</script>
