export default {
  paginate: (state) => (id) => {
    return state.paginate[id] || false
  },

  itemContextMenu: () => (item, type) => {
    const types = {
      media: {
        active: true,
        component: 'ModalMedia',
        class: 'media',
        fullscreen: true,
        props: { id: item.id }
      }
    }

    return types[type] || false
  },

  itemRoute: () => (item, type) => {
    const types = {
      media: {
        name: 'user-video',
        params: {
          id: item.id,
          slug: item.slug,
          user: item.relationships.user.id
        },
        meta: {
          hasModal: true
        }
      }
    }

    return types[type] || false
  }
}
