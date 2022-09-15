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

<v-btn color="primary" @click="showAddOrg = true">Add Organization</v-btn>

 <v-list two-line>
      

        <v-list-item
          v-for="org in orgs"
          :key="org.id"
        >

          <v-list-item-content @click="loadOrgInfo(org)">
            <v-list-item-title v-html="org.id"></v-list-item-title>
            <v-list-item-subtitle>{{org.users.length}} users</v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
  
    </v-list>

<v-dialog
      v-model="showSingleOrg"
      max-width="600px"
    >
      <v-card v-if="showSingleOrg">
        <v-card-title>
          <span class="text-h6">Manage Organization</span>
        </v-card-title>
        <v-card-subtitle>
          {{activeOrg.name}}
        </v-card-subtitle>
        <v-card-text>
          <v-container>
            <v-row>
              

              <v-col
                cols="12"
              >

  
              <v-list two-line>
                
                  <v-list-item 
                  ripple
                  
                  v-for="(user, i) in activeOrg.users"
                    :key="i">

                    <v-list-item-content style="border-bottom:1px solid #c3c3c3;">
                      <v-list-item-title>{{user.name}}</v-list-item-title>
                      <v-radio-group v-model="user.type" row>
                          <v-radio
                              label="User"
                              value="user"
                              @click="updateUserType(user.name, 'user')"
                          ></v-radio>

                          <v-radio
                              label="Admin"
                              value="admin"
                              @click="updateUserType(user.name, 'admin')"
                          ></v-radio>

                      </v-radio-group>
                    </v-list-item-content>

                    <v-list-item-action>
                      <v-btn title="Send Password Reset Email" @click="resetPassword(user.name)">
                        <v-icon color="grey lighten-1">mdi-lock-reset</v-icon>
                      </v-btn>
                    </v-list-item-action>
                  
                  </v-list-item>
                  
           
              </v-list>
  
              </v-col>

            </v-row>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="blue darken-1"
            text
            @click="showSingleOrg = false"
          >
            Close
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

<v-dialog
      v-model="showAddOrg"
      width="500"
    >


      <v-card>
        <v-card-title class="text-h5 grey lighten-2">
          Add Organization
        </v-card-title>

        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12">
                <v-text-field
                  v-model="newOrgName"
                  label="Organization Name"
                  required
                ></v-text-field>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>

        <v-divider></v-divider>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="primary"
            text
            @click="showAddOrg = false; addOrg()"
          >
            Save
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>




<!-- <v-expansion-panels>
    <v-expansion-panel
      v-for="org in orgs"
      :key="org.id"
    >
      <v-expansion-panel-header>
        {{org.id}} ({{org.users.length}} users)
      </v-expansion-panel-header>
      <v-expansion-panel-content>

<v-row
    
    justify="space-between"
  >
    <v-btn color="primary">
      Add User
    </v-btn>
    <v-btn
      
      text
      color="error"
    >
      Delete Organization
    </v-btn>

  </v-row>


          <v-simple-table>
            <template v-slot:default>
            <thead>
                <tr>
                <th class="text-left">
                    Name
                </th>
                <th class="text-left">
                    User Type
                </th>

                <th class="text-left">
                    Actions
                </th>
                </tr>
            </thead>
            <tbody>
                <tr
                v-for="user in org.users" :key="user.name">
                
                <td>{{ user.name }}</td>
                <td>
                     <v-radio-group v-model="user.type" row>
                        <v-radio
                            label="User"
                            value="user"
                        ></v-radio>

                        <v-radio
                            label="Admin"
                            value="admin"
                        ></v-radio>

                     </v-radio-group>
                        
                        
                </td>

                <td>
                    <v-btn>Reset User</v-btn>
                </td>
                </tr>
            </tbody>
            </template>
        </v-simple-table>

      </v-expansion-panel-content>
    </v-expansion-panel>
  </v-expansion-panels> -->


 


 </div>
</template>

<script>
// @ is an alias to /src

import firebase from "firebase/app";
export default {
  name: 'OrganizationDashboard',
  data () {
      return {
          orgs:[],
          activeOrg:{},
          showSingleOrg:false,
          showAddOrg:false,
          newOrgName:'',
          
      }
    },

    methods: {
       getOrgsFromFirestore() {
          var db = firebase.firestore();
          var that = this;
          var listRef = db.collection("organizations");


          listRef.onSnapshot(querySnapshot => {    
            this.orgs = [];
            querySnapshot.forEach(function(doc) {
              console.log(doc.data());
                    var orgUsers = [];
                    var userRef = db.collection("users").where("organization" , "==", doc.id);
                        userRef.onSnapshot(querySnapshot => {
                            orgUsers = [];
                            querySnapshot.forEach(function(doc){
                                orgUsers.push({
                                    id: doc.id,
                                    name:doc.data().name,
                                    type:doc.data().usr_type
                                });
                            })

                            if(doc.data().name !== "uconn") {
                              that.orgs.push({
                                  id:doc.id,
                                  name:doc.data().name || " ",
                                  users:orgUsers
                              })
                            }

                        })


                });
                this.orgs.sort();
            })
        },

        resetPassword(email) {
          firebase.auth().sendPasswordResetEmail(email)
            .then(() => {
              // Password reset email sent!
              // ..
            })
            .catch((error) => {
              console.log(error);
              // ..
            });
        },

        updateUserType(user, type) {
          var db = firebase.firestore();
          var userRef = db.collection("users").doc(user);

          // Set the "usr_type" field
          return userRef.update({
              usr_type: type
          })
          .then(() => {
              console.log("Document successfully updated!");
          })
          .catch((error) => {
              // The document probably doesn't exist.
              console.error("Error updating document: ", error);
          });
        },

        loadOrgInfo(org){
            this.showSingleOrg = true;
            this.activeOrg = org;
        },

        addOrg(){
            var db = firebase.firestore();
            var orgRef = db.collection("organizations").doc(this.newOrgName);
            orgRef.set({
                name:this.newOrgName
            })
            .then(function() {
                console.log("Document successfully written!");
            })
            .catch(function(error) {
                console.error("Error writing document: ", error);
            });
        }


    },

    mounted(){
        this.getOrgsFromFirestore();
    }
}
</script>

<style scoped>
    /* tr:first-child td {
        background-color:rgb(250, 242, 138)!important;
    } */

.v-list-item{
  background-color:#fff;
  margin-bottom:4px;
}

.v-list {
  background:transparent!important;
}
</style>