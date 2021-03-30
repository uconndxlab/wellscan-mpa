<template>
  <v-app>
    <v-navigation-drawer
      absolute
      temporary
      v-model="drawer"
    >
      <template v-slot:prepend>
        <v-list-item two-line>
          <v-list-item-avatar>
            <img src="https://randomuser.me/api/portraits/men/81.jpg">
          </v-list-item-avatar>

          <v-list-item-content>
            <v-list-item-title>{{user.email}}</v-list-item-title>
            <v-list-item-subtitle>{{user.organization}}</v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </template>

      <v-divider></v-divider>

      <v-list>
        <v-list-item
          v-for="item in items"
          :key="item.title"
          :to="item.to"
          :class="item.class"
        >
          <v-list-item-icon>
            <v-icon>{{ item.icon }}</v-icon>
          </v-list-item-icon>

          <v-list-item-content>
            <v-list-item-title>{{ item.title }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
      
         <div v-if="user.loggedIn" class="pa-2">
          <v-btn @click="signOut" block>
            Log Out
          </v-btn>
        </div>

    </v-navigation-drawer>
    <v-app-bar
      app
      color="secondary"
      dark
    >
      <div class="d-flex align-center">
        <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>
        <v-toolbar-title>{{$route.name}}</v-toolbar-title>
      </div>

      <v-spacer></v-spacer>

  
    </v-app-bar>

  <!-- Sizes your content based upon application components -->
  <v-content>
    
    <!-- Provides the application the proper gutter -->
    <v-container >

      <!-- If using vue-router -->
      <router-view></router-view>

    </v-container>
  </v-content>
  </v-app>
</template>

<script>
 import firebase from "firebase";

export default {
  name: 'App',

  components: {
    
  },

  data: () => ({
     user: {
          displayName: "",
          email: "",
          organization:"organization",
          loggedIn:false,
          usr_type:""
     },
     drawer: null,
     items: [
          { title: "Home", icon: 'mdi-home', to:"/", class:""},
          { title: 'Inventory', icon: 'mdi-view-dashboard', to:"/inventory", class:"d-none d-md-flex d-md-none" },
        ],
  }),

  methods: {
    signOut() {
        firebase
        .auth()
        .signOut()
        .then(() => {
          this.$router.replace({
            name: "Login"
          });
        });
      }
  },

  mounted() {
    // Initialize Firebase
    var that = this;
    firebase.auth().onAuthStateChanged(user => {
      
      if (!user) {
        this.$router.replace({name:"Login"});
      } else {
        
       var db = firebase.firestore();
   
       let org,usr_type;
       
       db.collection("users")
       .doc(user.email).get().then(function(doc){
          var usr = doc.data();
         
          org = usr.organization;
          usr_type = usr.usr_type;

          that.user = {
            displayName: user.displayName,
            email: user.email,
            organization:org,
            loggedIn:true,
            usr_type:usr_type
          };
        });


      }
    });
  }
};
</script>
