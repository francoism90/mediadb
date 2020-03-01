export const contextHandler = {
  methods: {
    openContextMenu (item, type = null) {
      let contextMenu = false

      switch (type) {
        // case 'collect':
        // case 'tags':
        case 'media':
          contextMenu = {
            component: 'Media',
            class: 'manager',
            escape: ['escape'],
            fullscreen: true,
            props: { id: item.id }
          }
          break
      }

      if (contextMenu) {
        this.$store.dispatch('modal/open', contextMenu)
      }

      return contextMenu
    },

    openContextMenuSwipe (item, type = null) {
      const fn = (direction, event) => {
        this.openContextMenu(item, type)
      }

      return fn
    }
  }
}

export const routeHandler = {
  methods: {
    pushRoute (item, type = null) {
      switch (type) {
        case 'tag':
          this.$store.dispatch(this.paginate + '/reset', {
            params: {
              'filter[query]': '#' + item.slug
            }
          })
          break
        case 'media':
          this.$router.push({
            name: 'user-video',
            params: {
              id: item.id,
              slug: item.slug,
              user: item.relationships.user.id
            }
          })
          break
        case 'collect':
          this.$router.push({
            name: 'user-collect',
            params: {
              id: item.id,
              slug: item.slug,
              user: item.relationships.user.id
            }
          })
          break
      }

      this.$store.dispatch('modal/close')
    }
  }
}
