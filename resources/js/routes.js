
const RouterView = {
  template: '<router-view></router-view>'
}

const routes = [
  {
    path: '/',
    component: RouterView,
    children: [
      {
        path: 'login',
        name: 'login',
        component: () => import(/* webpackChunkName: "login" */ './views/auth/Login.vue')
      },
      {
        path: '',
        name: 'home',
        component: () => import(/* webpackChunkName: "home" */ './views/Home.vue'),
        meta: { auth: true }
      },
      {
        path: '/collections',
        name: 'collections',
        component: () => import(/* webpackChunkName: "collections" */ './views/Collections.vue'),
        meta: { auth: true }
      },
      {
        path: '/profiles',
        name: 'profiles',
        component: () => import(/* webpackChunkName: "profiles" */ './views/Profiles.vue'),
        meta: { auth: true }
      },
      {
        path: '/account',
        name: 'account',
        component: () => import(/* webpackChunkName: "account" */ './views/Account.vue'),
        meta: { auth: true }
      },
      {
        path: ':channel',
        component: () => import(/* webpackChunkName: "channel-index" */ './views/channel/Index.vue'),
        meta: { auth: true },
        children: [
          {
            path: '',
            name: 'channel-view',
            component: () => import(/* webpackChunkName: "channel-profile" */ './views/channel/Profile.vue')
          },
          {
            path: 'collection/:id/:slug?',
            name: 'channel-collect',
            component: () => import(/* webpackChunkName: "channel-collect" */ './views/channel/Collection.vue'),
            props: true
          },
          {
            path: 'video/:id/:slug?',
            name: 'channel-video',
            component: () => import(/* webpackChunkName: "channel-video" */ './views/channel/Video.vue'),
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
