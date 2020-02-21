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
        component: () => import(/* webpackChunkName: "home" */ './views/Home.vue')
      },
      {
        path: '/history',
        name: 'history',
        component: () => import(/* webpackChunkName: "upload" */ './views/History.vue')
      },
      {
        path: '/upload',
        name: 'upload',
        component: () => import(/* webpackChunkName: "upload" */ './views/Upload.vue')
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
      }
    ]
  },
  {
    path: '*',
    redirect: '/'
  }
]

export default routes
