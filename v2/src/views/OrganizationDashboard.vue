<template>
 <div>
    <v-alert
      icon="mdi-devices"
      prominent
      text
      type="info"
      class="d-sm-none"
    >
    The Dashboard feature works better on a large screen.   
    </v-alert>


<v-expansion-panels>
    <v-expansion-panel
      v-for="org in orgs"
      :key="org.id"
    >
      <v-expansion-panel-header>
        {{org.id}} ({{org.users.length}} users)
      </v-expansion-panel-header>
      <v-expansion-panel-content>

          <ul>
            <li v-for="user in org.users" :key="user.name">{{user.name}} ({{user.type}})</li>
          </ul>

      </v-expansion-panel-content>
    </v-expansion-panel>
  </v-expansion-panels>


 


 </div>
</template>

<script>
// @ is an alias to /src

import firebase from "firebase/app";
export default {
  name: 'OrganizationDashboard',
  data () {
      return {
          orgs:[]
        
          
      }
    },

    methods: {
       getOrgsFromFirestore() {
          var db = firebase.firestore();
          var that = this;
          var listRef = db.collection("organizations").orderBy("name", "asc");


          listRef.onSnapshot(querySnapshot => {    

            querySnapshot.forEach(function(doc) {
                    var orgUsers = [];
                    var userRef = db.collection("users").where("organization" , "==", doc.id);
                        userRef.onSnapshot(querySnapshot => {
                            querySnapshot.forEach(function(doc){
                                orgUsers.push({
                                    name:doc.data().name,
                                    type:doc.data().usr_type
                                });
                            })
                        })

                    that.orgs.push({
                        id:doc.id,
                        name:doc.data().name || " ",
                        users:orgUsers
                    })
                });
                
            })
        }


    },

    mounted(){
        this.getOrgsFromFirestore();
    }
}
</script>

<style scoped>
    tr:first-child td {
        background-color:rgb(250, 242, 138)!important;
    }
</style>