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
            <v-list-item-title>joel.salisbury@gmail.com</v-list-item-title>
            <v-list-item-subtitle>uconn</v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </template>

      <v-divider></v-divider>

      <v-list>
        <v-list-item
          v-for="item in items"
          :key="item.title"
          :to="item.to"
        >
          <v-list-item-icon>
            <v-icon>{{ item.icon }}</v-icon>
          </v-list-item-icon>

          <v-list-item-content>
            <v-list-item-title>{{ item.title }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
      
         <div class="pa-2">
          <v-btn block>
            Logout
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


export default {
  name: 'App',

  components: {
    
  },

  data: () => ({
     drawer: null,
     items: [
          { title: "Home", icon: 'mdi-home', to:"/"},
          { title: 'Inventory', icon: 'mdi-view-dashboard', to:"/inventory" },
          { title: 'My Profile', icon: 'mdi-account', to:"/profile" },
        ],
  }),
};
</script>
