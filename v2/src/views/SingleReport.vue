<template>
    <div>
    <v-card tile>
        <v-toolbar
        color="primary"
        flat
        fixed
        dark
        >

            

            
            <v-btn
                
                
                @click="downloadCSV"
            >
                Download CSV <v-icon>mdi-download</v-icon>
            </v-btn>
            <v-spacer></v-spacer>
            {{snapshot.name}}
            <v-spacer></v-spacer>
            <v-btn
                color="error"
                @click="deleteReport" 
            >
                Delete <v-icon>mdi-delete</v-icon>
            </v-btn>

        </v-toolbar>
    <v-card-text>
        <v-row>
            <v-col cols="12">{{snapshot.DateSaved}}</v-col>
            <v-col cols="12" v-bind:key="index" v-for="(field,index) in snapshot.meta">
                <strong>{{field.name}}</strong>: {{field.value}}
            </v-col>
        </v-row>
          </v-card-text>
    </v-card>
 <v-data-table
    :headers="headers"
    :items="snapshot.items"
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
      <v-btn v-if="item.status == 'active'" @click="archiveItem(item)" small>
        archive
        <v-icon
          small          
        >
          mdi-archive
        </v-icon>
      </v-btn>

      <v-btn v-if="item.status == 'archived'" color="error" @click="deleteItem(item)" small>
        delete
        <v-icon
          small
        >
          mdi-delete
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
  name: 'SingleReport',
  data () {
      return {
        user: {
            displayName: "",
            email: "",
            organization:"organization",
            loggedIn:false,
            usr_type:"",
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

      
        ],
        snapshot:{},
      }
    },

    props:['reportId'],

    methods: {
        getItems() {
            console.log(this.reportId);
            var that = this;
        
            var org = this.user.organization;
            var db = firebase.firestore();
            
            
            var docRef = db.collection("organizations")
            .doc(org)
            .collection("reports")
            .doc(that.reportId);

            docRef.get().then((doc) => {
                if (doc.exists) {
                    var info = doc.data();

                    this.snapshot = info;
                    this.snapshot.DateSaved = info.DateSaved.toDate();
    
                } else {
                    // doc.data() will be undefined in this case
                    console.log("No such document!");
                    }
                }).catch((error) => {
                    console.log("Error getting document:", error);
            });    
        },

        deleteReport() {
            var that = this;
            var org = this.user.organization;
            var db = firebase.firestore();
            var docRef = db.collection("organizations")
            .doc(org)
            .collection("reports")
            .doc(that.reportId);
            docRef.delete().then(() => {
              that.$router.push("/savedSnaps");
            });
        },
        
        ConvertToCSV(objArray) {
            var array = typeof objArray != 'object' ? JSON.parse(objArray) : objArray;
            var str = '';
            for (var i = 0; i < array.length; i++) {
                var line = '';
                for (var index in array[i]) {
                    if (line != '') line += ','
                    line += array[i][index];
                }
                str += line + '\r\n';
            }
            return str;
        },

        downloadCSV() {
            var items = this.snapshot.items;
            var exp = [];
            var that = this;
            
            for (var i = 0; i < items.length; i++) {
                var row = items[i];
                if (!row.name)
                    row.name = " ";

                var obj = {
                    food:row.name.replace(/,/g, ''),
                    upc: row.upc,
                    rank:row.rank
                }

                var headers = ["Food Name", "UPC", "Rank"]

                if(typeof that.snapshot.meta !== "undefined") {
                  for(var u =0; u < that.snapshot.meta.length; u++) {
                    var n = that.snapshot.meta[u].name;
                    var v = that.snapshot.meta[u].value;
                    headers.push(n)
                    obj[n] = v;
                  }
                }
                exp.push (obj);
            }

            
            
            exp.unshift(headers);
            
            let csvContent = "data:text/csv;charset=utf-8,"
            csvContent += this.ConvertToCSV(exp);
            var encodedUri = encodeURI(csvContent);
              var link = document.createElement('a');
            link.setAttribute('href',encodedUri);
            link.setAttribute('download', that.snapshot.name + ".csv");
            link.click();
        },        

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

    .unranked {
        background-color: #414042!important;
        color:#fff;
    }
</style>