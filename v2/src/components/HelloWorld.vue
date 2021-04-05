<template>

  <div>
    <v-list three-line>
      <template v-for="(item, index) in items">
        <v-list-item
          :key="index"
          v-if="(item.status == 'active')"
        >
          <v-list-item-avatar :class="item.rank"></v-list-item-avatar>

          <v-list-item-content
              @click="loadSingleFood(index)"
            >
            <v-list-item-title v-html="item.name"></v-list-item-title>
            <v-list-item-subtitle v-html="item.upc"></v-list-item-subtitle>
          </v-list-item-content>
          <v-list-item-action>
            <v-btn icon @click="removeItem(index)">
                <v-icon>                        
                  mdi-delete-outline
                </v-icon>
            </v-btn>
            </v-list-item-action>
            
        </v-list-item>
       
      </template>
    </v-list>
  
    <!-- <v-btn block>Load More</v-btn> -->
    <v-fab-transition>
      <v-btn
        color="primary"
        dark
        fixed
        bottom
        right
        fab
        @click="openScanner = !openScanner"
      >
        <v-icon>mdi-barcode-scan</v-icon>
      </v-btn>
    </v-fab-transition>

    <v-bottom-sheet v-model="openScanner">
      <v-sheet
        class="text-center" 
      >

        <div>
          <v-btn
            block
            @click="openScanner = !openScanner"
          >
            close
          </v-btn>
          <StreamBarcodeReader v-if="openScanner"
          @decode="onBarcodeDecode"
          @loaded="onCameraLoaded"
          
          >      
          </StreamBarcodeReader>
          <v-text-field
            class="mx-6 centered-input"
            v-model="upcToSearch"
            append-outer-icon="mdi-magnify"
            @click:append-outer="addFoodToInventory"
          ></v-text-field> 

        </div>


      </v-sheet>
    </v-bottom-sheet>

    <v-dialog fullscreen transition="dialog-bottom-transition" v-model="foodClicked">
          <v-card tile>
              <v-toolbar
              color="secondary"
              flat
              fixed
              dark
              >
                <v-btn
                  color="white"
                  text
                  @click="foodClicked = !foodClicked"
                >
                  <v-icon>mdi-close</v-icon>
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn
                  color="white"
                  text
                  @click="saveFood()"
                >
                  save
                </v-btn>
              </v-toolbar>
              <div
                :color="activeFood.rank"
                :class="['food-header', activeFood.rank]"
                height="125px"
              >
                
                <v-card-subtitle  class="text-left pb-0">{{activeFood.rank}}</v-card-subtitle>
                <v-btn icon style="position:absolute; right:20px; top:70px;" class="text-right pb-0 mb-0 mt-0">
                  <v-icon v-if="activeFood.flagged" color="white">mdi-flag</v-icon>
                </v-btn>
                <v-card-title  v-model="activeFood.name" class="text-left pt-0">{{activeFood.name}}</v-card-title>
                <v-card-subtitle class="text-left">{{activeFood.upc}}</v-card-subtitle>

              </div>
              <v-card-text style="position:relative; padding-top:20px;" class="text-left">
              <v-subheader class="pa-0">
                <v-btn icon class="text-right pb-0 mb-0 mt-0"><v-icon color="primary">mdi-information-outline</v-icon></v-btn>
                Category*
              </v-subheader>
              <v-chip
                class="ma-2 rounded-0"
                :input-value="activeFood.flagged"
                @click="flagFood()"
                style="position:absolute; right:0px; top:-25px; z-index:999"
                color="secondary"
                filter
                filter-icon="mdi-flag"
                
              >
                Flag <span v-if="activeFood.flagged" style="margin-right:6px;">ged</span>for Review
              </v-chip>

                <!-- <v-text-field
                  
                  dense
                  label="Quantity"
                  type="number"
                ></v-text-field> -->


                <v-chip-group
                  active-class="primary--text"
                  v-model="activeFood.category"
                  column
                >
                  <v-chip
                    v-for="tag in tags"
                    :key="tag.abbr"
                    :value="tag.abbr"
                    @click="setCat(tag.abbr)"
                  >
                    {{ tag.name }}
                  </v-chip>
                </v-chip-group>        
              <div style="margin-top:20px">
                <v-text-field
                  
                  v-model="activeFood.name"
                  dense
                  label="Name"
                  outlined
                  @change="onFoodUpdated"
                ></v-text-field>
                <v-row>
                <v-col
                  cols="4">       
                  <v-text-field
                    dense
                    v-model="activeFood.nutrition.nf_sodium"
                    label="Sodium"
                    outlined
                    :error = "activeFood.nutrition.nf_sodium == null"
                    @change="onFoodUpdated"
                  ></v-text-field>
                </v-col>

                <v-col
                  cols="4">   
                <v-text-field
                  v-model="activeFood.nutrition.nf_sugars"
                  :error = "activeFood.nutrition.nf_sugars == null"
                  dense
                  label="Sugars"
                  outlined
                  @change="onFoodUpdated"
                ></v-text-field>
                </v-col>

                <v-col
                  cols="4">   
                <v-text-field
                  v-model="activeFood.nutrition.nf_saturated_fat"
                  :error = "activeFood.nutrition.nf_saturated_fat == null"
                  dense
                  outlined
                  @change="onFoodUpdated"
                  label="Sat. Fat"
                ></v-text-field>
                </v-col>
                </v-row>
                </div>
              </v-card-text>
              
            </v-card>
    </v-dialog>
  </div>
  
</template>

<script>
import { StreamBarcodeReader } from "vue-barcode-reader";
import firebase from "firebase/app";
import 'firebase/firestore';
  export default {
    name: 'HelloWorld',
    components: {
        StreamBarcodeReader,
      },
    data: () => ({
    user: {
          displayName: "",
          email: "",
          organization:"organization",
          loggedIn:false,
          usr_type:""
     },
      openScanner:false,
      foodClicked:false,
      upcToSearch:"",
      activeFood: {          
          name: 'Not Found',
          upc: "",
          rank:"",
          category:"",
          flagged:"",
          nutrition: {
            nf_sodium:0,
            nf_saturated_fat:0,
            nf_sugars:0
          }},
     tags: [
        {name:'Fruit/Vegetable', abbr:"fruit-vegetable" },
        {name:'Protein', abbr:'protein'},
        {name:'Dairy', abbr:'dairy'},
        {name:'Non-Dairy Alternative', abbr:'non-dairy-alternative'},
        {name:'Beverage', abbr:'beverage'},
        {name:'Mixed Dish', abbr:'mixed-dish'},
        {name:'Whole Grain Snack', abbr:'snack-whole-grain'},
        {name:'Snack', abbr:'processed-packaged-snack'},
        {name:'Whole Grain', abbr:'grain-whole'},
        {name:'Non-whole Grain', abbr:'grain'},
        {name:'Dessert', abbr:'dessert'},
        {name:'Baking Supply/Condiment', abbr:'baking-supplies-condiments'}
      ],     
     items: [],
    }),

    // watch: {
    //   activeFood: { 
    //     handler() {
    //       // food, you've changed
    //       this.onFoodUpdated();
    //     },
    //     // but haven't we all
    //     deep:true
    //   }
    // },

    methods: {
      removeItem(index) {
        var db = firebase.firestore();
        //var that = this;
        var itemToDelete = this.items[index].id;
        var org = this.user.organization;
        
        console.log(`Deleting...`+ itemToDelete);
        db.collection("organizations")
          .doc(org)
          .collection("inventory")
          .doc(itemToDelete)
          .delete().then(()=> {
            
            console.log(`Deleted...`+ itemToDelete);
          }).catch((error) => {
            console.log("Error deleting..." + error);
          });

      },

      setCat(cat){
        this.activeFood.category = cat;
        console.log(this.activeFood.category);
        this.calculateRankOfActiveFood();
      },
 
      loadSingleFood(index) {

        let that = this;
        that.foodClicked = !that.foodClicked;
        that.activeFood = that.items[index];
        fetch("https://v2.api.wellscan.io/api/foods/lookup/"+this.activeFood.upc)
        .then(
           function(response) {
            if (response.status !== 200) {
                console.log('Looks like there was a problem. Status Code: ' +
                response.status);
                return;
            } 
            
            response.json().then(function(data) {
                console.log(data);
                if(typeof data.rankings.swap !== "undefined")
                  that.activeFood.category = data.rankings.swap.category;
                
                if(typeof data.rankings.swap !== "undefined")
                  that.activeFood.rank = data.rankings.swap.rank
                
                that.activeFood.nutrition = data.nutrition;
                that.activeFood.name = data.name;

                that.onFoodUpdated();
              })
            }
        )
      },

      flagFood() {
        console.log("Flagging food...");
        this.activeFood.flagged = !this.activeFood.flagged;
        this.onFoodUpdated();
      },

      saveFood() {
        this.foodClicked = !this.foodClicked;

        // data to be sent to the POST request
        let _data = {
          upc:this.activeFood.upc,
          name: this.activeFood.name,
          nf_saturated_fat:this.activeFood.nutrition.nf_saturated_fat,
          sugars:this.activeFood.nutrition.nf_sugars,
          sodium:this.activeFood.nutrition.nf_sodium,
          category:this.activeFood.category,
          rank:this.activeFood.rank,
          nutrition_source:"manual",
          nutrition_method:"manual"
        }

        fetch('https://v2.api.wellscan.io/api/foods/'+this.activeFood.upc, {
          method: "PUT",
          body: JSON.stringify(_data),
          headers: {"Content-type": "application/json; charset=UTF-8"}
        })
        .then(response => response.json()) 
        .then(json => console.log(json))
        .catch(err => console.log(err));
  
      },

      onBarcodeDecode(results) {
        this.upcToSearch = results;
        // navigator.vibrate(200);
        // window.navigator.vibrate(200);
        this.addFoodToInventory();
      },

      onCameraLoaded() {
        this.upcToSearch = "";
        console.log("Loaded camera");
      },

      lookUpFood() {

      },

      addFoodToInventory() {
          var db = firebase.firestore();
          var that = this;
          
          db.collection("organizations")
          .doc(that.user.organization)
          .collection("inventory")
          .add({
              upc:that.upcToSearch,
              status:"active",
              name:"Unknown",
              rank:"unranked",
              scanned_by:that.user.email,
              date_scanned:new Date(),
            })
          .then(function(docRef) {
              that.loadSingleFood(0);
              that.openScanner =  !that.openScanner;
              console.log("Your item has been saved with ID " + docRef.id);
          })
          .catch(function(error) {
              console.error("Error adding document: ", error);
        });
      },

      getInventoryScannedByUser() {
        let org = this.user.organization;
        let user= this.user.email;
        let that = this;

        var db = firebase.firestore();
        
        var listRef = db.collection("organizations")
        .doc(org)
        .collection("inventory")
        // .where("scanned_by", "==", user)
        .orderBy("date_scanned", "desc")
        .limit(40);
        

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
                
                if(item.scanned_by == user)
                  that.items.push(item);

            });
        })
      },

      calculateRankOfActiveFood() {
          let that = this;
          
            console.log("Ready to calculate rank..."); 
            console.log(`Ranking for ${this.activeFood.category}`);
            fetch("https://v2.api.wellscan.io/api/foods/rankFromNuts/" + this.activeFood.category + "/" + this.activeFood.nutrition.nf_saturated_fat + "/" + this.activeFood.nutrition.nf_sodium + "/" + 0 + "/" + this.activeFood.nutrition.nf_sugars)
            .then(
              function(response) {
                if (response.status !== 200) {
                    console.log('Looks like there was a problem. Status Code: ' +
                    response.status);
                    return;
                }

                // Examine the text in the response
                response.json().then(function(data) {
                  that.activeFood.rank = data.rank;
                  
                  that.onFoodUpdated();
                 

                });
              });
          

      },

      onFoodUpdated() {
        let that = this;

        var db = firebase.firestore();

        db.collection("organizations")
        .doc(that.user.organization)
        .collection("inventory")
        .doc(that.activeFood.id)
        .set({
          name:that.activeFood.name,
          category:that.activeFood.rank,
          status:that.activeFood.status,
          flagged:that.activeFood.flagged,
          rank:that.activeFood.rank
        }, 
        {merge:true})
      }
    },

  computed : {
    



  },

    mounted() {
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

              that.getInventoryScannedByUser();

            });
        }
      });
      
    }
  }
</script>
<style scoped>
.v-card__title {
  word-break: break-word;
  height:2em;
  line-height:1em;
  text-overflow:ellipsis;
  overflow:hidden;
}

.v-toolbar--flat {
  transition: .4s;
}

.v-card__title + .v-card__subtitle {
  margin-top:10px;
}

.v-list-item__title, .v-list-item__subtitle {
  white-space:unset;
  max-height:4ch;
}

.v-toolbar--flat, .food-header {
  box-shadow: none!important;
}

.sometimes, .yellow {
    background-color: #F6D860 !important;
    color:#333;
}

.often, .green {
    background-color: #28A745 !important;
    color:#fff;
}

.often .v-card__subtitle {
  color:#fff!important;
}

.rarely, .red {
    background-color: #dc3545 !important;
    color:white;
}

.rarely .v-card__subtitle {
  color:#fff!important;
}

.unranked {
    background-color: #414042!important;
    color:#fff;
}

.unranked .v-card__subtitle {
  color:#fff!important;
}

.v-avatar.unranked::after {
    content:"?";
    color:#ffc107;
}

.v-avatar {
  border-radius:3px;
}

.centered-input >>> input {
  text-align: center
}
</style>