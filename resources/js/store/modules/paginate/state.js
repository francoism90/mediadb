export default function () {
  return {
    ready: false,
    loading: false,
    id: null,
    path: null,
    params: {
      'page[number]': 1,
      'page[size]': 9
    },
    data: [],
    meta: {},
    selected: []
  }
}
