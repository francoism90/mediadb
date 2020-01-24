export default {
  modal: {},
  paginate: {},
  paginateDefaults: {
    data: [],
    meta: {},
    props: {
      initialized: new Date(),
      page: 1
    }
  },
  sorters: [
    {
      key: 'recommended',
      label: 'Recommended for You'
    },
    {
      key: 'trending',
      label: 'Trending'
    },
    {
      key: 'recent',
      label: 'Most recent'
    },
    {
      key: 'views',
      label: 'Most viewed'
    },
    {
      key: 'popular-week',
      label: 'Popular this week'
    },
    {
      key: 'popular-month',
      label: 'Popular this month'
    }
  ]
}
