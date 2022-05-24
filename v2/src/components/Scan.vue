<template>

  <div>
    <div v-if="!items.length">
    <v-card
    flat tile>
      <v-alert
        icon="mdi-barcode"
        prominent
        text
        
        type="info"
      >
        <strong>No recent foods.</strong> <p>When you scan foods, they'll show up in a list on this screen.</p>
      </v-alert>
    </v-card>
    </div>
    <v-list three-line v-if="items.length !== 0">
      <template v-for="(item, index) in items">
        <v-list-item
          ripple
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
            <v-btn icon @click="confirmDelete(index)">
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
        color="accent"
        dark
        fixed
        bottom
        right
        tile
        
        fab
        @click="openScanner = !openScanner; $router.push('#open-scanner');"
      >
        <v-icon>mdi-barcode-scan</v-icon>
      </v-btn>
    </v-fab-transition>

    <v-bottom-sheet v-model="openScanner">
      <v-sheet
        class="text-center" 
      >
      <v-toolbar
      color="secondary"
      
      fixed
      
      >
        <v-btn
          color="primary"
          
          text
                      
            @click="openScanner = !openScanner; $router.back();"
        >
          <v-icon>mdi-close</v-icon>
        </v-btn>
    
        <v-spacer></v-spacer>
          <v-text-field
            class="centered-input"
            v-model="upcToSearch"
          ></v-text-field> 

          <v-spacer></v-spacer>
        <v-btn
          color="primary"
          text
          @click="addFoodToInventory"
        >
          submit
        </v-btn>
          
      </v-toolbar>
        <div>
          <StreamBarcodeReader v-if="openScanner"
          @decode="onBarcodeDecode"
          @loaded="onCameraLoaded">      
          </StreamBarcodeReader>


        </div>


      </v-sheet>
    </v-bottom-sheet>

    <v-bottom-sheet scrollable  transition="dialog-bottom-transition" v-model="foodClicked">
          <v-card tile>
              <v-toolbar
              color="secondary"
              fixed
              dark
              >
                <v-btn
                  color="primary"
                  text
                  @click="foodClicked = !foodClicked; $router.back();onFoodUpdated();"
                >
                  <v-icon>mdi-close</v-icon>
                </v-btn>
                <v-spacer></v-spacer>
                <!-- <v-btn
                  color="primary"
                  text
                  @click="saveFood()"
                >
                  save
                </v-btn> -->
              </v-toolbar>
              <div
                style="position:relative;"
                :class="['food-header',activeFood.rank, activeFood.category]"
                
              >

                <v-card-subtitle class="text-left pb-0"><span v-if="activeFood.rank !== 'unranked'">Choose </span> {{activeFood.rank}}</v-card-subtitle>
                
                <v-btn icon style="position:absolute; right:20px; top:70px;" class="text-right pb-0 mb-0 mt-0">
                  <v-icon v-if="activeFood.flagged" color="white">mdi-flag</v-icon>
                </v-btn>
                <v-card-title color="primary" v-model="activeFood.name" class="text-left pt-0">{{activeFood.name}}</v-card-title>
                <v-card-subtitle class="text-left">{{activeFood.upc}}</v-card-subtitle>
                
                <v-chip
                class="ma-2 rounded-0"
                :input-value="activeFood.flagged"
                @click="showFlagDetails = true"
                style="position:absolute; right:0px; bottom:0px; z-index:999"
                color="primary"
                filter
                filter-icon="mdi-flag"
                
              >
                Flag <span v-if="activeFood.flagged" style="margin-right:6px;">ged</span>for Review
              </v-chip>
              </div>
              <v-card-text style="position:relative; " class="text-left">



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
                ></v-text-field>
                <v-row>
                <v-col
                  cols="6">       
                  <v-text-field
                  type="number"
                    dense
                    v-model="activeFood.nutrition.nf_sodium"
                    label="Sodium*"
                    @change="activeFood.rank = 'unranked'; activeFood.flagged = true;"
                    outlined
                    :error = "hasNutrientError('nf_sodium')"
                  ></v-text-field>
                </v-col>

                <v-col
                  cols="6">   
                  <v-text-field
                    v-model="activeFood.nutrition.nf_saturated_fat"
                    @change="activeFood.rank = 'unranked'; activeFood.flagged = true;"
                    type="number"
                    :error = "hasNutrientError('nf_saturated_fat')"
                    dense
                    outlined
                    label="Sat. Fat*"
                  ></v-text-field>
                  
                </v-col>

                <!-- <v-col
                  cols="2">   
                  <v-select
                    label="Sugar Type"
                    :items="sugarTypes"
                   solo
                  ></v-select>
                </v-col> -->

                

       
                </v-row>

                <v-row style="margin-top:-20px!important;">

                <v-col
                  cols="6">

                  <v-text-field
                    type="number"
                    v-model="activeFood.nutrition.nf_added_sugars"
                    :error = "hasNutrientError('nf_added_sugars')"
                    @change="activeFood.rank = 'unranked'; activeFood.flagged = true;"
                    dense
                    label="Added Sugars"
                    outlined
                    
                  ></v-text-field>
                 
                </v-col> 

                <v-col
                  cols="6">

                  <v-text-field
                    type="number"
                    :error = "hasNutrientError('nf_sugars')"
                    @change="activeFood.rank = 'unranked'; activeFood.flagged = true;"
                    dense
                    label="Total Sugars"
                    v-model="activeFood.nutrition.nf_sugars"
                    outlined
                    
                  ></v-text-field>
                 
                </v-col>                    

                </v-row>
                
                </div>
           
                <v-row>
                <v-col
                cols="12">
                <v-btn style="padding-left:0px;" color="primary" @click="showCategoryExplainer" text>Category Information*</v-btn>

                <v-chip-group
                
                  active-class="primary--text"
                  v-model="activeFood.category"
                  column
                >
                  <v-chip
                    v-for="tag in tags"
                    :key="tag.abbr"
                    :value="tag.abbr"
                    class="rounded-0"
                    @click="setCat(tag.abbr)"
                  >
                    {{ tag.name }}
                  </v-chip>
                </v-chip-group>  
                </v-col>
                <v-col v-if="readyToRank" col="12"> 
                <v-btn
                  v-if="readyToRank"
                  color="primary"
                  block
                  tile
                  depressed
                  @click="calculateRankOfActiveFood"
                >
                  Calculate Rank
                </v-btn>
                </v-col>
                </v-row>
                <v-row v-if="activeFood.rank !== 'unranked'" :class="activeFood.rank">
                  
                  <v-col cols="6" style="text-transform:capitalize"><h2>Choose {{activeFood.rank}}</h2></v-col>
                </v-row>
              
                <!-- <v-file-input
                @change="handleImage"
                accept="image/png, image/jpeg, image/bmp"
                placeholder="Add a photo (optional)"
                prepend-icon="mdi-camera"
                label="Add a photo (optional)"
              ></v-file-input>
              <img :src="activeFood.img"> -->

                  <v-fab-transition>
      <v-btn
        color="accent"
        dark
        fixed
        bottom
        right
        tile
        
        fab
        @click="foodClicked = !foodClicked; onFoodUpdated(); openScanner = !openScanner; $router.push('#open-scanner');"
      >
        <v-icon>mdi-barcode-scan</v-icon>
      </v-btn>
    </v-fab-transition>
              </v-card-text>              
            </v-card>
    </v-bottom-sheet>

<v-dialog fullscreen v-model="showCategoryInfo">
  <v-card tile>
      <v-toolbar
      color="secondary"
      
      fixed
      
      >
        <v-btn
          color="primary"
          text
          @click="showCategoryInfo = !showCategoryInfo"
        >
          <v-icon>mdi-close</v-icon>
        </v-btn>

        <v-toolbar-title>Category Information</v-toolbar-title>

      </v-toolbar>
      <v-card-text>
         <v-container fluid>
          <v-row>
            <v-col
              cols="12"
              
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
</v-dialog>

<v-dialog  v-model="showFlagDetails">
  <v-card tile>
      <v-toolbar
      color="secondary"
      
      fixed
      
      >
        <v-btn
          color="primary"
          
          text
          @click="showFlagDetails = !showFlagDetails"
        >
          <v-icon>mdi-close</v-icon>
        </v-btn>
        <v-spacer></v-spacer>
        <v-toolbar-title>Flag Details</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-btn
          color="primary"
          text
          @click="flagFood()"
        >
          submit
        </v-btn>
      </v-toolbar>
      <v-card-text>
         <v-container fluid>
          <v-row>
            <v-col
              cols="12"
              
            >
              <v-textarea
                label="Flag Details"
                placeholder="Why are you flagging this food?"
                v-model="activeFood.flagText"
                hint="Why are you flagging this food?"
              ></v-textarea>
            </v-col>
          </v-row>
         </v-container>
      </v-card-text>
  </v-card>
</v-dialog>

<v-dialog v-model="showDeletePrompt" v-if="items.length !== 0">
<v-card>
        <v-card-title class="text-h5">
          Delete Food?
        </v-card-title>

        <v-card-subtitle>
          {{items[indexToDelete].name}}
        </v-card-subtitle>

        <v-card-text>
          This will remove the food from your organization's WellSCAN inventory.
        </v-card-text>

        <v-card-actions>
         

          <v-btn
            color="darken-1"
            text
            @click="showDeletePrompt = false"
          >
            Don't Delete
          </v-btn>
 <v-spacer></v-spacer>
          <v-btn
            color="red darken-1"
            text
            @click="showDeletePrompt = false; removeItem(indexToDelete)"
          >
            Delete
          </v-btn>
        </v-card-actions>
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
      indexToDelete:0,
      showDeletePrompt:false,
      showFlagDetails: false,
      openScanner:false,
      foodClicked:false,
      
      upcToSearch:"",
      showCategoryInfo: false,
      sugarTypes:['Total Sugars', 'Added Sugars'],
      activeFood: {          
          name: 'Not Found',
          upc: "",
          rank:"",
          category:"",
          flagged:"",
          flagText:"",
          nutrition: {
            nf_sodium:"",
            nf_saturated_fat:"",
            nf_sugars:"",
            nf_added_sugars:""
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
      confirmDelete(index){
        console.log(index);
        this.indexToDelete = index;
        this.showDeletePrompt = true;
      },
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

      handleImage(e) {
        console.log(e);
        //this.createBase64Image(e[0]);
      },

      createBase64Image(obj){
        let that = this;
        const reader = new FileReader();

        reader.onload = (e) => {
          that.activeFood.img = e.target.result;
        }

        reader.readAsBinaryString(obj);
      },

      setCat(cat){
        this.activeFood.category = cat;
        this.activeFood.rank = "unranked";
        console.log(this.activeFood.category);
      },
 
      loadSingleFood(index) {

        let that = this;
        that.foodClicked = !that.foodClicked;
        
        this.$router.push("#single-food");
   

        
        that.activeFood = that.items[index];
        

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

              })
            }
        )
      },

      flagFood() {
        console.log("Flagging food...");
        this.activeFood.flagged = !this.activeFood.flagged;
        this.showFlagDetails = false;
        this.onFoodUpdated();
        this.flagText = null;
      },

      saveFood() {
        console.log("saveFood()...");
        //var api_prefix = "https://v2.api.wellscan.io/api/";
         var api_prefix = "http://localhost:8000/api/"
        
        // data to be sent to the POST request
        let _data = {
          upc:this.activeFood.upc,
          name: this.activeFood.name,
          nf_saturated_fat:this.activeFood.nutrition.nf_saturated_fat,
          sugars:this.activeFood.nutrition.nf_sugars,
          added_sugars:this.activeFood.nutrition.nf_added_sugars,
          sodium:this.activeFood.nutrition.nf_sodium,
          category:this.activeFood.category,
          rank:this.activeFood.rank,
          nutrition_source:"manual",
          nutrition_method:"manual"
        }

        //console.log(_data);

        fetch(api_prefix + 'foods/'+this.activeFood.upc, {
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
          this.$router.back();
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
                  nf_sodium:"",
                  nf_saturated_fat: "",
                  nf_sugars: "",
                  nf_added_sugars:""
                };
                
                
                if(typeof item.flagged == "undefined") {
                  item.flagged = false
                }

                if(typeof item.flagText == "undefined") {
                  item.flagText = ""
                }
                
                if(item.scanned_by == user)
                  that.items.push(item);

            });
        })
      },

      calculateRankOfActiveFood() {
          let that = this;
          var api_prefix = "https://v2.api.wellscan.io/api/";
            //api_prefix = "http://localhost:8000/api/"
            console.log("CalculateRankOfActiveFood(): Ready to calculate rank..."); 
            console.log(`Ranking for ${this.activeFood.category}`);
            console.log(api_prefix+"foods/rankFromNuts/" + this.activeFood.category + "/" + this.activeFood.nutrition.nf_saturated_fat + "/" + this.activeFood.nutrition.nf_sodium + "/" + 0 + "/" + this.activeFood.nutrition.nf_sugars)
            fetch(api_prefix+"foods/rankFromNuts/" + this.activeFood.category + "/" + this.activeFood.nutrition.nf_saturated_fat + "/" + this.activeFood.nutrition.nf_sodium + "/" + this.activeFood.nutrition.nf_added_sugars + "/" + this.activeFood.nutrition.nf_sugars)
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
          category:that.activeFood.category,
          status:that.activeFood.status,
          flagged:that.activeFood.flagged,
          flagText:that.activeFood.flagText,
          rank:that.activeFood.rank
        }, 
        {merge:true}).then(function(){
          that.saveFood();
        });
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
    },

    readyToRank() {
      var ready = this.activeFood.category !== null
        && this.activeFood.nutrition['nf_sodium'].toString().length > 0
        && this.activeFood.nutrition['nf_saturated_fat'].toString().length > 0
        && ((this.activeFood.nutrition['nf_sugars'].toString().length > 0) || (this.activeFood.nutrition['nf_added_sugars'].toString().length > 0))
        && this.activeFood.category !=="baking-supplies-condiments"
        && this.activeFood.rank == "unranked";

        console.log(ready);
        
        return ready;
    }
 

  },

  watch: {
    '$route.hash'(newHash, oldHash) {
      if (newHash === '#single-food') {
        this.foodClicked = true;
      } else if (oldHash === '#single-food') {
        this.foodClicked = false;
      }

      if (newHash === '#open-scanner') {
        this.openScanner = true;
      } else if (newHash !== '#open-scanner') {
        this.openScanner = false;
      }


    },
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

.food-header {
  padding-top:20px;
  background-size:cover;
  
  background-blend-mode: multiply;
}

.food-header.mixed-dish {
    background-image:url('~@/assets/category-backgrounds/mixed-dish.jpg');
}

.food-header.grain {
    background-image:url('~@/assets/category-backgrounds/grain.jpg');
}

.food-header.grain-whole {
    background-image:url('~@/assets/category-backgrounds/grain-whole.jpg');
}

.food-header.processed-packaged-snack {
    background-image:url('~@/assets/category-backgrounds/processed-packaged-snack.jpg');
}


.food-header.protein {
    background-image:url('~@/assets/category-backgrounds/protein.jpg');
}

.food-header.beverage {
    background-image:url('~@/assets/category-backgrounds/beverage.jpg');
}

.food-header.fruit-vegetable {
    background-image:url('~@/assets/category-backgrounds/fruit-vegetable.jpg');
}

.food-header.dairy{
    background-image:url('~@/assets/category-backgrounds/dairy.jpg');
}

.food-header.non-dairy-alternative {
    background-image:url('~@/assets/category-backgrounds/non-dairy-alternative.jpg');
}

.food-header.baking-supplies-condiments {
    background-image:url('~@/assets/category-backgrounds/baking-supplies-condiments.jpg');
}

.food-header.dessert{
    background-image:url('~@/assets/category-backgrounds/dessert.jpg');
}


.food-header .v-card__title {
  word-break: break-word;
  height:2em;
  line-height:1em;
  text-overflow:ellipsis;
  overflow:hidden;
  max-width: 70%;
    color:#fff!important;
}

.food-header .v-card__subtitle {
  color:#fff;
  text-transform: uppercase;
  font-weight: 800;
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
  border-radius:0px;
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

.v-list--three-line .v-list-item, .v-list-item--three-line {
  background-color:#fff;
  margin-bottom:4px;
}

.v-list {
  background:transparent!important;
}

</style>