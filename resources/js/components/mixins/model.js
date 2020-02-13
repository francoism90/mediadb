export const contextHandler = {
  methods: {
    openContextMenu (item, type = null) {
      let contextMenu = false

      switch (type) {
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
      let route = false

      switch (type) {
        // case 'tags':
        case 'media':
          route = {
            name: 'user-video',
            params: {
              id: item.id,
              slug: item.slug,
              user: item.relationships.user.id
            }
          }
          break
      }

      if (route) {
        this.$router.push(route)
        this.$store.dispatch('modal/close')
      }

      return route
    }
  }
}
