<template>
 <div>
    <v-alert
      icon="mdi-devices"
      prominent
      text
      type="info"
      class="d-sm-none"
    >
    The Inventory feature works better on a large screen.   
    </v-alert>
    <v-toolbar
        flat
        dark
        color="primary"
    >
            <v-menu
        bottom
        left
        >
            <template v-slot:activator="{ on, attrs }">
                <v-btn
                dark
                icon
                v-bind="attrs"
                v-on="on"
                >
                <v-icon>mdi-dots-vertical</v-icon>
                </v-btn>
            </template>

            <v-list>
                <v-list-item>
                    <v-list-item-title @click="getInventoryForOrg();" style="cursor:pointer">Active</v-list-item-title>
                </v-list-item>

                <v-list-item>
                    <v-list-item-title @click="getArchivedItems()" style="cursor:pointer">Archived</v-list-item-title>
                </v-list-item>

                <v-list-item>
                    <v-list-item-title @click="getFlaggedItems();" style="cursor:pointer">Flagged</v-list-item-title>
                </v-list-item>

                <!-- <v-list-item>
                    <v-list-item-title style="cursor:pointer">Saved Snapshots</v-list-item-title>
                </v-list-item>                     -->
            </v-list>
        </v-menu>
        <v-toolbar-title tile>{{folder}} <small>{{user.organization}}</small></v-toolbar-title>
        
        <v-spacer></v-spacer>

        <v-btn
            v-if="this.folder == 'Active Foods'"
            dark
            color="secondary"
            @click="prepSnapshot()">
            <v-icon>mdi-export</v-icon>
            Save Snapshot
        </v-btn>    



    </v-toolbar>
 <v-data-table
    :headers="headers"
    :items="items"
    :items-per-page="15"
    class="elevation-1"
  >
    
    <template v-slot:[`item.rank`]="{ item }">
      <v-chip
        :color="item.rank"
        dark
      >
        {{ item.rank }}
      </v-chip>
    </template>

    <template v-slot:[`item.actions`]="{ item }">
      <v-btn v-if="item.status == 'active' && item.flagged == false" @click="archiveItem(item)" text small>
        <v-icon          
        >
          mdi-archive
        </v-icon>
      </v-btn>

      <v-btn @click="loadItem(item)" text color="success" small>
        <v-icon          
        >
          mdi-magnify
        </v-icon>
      </v-btn>

      <v-btn v-if="item.status == 'archived'" color="error" @click="deleteItem(item)" text small>
        
        <v-icon
          small
          
        >
          mdi-delete
        </v-icon>
      </v-btn>

    </template>
  </v-data-table>

<v-dialog
  v-model="showExport"
  fullscreen
  hide-overlay
  transition="dialog-bottom-transition"
>
  <v-card>
    <v-toolbar
      dark
      color="secondary"
    >
      <v-btn
        icon
        dark
        @click="showExport = false"
      >
        <v-icon>mdi-close</v-icon>
      </v-btn>
      <v-toolbar-title>Save Snapshot</v-toolbar-title>
      <v-spacer></v-spacer>
      <v-toolbar-items>
        <v-btn
          dark
          text
          @click="saveSnapshot"
        >
          Save
        </v-btn>
      </v-toolbar-items>
    </v-toolbar>
    <v-card-text>
      <div style="margin-top:20px">
        <v-text-field
          v-model="snapshot.name"
          dense
          label="Snapshot Name"
          outlined
        ></v-text-field>
        <v-card-subtitle>Custom Fields</v-card-subtitle>
        <v-row v-bind:key="index" v-for="(field,index) in snapshot.meta">
          <v-col cols="5">
            <v-text-field
              v-model="field.name"
              dense
              label="Field Name"
              outlined
            ></v-text-field>
          </v-col>

          <v-col cols="5">
            <v-text-field
              v-model="field.value"
              dense
              label="Field Value"
              outlined
            ></v-text-field>
          </v-col>

          <v-col cols="2">
            <v-btn
              @click="deleteSnapshotField(index)"
            >
              Delete
            </v-btn>
          </v-col>

        </v-row>
        <v-row>
          <v-btn
          @click="snapshot.meta.push({name:'New Field Name', value:'New Field value'})"
            block>
            Add a Field
            
          </v-btn>
        </v-row>
        
        </div>
    </v-card-text>
  </v-card>
</v-dialog>


 


 </div>
</template>

<script>
// @ is an alias to /src

import firebase from "firebase/app";
export default {
  name: 'Inventory',
  data () {
      return {
        showSingleItem:false,
        user: {
            displayName: "",
            email: "",
            organization:"organization",
            loggedIn:false,
            usr_type:"",
        },
        showExport: false,
        folder:"Active Foods",
        headers: [
          {
            text: 'Name',
            align: 'start',
            sortable: false,
            value: 'name',
          },
          { text: 'Rank', value: 'rank' },
          { text: 'Actions', value:'actions', sortable: false},
        ],
        items: [

        ],
        snapshot: 
        {
          name:"Name",
          meta:[
            {
              name:"Custom Field Name",
              value:"Custom Field Value"
            }
          ],
          items:[]
        }
      }
    },

    methods: {
        deleteSnapshotField(index) {
          this.snapshot.meta.splice(index, 1);
        },
        getInventoryForOrg() {
            this.folder="Active Foods";
            let org = this.user.organization;
            
            let that = this;

            var db = firebase.firestore();
            
            var listRef = db.collection("organizations")
            .doc(org)
            .collection("inventory")
            .orderBy("date_scanned", "desc");
            

            listRef.onSnapshot(querySnapshot => {    
                that.items = [];       
                querySnapshot.forEach(function(doc) {                              
                    var item = doc.data();

                    item.date_scanned = item.date_scanned.toDate();
                    item.id = doc.id;

                    
                    
                    if(typeof item.flagged == "undefined") {
                        item.flagged = false
                    }
                    
                    if(item.status == "active" && item.flagged == false)
                        that.items.push(item);

                });
            })
        },

        loadItem(item) {
          console.log(item);
          this.$router.push("/singleFood/"+item.id)
        },

        getArchivedItems() {
            this.folder="Archived Foods";
            let org = this.user.organization;
            
            let that = this;

            var db = firebase.firestore();
            
            var listRef = db.collection("organizations")
            .doc(org)
            .collection("inventory")
            .orderBy("date_scanned", "desc");
            

            listRef.onSnapshot(querySnapshot => {    
                that.items = [];       
                querySnapshot.forEach(function(doc) {                              
                    var item = doc.data();

                    item.date_scanned = item.date_scanned.toDate();
                    item.id = doc.id;

                    
                    
                    if(typeof item.flagged == "undefined") {
                        item.flagged = false
                    }
                    
                    if(item.status == "archived" && item.flagged == false)
                        that.items.push(item);

                });
            })
        },

        getFlaggedItems() {
            this.folder="Flagged Foods";
            let org = this.user.organization;
            
            let that = this;

            var db = firebase.firestore();
            
            var listRef = db.collection("organizations")
            .doc(org)
            .collection("inventory")
            .orderBy("date_scanned", "desc");
            

            listRef.onSnapshot(querySnapshot => {    
                that.items = [];       
                querySnapshot.forEach(function(doc) {                              
                    var item = doc.data();

                    item.date_scanned = item.date_scanned.toDate();
                    item.id = doc.id;

                    
                    
                    if(typeof item.flagged == "undefined") {
                        item.flagged = false
                    }
                    
                    if(item.status == "active" && item.flagged == true)
                        that.items.push(item);

                });
            })
        },

        archiveItem(item) {
            console.log(item);
            let that = this;

            var db = firebase.firestore();

            db.collection("organizations")
            .doc(that.user.organization)
            .collection("inventory")
            .doc(item.id)
            .set({
              status:"archived"
            }, 
            {merge:true})
        },

        toggleFlag(item) {
          let that = this;
          var db = firebase.firestore();
          db.collection("organizations")
            .doc(that.user.organization)
            .collection("inventory")
            .doc(item.id)
            .set({
              flagged:false
            }, 
            {merge:true})
        },

        deleteItem(item) {
            console.log(item);
            let that = this;

            var db = firebase.firestore();

            db.collection("organizations")
            .doc(that.user.organization)
            .collection("inventory")
            .doc(item.id)
            .delete()
            
        },

        prepSnapshot() {
          this.showExport = true;
        },

        saveSnapshot() {
          this.showExport = false;
          var db = firebase.firestore();
          let org = this.user.organization;
          this.snapshot.DateSaved = new Date()
          this.snapshot.items = this.items;
          let snapshot = this.snapshot;

         

          var docRef = db.collection("organizations")
          .doc(org)
          .collection("reports")
          .doc();
          
          docRef.set(snapshot).then(() => {
              this.$router.push("/singleReport/"+docRef.id);
              console.log("Document successfully updated!");
              
          })
          .catch((error) => {
              // The document probably doesn't exist.
              console.error("Error updating document: ", error);
          });
        }
    },

    mounted(){
        firebase.auth().onAuthStateChanged(user => {
        let that = this;
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

              that.getInventoryForOrg();

            });
          
        }
      });
    }
}
</script>

<style scoped>
    tr:first-child td {
        background-color:rgb(250, 242, 138)!important;
    }
</style>