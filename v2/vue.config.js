module.exports = {
  transpileDependencies: [
    'vuetify'
  ],

  pwa: {
    name: 'WellSCAN App',
    themeColor: '#d56a3a',
    workboxOptions: {
      skipWaiting: true
    }
  },

  chainWebpack: config => {
    config
    .plugin('html')
    .tap(args => {
      args[0].title = 'WellSCAN App'
      return args
    })
  }
}
