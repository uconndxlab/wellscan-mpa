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

      <v-select
          :items="swapcats"
          placeholder="All Categories"
          item-text="name"
          item-value="abbr"
          v-model="activeCat"
          dense
          solo
          @change="getItems(activeCat)"
      
        ></v-select>
   

    <v-toolbar
        flat
        dark
        color="primary"
    >

        <v-toolbar-title tile>All Foods 
        <span v-if="activeCat != ''">
            <v-chip
              class="ma-2"
             
            >
              {{activeCat}}
            </v-chip>
        </span> </v-toolbar-title>
        
        <v-spacer></v-spacer>





    </v-toolbar>
  <v-data-table
      :headers="headers"
      :items="items"
      :items-per-page="15"
      flat
      
      class="elevation-1 rounded-0"
    >
  
      <template v-slot:[`item.upc`]="{ item }">
        <a @click="setActiveItem(item)" href="#">{{item.upc}}</a>
      </template>

    <template v-slot:[`item.rankings.tags`]="{ item }">

        <span v-if = "typeof item.rankings.tags != 'undefined'"> 

          <v-select
            v-model="item.rankings.tags"
            :items="item.rankings.tags"
            attach
            chips
            dense
            multiple
          ></v-select>

        </span>

    </template>
    
    <template v-slot:[`item.rankings.swap.rank`]="{ item }">
      <v-chip
         v-if = "typeof item.rankings.swap !== 'undefined'"
         :color="item.rankings.swap.rank"
        dark
      >
        {{ item.rankings.swap.rank}}
      </v-chip>

      <v-chip  v-if = "typeof item.rankings.swap == 'undefined'">
        --
      </v-chip>
    </template>

    <template v-slot:[`item.rankings.fano`]="{ item }">
      <v-select
          :items="swapcats[item.swapIndex].fano"
          
          item-text="name"
          item-value="abbr"
          v-model="item.rankings.fano.category"
          
          dense
      
        ></v-select>
    </template>

    <template v-slot:[`item.rankings.swap.category`]="{ item }">
      
      <v-select
          :items="swapcats"
          item-text="name"
          item-value="abbr"
          v-model="item.rankings.swap.category"
  
          dense
      
        ></v-select>

    </template>

    <!-- <template v-slot:[`item.actions`]="{ item }">
    </template> -->
  </v-data-table>

<v-dialog
      v-model="showSingleFood"
      max-width="600px"
    >
      <v-card v-if="showSingleFood">
        <v-card-title>
          <span class="text-h5">Edit Food</span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col
                cols="12"
                
              >
                <v-text-field
                  label="Name"
                  v-model="activeItem.name"
                  :placeholder="activeItem.name"
                  required
                ></v-text-field>
              </v-col>
              <v-col
                cols="4"
              >
                <v-text-field
                  label="Sodium"
                  v-model="activeItem.nutrition.nf_sodium"
                ></v-text-field>
              </v-col>
              <v-col
                cols="4"
              >
                <v-text-field
                  label="Sugars"
                  v-model="activeItem.nutrition.nf_sugars"
                ></v-text-field>
              </v-col>

              <v-col
                cols="4"
              >
                <v-text-field
                  label="Saturated Fat"
                  v-model="activeItem.nutrition.nf_saturated_fat"
                ></v-text-field>
              </v-col>

              <v-col
                cols="12"
              >
                      <v-select
          :items="swapcats"
          placeholder="All Categories"
          item-text="name"
          item-value="abbr"
          v-model="activeItem.rankings.swap.category"
          dense
          solo
              
        ></v-select>
              </v-col>

            </v-row>
          </v-container>
          <small>*indicates required field</small>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="blue darken-1"
            text
            @click="showSingleFood = false"
          >
            Close
          </v-btn>
          <v-btn
            color="blue darken-1"
            text
            @click="showSingleFood = false"
          >
            Save
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>





  
 </div>
</template>

<script>
// @ is an alias to /src

import firebase from "firebase/app";
export default {
  name: 'AllFoods',
  data () {
      return {

        activeCat:"",
        activeItem:null,
        showSingleFood:false,

        headers: [

         {
            text:"UPC",
            value:"upc",
            sortable:true,
            width:'10%'
          },

          {
            text: 'Name',
            align: 'start',
            sortable: false,
            value: 'name',
            width:'20%'
          },

        

          {
            text: "HER Category",
            align:"start",
            sortable:true,
            value:"rankings.swap.category",
            width:'20%'
          },

          {
            text: "FANO Category",
            align:"start",
            sortable:false,
            value:"rankings.fano"
          },

          {
            text: 'Tags',
            align: 'start',
            sortable: false,
            value: 'rankings.tags',
          },
          
          { text: 'Rank', value: 'rankings.swap.rank', sortable:true },
          
        ],

        swapcats: [], 
        
        items: [],
      }
    },

    methods: {
        setActiveItem(item){
          this.activeItem = item;
          this.showSingleFood = true;
          console.log(this.activeItem);

        },
       getHERCatsFromFirestore() {
          var db = firebase.firestore();
          var that = this;
          var listRef = db.collection("her-cats");

          
          listRef.onSnapshot(querySnapshot => {    
            this.swapcats = [];       

            querySnapshot.forEach(function(doc) {
                var cat = doc.data();
                let fanoCodes = [];

                let fano = listRef.doc(doc.id).collection("fano-codes");
                fano.onSnapshot(querySnapshot => {
                  querySnapshot.forEach(function(unc) {
                    fanoCodes.push({
                      name: unc.data().name,
                      id:unc.id
                    });
                  });
                });

                that.swapcats.push({
                  abbr:doc.id,
                  name: cat.name,
                  fano: fanoCodes
                });


                
            });
          })
        },

        populateFANODropdown() {

        },

        getItems(swapcat = '') {
          let that = this;
          this.items = [];
          let api_suffix = "";
          var api_prefix = "https://v2.api.wellscan.io/api/";
          if(swapcat !== '') {
            api_suffix = "category/"+swapcat;
          }
          fetch(api_prefix + "foods/" + api_suffix)
          .then(
            function(response) {

              

              if (response.status !== 200) {
                  console.log('Looks like there was a problem. Status Code: ' +
                  response.status);
                  return;
              } 
              
              response.json().then(function(data) {
                  
                  //console.log(data);
                  //that.items = data;

                  data.forEach(function(item) {

                    let swapIndex = typeof item.rankings.swap != 'undefined' ? that.swapcats.findIndex(x => x.abbr === item.rankings.swap.category) : 0;

                    that.items.push({
                      name:item.name,
                      upc:item.upc,
                      swapIndex:swapIndex,

                      nutrition:item.nutrition,
                      
                      rankings: {
                        swap: typeof item.rankings.swap != 'undefined' ? item.rankings.swap : {
                          category:"Uncategorized",
                          rank:"unranked"
                        },
                        fano: typeof item.rankings.fano != 'undefined' ? item.rankings.fano : [{
                          name:"none",
                          abbr:"none"
                        }],
                        tags: typeof item.rankings.tags != 'undefined' ? item.rankings.tags :[
                          
                        ]
                      }
                    })
                  })
                  console.log(that.items);
                })
              }
          )
        }
    },

    mounted(){

      // first, populate all the HER Cats
      // then populate all the FANO codes for each HER Cat


      this.getHERCatsFromFirestore();

      firebase.auth().onAuthStateChanged(user => {
        if (!user) {
          this.$router.replace({name:"Login"});
        } else {
          //this.getItems();
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