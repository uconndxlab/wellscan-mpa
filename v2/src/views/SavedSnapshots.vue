<template>
    <div>
    <v-list three-line>
      <template v-for="(item, index) in items">
        <v-list-item
          :key="index"
        >

            <v-list-item-content @click="$router.push('singleReport/'+item.id)">
                <v-list-item-title v-html="item.name"></v-list-item-title>
                <v-list-item-subtitle v-html="item.DateSaved"></v-list-item-subtitle>
            </v-list-item-content>
          <v-list-item-action>
            <v-btn icon @click="removeItem(item.id)">
                <v-icon>                        
                  mdi-delete-outline
                </v-icon>
            </v-btn>
            </v-list-item-action>
        </v-list-item>
       
      </template>
    </v-list>
    </div>
</template>

<script>
// @ is an alias to /src
import firebase from "firebase/app";
export default {
  name: 'SavedSnapshots',
  data () {
      return {
        user: {
            displayName: "",
            email: "",
            organization:"organization",
            loggedIn:false,
            usr_type:"",
        },
        items:[]
      }
    },

    methods: {
      getItems() {
      
          var that = this;
        
          var org = this.user.organization;
          var db = firebase.firestore();
          var ret = [];
          var listRef = db.collection("organizations")
          .doc(org)
          .collection("reports")
          .orderBy("DateSaved");

          listRef.onSnapshot(querySnapshot => {

              //doc.data() is never undefined for query doc snapshots
              ret = [];
              querySnapshot.forEach(function(doc) {
                  var item = doc.data();
                  item.DateSaved = item.DateSaved.toDate();
                  item.id = doc.id;
                  ret.push(item);
              });
              that.items = ret;

              
              
  
          })
      
      },

      removeItem(id) {
            let that = this;

            var db = firebase.firestore();

            db.collection("organizations")
            .doc(that.user.organization)
            .collection("reports")
            .doc(id)
            .delete()
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

              that.getItems();

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