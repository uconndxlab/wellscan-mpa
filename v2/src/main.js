import Vue from 'vue'
import App from './App.vue'
import './registerServiceWorker'
import firebase from "firebase/app"
import router from './router'
import store from './store'
import vuetify from './plugins/vuetify';

// Your web app's Firebase configuration
var firebaseConfig = {
  apiKey: "AIzaSyCeZsQqloriPNrPUaVsEYtvAGmgdYPiA1Q",
  authDomain: "wellscan.firebaseapp.com",
  databaseURL: "https://wellscan.firebaseio.com",
  projectId: "wellscan",
  storageBucket: "wellscan.appspot.com",
  messagingSenderId: "477728865423",
  appId: "1:477728865423:web:40f0f068ad41181c7c44fa"
};
// Initialize Firebase
firebase.initializeApp(firebaseConfig);



Vue.config.productionTip = false
Vue.use(require('vue-moment'));

new Vue({
  router,
  store,
  vuetify,
  firebase,
  render: h => h(App)
}).$mount('#app')
