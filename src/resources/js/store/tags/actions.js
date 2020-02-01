import Vue from 'vue'

export default {
  async fetch ({ state }, params = {}) {
    const response = await Vue.axios.get('tag', {
      params: {
        include: params.include || null,
        'filter[feed]': params.feed || null,
        'filter[query]': params.query || null,
        sort: params.sort || null,
        'page[number]': params.page || null,
        'page[size]': params.size || 0
      }
    })

    return response.data
  }
}
