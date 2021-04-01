<template>
  <v-app>
    <v-navigation-drawer
      absolute
      temporary
      v-model="drawer"
    >
      <template v-slot:prepend>
        <v-list-item two-line v-if="user.loggedIn">


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

      <v-btn
      color="white"
      icon
      @click="loadFeedbackForm"
        >
        <v-icon>mdi-message-reply-text</v-icon>
         
      </v-btn>
    </v-app-bar>
<v-dialog  v-model="openFeedback">
  <v-card tile>
      <v-toolbar
      color="secondary"
      flat
      fixed
      dark
      >
        <v-btn
          color="white"
          text
          @click="openFeedback = !openFeedback"
        >
          <v-icon>mdi-close</v-icon>
        </v-btn>
        <v-spacer></v-spacer>
        Feedback
        <v-spacer></v-spacer>
        <v-btn
          color="white"
          text
          @click="openFeedback = !openFeedback"
        >
          submit
        </v-btn>
      </v-toolbar>
      <v-card-text>
         <v-container fluid>
          <v-row>
            <v-col
              cols="12"
              md="6"
            >
              <v-textarea
                label="Your message..."
                placeholder="Type your feedback here..."
                hint="Your name and organization will be automatically included."
              ></v-textarea>
            </v-col>
          </v-row>
         </v-container>
      </v-card-text>
  </v-card>
</v-dialog  >
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
     openFeedback:false,
     drawer: null,
     items: [
          { title: "Scan", icon: 'mdi-barcode', to:"/", class:""},
          { title: 'Inventory', icon: 'mdi-view-dashboard', to:"/inventory", class:"d-none d-md-flex d-md-none" },
          { title: 'About WellSCAN', icon: 'mdi-information', to:"/about", class:"" },
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
      },

    loadFeedbackForm() {
      console.log("wtf");
      this.openFeedback = !this.openFeedback;
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
