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
        path: '/collections',
        name: 'collections',
        component: () => import(/* webpackChunkName: "collections" */ './views/Collections.vue')
      },
      {
        path: '/profiles',
        name: 'profiles',
        component: () => import(/* webpackChunkName: "profiles" */ './views/Profiles.vue')
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
            path: 'collection/:id/:slug?',
            name: 'user-collect',
            component: () => import(/* webpackChunkName: "user-collect" */ './views/user/Collections.vue'),
            props: true
          },
          {
            path: 'video/:id/:slug?',
            name: 'user-video',
            component: () => import(/* webpackChunkName: "user-video" */ './views/user/Video.vue'),
            props: true
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
