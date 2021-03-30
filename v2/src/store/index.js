import Vue from 'vue'
import Vuex from 'vuex'
import firebase from 'firebase';

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    user: {

    }
  },
  getters:{
    user(state) {
      return state.user
    },
  },
  mutations: {
    SET_LOGGED_IN (state,value) {
      state.user.loggedIn = value;
    },

    SET_USER(state, data) {

      state.user = data;

    },
  },
  actions: {
    fetchUser({ commit }, user) {

      commit("SET_LOGGED_IN", user !== null);
      if (user) {

       
       var db = firebase.firestore();
   
       let org,usr_type;
       
       db.collection("users")
       .doc(user.email).get().then(function(doc){
          var usr = doc.data();
         
          org = usr.organization;
          usr_type = usr.usr_type;

          commit("SET_USER", {
            displayName: user.displayName,
            email: user.email,
            organization:org,
            loggedIn:true,
            usr_type:usr_type
          });


       });


      } else {
        commit("SET_USER", null);
      }
    }
  },
  modules: {
  }
})
