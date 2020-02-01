export default {
  videoOptions: (state) => {
    return {
      autoplay: true,
      poster: state.data.thumbnail,
      height: state.data.properties.height || 720,
      width: state.data.properties.width || 1280,
      manifest: state.data.stream_url,
      download: state.data.download || false
    }
  }
}
