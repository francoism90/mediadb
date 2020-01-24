const RouterView = {
  template: '<router-view></router-view>'
}

const routes = [
  {
    path: '/',
    component: RouterView,
    meta: {
      auth: true
    },
    children: [
      {
        path: 'login',
        name: 'auth',
        component: () => import(/* webpackChunkName: "auth" */ './views/Auth.vue'),
        meta: {
          auth: false
        }
      },
      {
        path: '',
        name: 'home',
        component: () => import(/* webpackChunkName: "user-feed" */ './views/user/Feed.vue')
      },
      {
        path: ':user',
        component: () => import(/* webpackChunkName: "user-index" */ './views/user/Index.vue'),
        children: [
          {
            path: '',
            name: 'user-view',
            component: () => import(/* webpackChunkName: "user-profile" */ './views/user/Profile.vue')
          },
          {
            path: 'video/:id/:slug?',
            name: 'user-video',
            component: () => import(/* webpackChunkName: "user-video" */ './views/user/Video.vue')
          }
        ]
      },
      {
        path: '/collections',
        name: 'collections',
        component: () => import(/* webpackChunkName: "user-feed" */ './views/user/Feed.vue')
      },
      {
        path: '/subscriptions',
        name: 'subscriptions',
        component: () => import(/* webpackChunkName: "user-feed" */ './views/user/Feed.vue')
      },
      {
        path: '/upload',
        name: 'upload',
        component: () => import(/* webpackChunkName: "upload" */ './views/Upload.vue')
      }
    ]
  },
  {
    path: '*',
    redirect: '/'
  }
]

export default routes
