<template>
  <div>
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
                  @click="foodClicked = !foodClicked; $router.back();"
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
                  type="number"
                    dense
                    v-model="activeFood.nutrition.nf_sodium"
                    label="Sodium*"
                    outlined
                    :error = "hasNutrientError('nf_sodium')"
                    @change="calculateRankOfActiveFood"
                  ></v-text-field>
                </v-col>

                <v-col
                  cols="4">   
                <v-text-field
                  type="number"
                  v-model="activeFood.nutrition.nf_sugars"
                  :error = "hasNutrientError('nf_sugars')"
                  
                  dense
                  label="Sugars*"
                  outlined
                  @change="calculateRankOfActiveFood"
                ></v-text-field>
                </v-col>

                <v-col
                  cols="4">   
                <v-text-field
                  v-model="activeFood.nutrition.nf_saturated_fat"
                  type="number"
                  :error = "hasNutrientError('nf_saturated_fat')"
                  dense
                  outlined
                  @change="calculateRankOfActiveFood"
                  label="Sat. Fat*"
                ></v-text-field>
                </v-col>
                </v-row>
                </div>
              <fieldset :class="categoryCheck">
                <legend @click="showCategoryExplainer">
                <v-btn text><v-icon>mdi-information</v-icon> Category Information*</v-btn></legend>

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
              </fieldset>
              
                <!-- <v-file-input
                @change="handleImage"
                accept="image/png, image/jpeg, image/bmp"
                placeholder="Add a photo (optional)"
                prepend-icon="mdi-camera"
                label="Add a photo (optional)"
              ></v-file-input>
              <img :src="activeFood.img"> -->
              </v-card-text>              
            </v-card>
    </v-dialog>

<v-dialog fullscreen v-model="showCategoryInfo">
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
          @click="showCategoryInfo = !showCategoryInfo"
        >
          <v-icon>mdi-close</v-icon>
        </v-btn>

        Category Information

      </v-toolbar>
      <v-card-text>
         <v-container fluid>
          <v-row>
            <v-col
              cols="12"
              class="ma-5"
            >
              <p>Category is a required field when calculating a rank.</p>
              
              <h2>Fruits and Vegetables</h2>
              <p>Fresh, canned, frozen, and dried fruits and vegetables, frozen broccoli with cheese sauce, apple sauce, tomato sauce, 100% juice, 100% fruit popsicle</p>
              
              <h2>Grains</h2>
              <p>Bread, rice, pasta, grains with seasoning mixes</p>

              <h2>Grains (Whole)</h2>
              <p>First ingredient must be whole</p>
              
              <h2>Protein</h2>
              <p>Animal (beef, pork, poultry, sausage, deli meats, hot dogs, eggs) and plant proteins (nuts, seeds, veggie burgers, soy, beans, peanut butter</p>

              <h2>Dairy</h2>
              <p>Milk, cheese, yogurt</p>

              <h2>Non-Dairy Alternatives</h2>
              <p>All plant-based milks, yogurts and cheeses</p>

              <h2>Beverages</h2>
              <p>Water, soda, coffee, tea, sports drinks, non-100% juice products </p>

              <h2>Mixed Dishes</h2>
              <p>Frozen meals, soups, stews, macaroni and cheese</p>
              
              <h2>Snacks</h2>
              <p>Chips (including potato, corn, and other vegetable chips), crackers, granola and other bars, popcorn</p>

              <h2>Snacks (Whole Grain)</h2>
              <p>If a grain is the first ingredient, it must be a whole grain</p>

              <h2>Desserts</h2>
              <p>Ice cream, frozen yogurt, chocolate, cookies, cakes, pastries, snack cakes, baked goods, cake mixes</p>

              <h2>Condiments and Cooking Staples</h2>
              <p>These items are not ranked. Spices, oil, butter, plant-based spreads, flour, salad dressing, jarred sauces (except tomato sauce), seasoning, salt, sugar </p>
            
            </v-col>
          </v-row>
         </v-container>
      </v-card-text>
  </v-card>
</v-dialog  >

  </div>
  
</template>

<script>

import firebase from "firebase/app";
import 'firebase/firestore';
  export default {
    name: 'InventoryItem',
    components: {
        
      },
    props:['inventoryId'],
    data: () => ({
    user: {
          displayName: "",
          email: "",
          organization:"organization",
          loggedIn:false,
          usr_type:""
     },
      foodClicked:false,
      upcToSearch:"",
      showCategoryInfo: false,
      activeFood: {          
          name: 'Not Found',
          upc: "",
          rank:"",
          category:"",
          flagged:"",
          nutrition: {
            nf_sodium:"",
            nf_saturated_fat:"",
            nf_sugars:""
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

      showCategoryExplainer() {
        this.showCategoryInfo = true;
      },

      setCat(cat){
        this.activeFood.category = cat;
        console.log(this.activeFood.category);
        this.calculateRankOfActiveFood();
      },
 
      loadSingleFood() {

        let that = this;
        that.foodClicked = !that.foodClicked;       
   
        var api_prefix = "https://v2.api.wellscan.io/api/";
         //api_prefix = "http://localhost:8000/api/"
        fetch(api_prefix + "foods/lookup/"+this.activeFood.upc)
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
                that.foodClicked = true;
              })
            }
        )
      },

      flagFood() {
        console.log("Flagging food...");
        this.activeFood.flagged = !this.activeFood.flagged;
        this.onFoodUpdated();
      },

      getFoodByInventoryId() {
        let that = this;

        var db = firebase.firestore();
        console.log(that.inventoryId);
        db.collection("organizations")
            .doc(that.user.organization)
            .collection("inventory")
            .doc(that.inventoryId).get().then(function(doc){
        //   name: 'Not Found',
        //   upc: "",
        //   rank:"",
        //   category:"",
        //   flagged:"",

                var food = doc.data();
                
                that.activeFood.id = doc.id;
                that.activeFood.name = food.name;
                that.activeFood.upc = food.upc;
                that.activeFood.rank = food.rank;
                that.activeFood.category = food.category;
                that.activeFood.flagged = food.flagged;
                that.activeFood.status = food.status;
                that.loadSingleFood();
            })
      },

      saveFood() {
        console.log("saveFood()...");
        this.foodClicked = !this.foodClicked;
        var api_prefix = "https://v2.api.wellscan.io/api/";
         //api_prefix = "http://localhost:8000/api/"
        this.$router.back();
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

        console.log(_data);

        fetch(api_prefix + 'foods/'+this.activeFood.upc, {
          method: "PUT",
          body: JSON.stringify(_data),
          headers: {"Content-type": "application/json; charset=UTF-8"}
        })
        .then(response => response.json()) 
        .then(json => console.log(json))
        .catch(err => console.log(err));
  
      },

      calculateRankOfActiveFood() {
          let that = this;
          var api_prefix = "https://v2.api.wellscan.io/api/";
            //api_prefix = "http://localhost:8000/api/"
            console.log("CalculateRankOfActiveFood(): Ready to calculate rank..."); 
            console.log(`Ranking for ${this.activeFood.category}`);
            console.log(api_prefix+"foods/rankFromNuts/" + this.activeFood.category + "/" + this.activeFood.nutrition.nf_saturated_fat + "/" + this.activeFood.nutrition.nf_sodium + "/" + 0 + "/" + this.activeFood.nutrition.nf_sugars)
            fetch(api_prefix+"foods/rankFromNuts/" + this.activeFood.category + "/" + this.activeFood.nutrition.nf_saturated_fat + "/" + this.activeFood.nutrition.nf_sodium + "/" + 0 + "/" + this.activeFood.nutrition.nf_sugars)
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
      },

      hasNutrientError(nutrient) {
        return this.activeFood.nutrition[nutrient].length == 0;
      }
    },


  computed : {
    categoryCheck() {
      if(this.activeFood.category == null) {
        return "error-outline"
      } else {
        return "";
      }
    }
 

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

              that.getFoodByInventoryId();

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

fieldset {
  padding:5px;
  border:1px solid rgba(0,0,0,0.38);
  border-radius:4px;
}

fieldset legend {
  padding:5px;
  font-size:11px;
}

.error-outline {
  color:#b71c1c!important;
}

fieldset.error-outline {
  border:1px solid #b71c1c;
}

</style>