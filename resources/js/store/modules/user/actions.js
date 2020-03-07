import Vue from 'vue'

const fetch = async ({ commit }) => {
  try {
    const response = await Vue.axios.get('auth/user')

    commit('setUser', response.data)
  } catch (e) {
    commit('setUser', { data: {} })
  }

  commit('setReady', true)
}

const login = async ({ state }, payload = {}) => {
  // Initialize CSRF protection
  await Vue.axios.get('airlock/csrf-cookie')

  // Try to login
  await Vue.axios.post('auth/login', payload)
}

const logout = async ({ commit }) => {
  // Try to logout
  await Vue.axios.post('auth/logout')

  // Reset state
  commit('resetState')
}

export default {
  fetch,
  login,
  logout
}
