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

        <v-toolbar-title tile>All Foods</v-toolbar-title>
        
        <v-spacer></v-spacer>

   



    </v-toolbar>
 <v-data-table
    :headers="headers"
    :items="items"
    :items-per-page="15"
    flat
    
    class="elevation-1 rounded-0"
  >
  

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
          @change="setFanoCatsForHERCatAndApplyToItem(item)"
          dense
      
        ></v-select>

    </template>

    <!-- <template v-slot:[`item.actions`]="{ item }">
  


    

    </template> -->
  </v-data-table>



 


 </div>
</template>

<script>
// @ is an alias to /src

import firebase from "firebase/app";
export default {
  name: 'AllFoods',
  data () {
      return {

        headers: [

         {
            text:"UPC",
            value:"upc",
            sortable:true,
            width:'20%'
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

        swapcats: [
          {name:'Fruit/Vegetable', abbr:"fruit-vegetable", fano:[] },
          {name:'Protein', abbr:'protein', fano:[]},
          {name:'Dairy', abbr:'dairy' , fano:[]},
          {name:'Non-Dairy Alternative', abbr:'non-dairy-alternative' , fano:[]},
          {name:'Beverage', abbr:'beverage' , fano:[]},
          {name:'Mixed Dish', abbr:'mixed-dish', fano:[]},
          {name:'Whole Grain Snack', abbr:'snack-whole-grain' , fano:[]},
          {name:'Snack', abbr:'processed-packaged-snack' , fano:[]},
          {name:'Whole Grain', abbr:'grain-whole' , fano:[]},
          {name:'Non-whole Grain', abbr:'grain' , fano:[]},
          {name:'Dessert', abbr:'dessert' , fano:[]},
          {name:'Baking Supply/Condiment', abbr:'baking-supplies-condiments' , fano:[]}
        ], 
        swap_fano: {},
        items: [],
      }
    },

    methods: {
       getFanoCatsFromFirestore() {
          var db = firebase.firestore();
          var that = this;
          var listRef = db.collection("fano-codes");

          

          listRef.onSnapshot(querySnapshot => {    
            that.fanocats = [];       

            querySnapshot.forEach(function(doc) {
                 
                
                var cat = doc.data();

                let index = that.swapcats.findIndex(x => x.abbr ===cat.her_parent);
                console.log(index);

                that.swapcats[index]['fano'].push({
                  name:doc.id,
                  her_parent: cat.her_parent,
                  tags: cat.tags
                });

              console.log(that.swapcats);

            });
            console.log(that.swapcats);
        })
        },

        setFanoCatsForHERCatAndApplyToItem(item) {
          // get fanocat for item.rankings.swap.category

          let index = this.swapcats.findIndex(x => x.abbr === item.rankings.swap.category);

          item.swapIndex = index;

          // let her_cat = this.swapcats[index];

          // console.log(her_cat);

          // item.rankings.fano = her_cat.fano;
          
          // item.rankings.tags = her_cat.fano.tags;
          // console.log(item.rankings.fano);

        },

        setTagsForFanoCatAndApplyToItem(){

        
          
        }
    },

    mounted(){
        this.getFanoCatsFromFirestore();
        firebase.auth().onAuthStateChanged(user => {
        if (!user) {
          this.$router.replace({name:"Login"});
        } else {

          let that = this;
          
        var api_prefix = "https://v2.api.wellscan.io/api/";
        fetch(api_prefix + "foods/")
        .then(
           function(response) {
            if (response.status !== 200) {
                console.log('Looks like there was a problem. Status Code: ' +
                response.status);
                return;
            } 
            
            response.json().then(function(data) {
                
                console.log(data);
                //that.items = data;

                data.forEach(function(item) {

                let swapIndex = typeof item.rankings.swap != 'undefined' ? that.swapcats.findIndex(x => x.abbr === item.rankings.swap.category) : 0;

                  that.items.push({
                    name:item.name,
                    upc:item.upc,
                    swapIndex:swapIndex,
                    
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

              })
            }
        )
          
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