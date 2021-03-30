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
        <v-toolbar-title tile>Inbox <small>{{user.organization}}</small></v-toolbar-title>
        
        <v-spacer></v-spacer>

        <v-btn
            dark
            color="secondary">
            <v-icon>mdi-export</v-icon>
            export
        </v-btn>    

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
                    <v-list-item-title>Inbox</v-list-item-title>
                </v-list-item>

                <v-list-item>
                    <v-list-item-title>Archived Items</v-list-item-title>
                </v-list-item>

                <v-list-item>
                    <v-list-item-title>Flagged Items</v-list-item-title>
                </v-list-item>

                <v-list-item>
                    <v-list-item-title>Saved Snapshots</v-list-item-title>
                </v-list-item>                    
            </v-list>
        </v-menu>

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
      <v-btn small>
        archive
        <v-icon
          small
          @click="deleteItem(item)"
        >
          mdi-archive
        </v-icon>
      </v-btn>
    </template>
  
  </v-data-table>
 </div>
</template>

<script>
// @ is an alias to /src

import firebase from "firebase/app";
export default {
  name: 'Inventory',
  data () {
      return {
        user: {
            displayName: "",
            email: "",
            organization:"organization",
            loggedIn:false,
            usr_type:""
        },
        headers: [
          {
            text: 'Name',
            align: 'start',
            sortable: false,
            value: 'name',
          },
          { text: 'UPC', value: 'upc' },
          { text: 'Rank', value: 'rank' },
          { text: 'Date', value:'date_scanned'},
          { text: 'BlameID', value: 'scanned_by' },
          { text: 'Actions', value:'actions', sortable: false},
        ],
        items: [
          {
            name: 'Frozen Yogurt',
            upc: "159",
            rank: "rarely",
            blameID:"joel.salisbury@gmail.com",
            datestamp:Date() 
          },
          {
            name: 'Ice cream sandwich',
            upc: "237",
            rank: "often",
            blameID:"joel.salisbury@gmail.com",
            datestamp:Date()
          },
          {
            name: 'Eclair',
            upc: "262",
            rank: "sometimes",
            blameID:"joel.salisbury@gmail.com",
            datestamp:Date()
          },
        ],
      }
    },

    methods: {
        getInventoryForOrg() {
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
                    item.nutrition = {
                    nf_sodium:0,
                    nf_saturated_fat: 0,
                    nf_sugars: 0
                    };
                    
                    
                    if(typeof item.flagged == "undefined") {
                    item.flagged = false
                    }
                    
                    
                    that.items.push(item);

                });
            })
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
