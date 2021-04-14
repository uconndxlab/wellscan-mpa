<template>
  <v-app>
    <v-navigation-drawer
    app
      fixed
      
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
          <v-btn @click="signOut" block depressed tile color="accent">
            Log Out
          </v-btn>
        </div>

    </v-navigation-drawer>
    <v-app-bar
      app
      
      color="secondary"
      
    >
      <div class="d-flex align-center">
        <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>
        <v-toolbar-title>{{$route.name}}</v-toolbar-title>
      </div>

      <v-spacer></v-spacer>

      <v-btn
      color="primary"
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
      
      fixed
      
      >
        <v-btn
          color="primary"
          
          text
          @click="openFeedback = !openFeedback"
        >
          <v-icon>mdi-close</v-icon>
        </v-btn>
        <v-spacer></v-spacer>
        <v-toolbar-title>Feedback</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn
          color="primary"
          text
          @click="submitFeedback"
        >
          submit
        </v-btn>
      </v-toolbar>
      <v-card-text>
         <v-container fluid>
          <v-row>
            <v-col
              cols="12"
              
            >
              <v-textarea
                label="Your message..."
                placeholder="Type your feedback here..."
                v-model="bugText"
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
     bugText:"",
     drawer: null,
     items: [
          { title: "Scan", icon: 'mdi-barcode', to:"/", class:""},
          { title: 'Inventory', icon: 'mdi-view-list', to:"/inventory", class:"d-none d-md-flex d-md-none" },
          { title: 'Saved Snapshots', icon: 'mdi-view-dashboard', to:"/savedSnaps", class:"d-none d-md-flex d-md-none" },
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
    },

    submitFeedback() {
        var db = firebase.firestore();
        var that = this;
        
        db.collection("bugReports")
            .add({
                date_reported: new Date(),
                current_page:that.$route.name,
                org:that.user.organization,
                usr:that.user.email,
                description:that.bugText
            }
        )
        .then(function(docRef) {
            that.bugText = "";
            that.openFeedback = !that.openFeedback;
            console.log("Your bug has been saved with ID " + docRef.id);
        })
        .catch(function(error) {
            console.error("Error adding document: ", error);
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

<style>
  /** Totally wasteful prefetching /shrug */
  html {
    background-image:url('~@/assets/bg1.jpg');
    background-size:0;
  }
  .v-application--wrap {
    background:url('~@/assets/bg2.jpg') fixed;
    background-size:cover;
    background-color:rgba(255,255,255,0.35);
    background-blend-mode:overlay;
  }


</style>