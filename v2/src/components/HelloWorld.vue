<template>

  <v-container>
    
    <v-list three-line>
      <template v-for="(item, index) in items">
        <v-list-item
          :key="item.name"
          @click="loadSingleFood(index)"
        >
          <v-list-item-avatar :class="item.rank"></v-list-item-avatar>

          <v-list-item-content>
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
        <v-divider :key="index" inset></v-divider>
      </template>
    </v-list>

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

          <StreamBarcodeReader v-if="openScanner"
          @decode="onBarcodeDecode"
          @loaded="onCameraLoaded"
          style="margin-bottom:25px"
          >      
          </StreamBarcodeReader>
          <v-text-field
            class="ma-6"
            outlined
            label="UPC"
          ></v-text-field> 
        </div>

          <v-btn
            class="mb-6"
            text
            @click="openScanner = !openScanner"
          >
            close
          </v-btn>
      </v-sheet>
    </v-bottom-sheet>

    <v-dialog fullscreen transition="dialog-bottom-transition" v-model="foodClicked">
          <v-card tile>

              <v-toolbar
              :color="activeFood.rank"
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
                  @click="foodClicked = !foodClicked"
                >
                  <v-icon>mdi-check</v-icon>
                </v-btn>
              </v-toolbar>
              <div
                :class="activeFood.rank"
                height="135px"
              >
                
                <v-card-subtitle class="text-left pb-0">{{activeFood.rank}}</v-card-subtitle>
                <v-card-title v-model="activeFood.name" class="text-left pt-0">{{activeFood.name}}</v-card-title>
                <v-card-subtitle class="text-left">{{activeFood.upc}}</v-card-subtitle>

              </div>
              <v-card-text style="position:relative; padding-top:20px;" class="text-left">

              <v-chip
                class="ma-2 rounded-0"
                :input-value="activeFood.flagged"
                @click="activeFood.flagged = !activeFood.flagged"
                style="position:absolute; right:0px; top:-25px; z-index:999"
                color="secondary"
                filter
                filter-icon="mdi-flag"
                
              >
                Flag <span v-if="activeFood.flagged" style="margin-right:6px;">ged</span>for Review
              </v-chip>



                <v-chip-group
                  active-class="primary--text"
                  v-model="activeFood.category"
                  column
                >
                  <v-chip
                    v-for="tag in tags"
                    :key="tag.abbr"
                    :value="tag.abbr"
                    
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
                ></v-text-field>        
                <v-text-field
                  dense
                  v-model="activeFood.nutrition.nf_sodium"
                  label="Sodium"
                  outlined
                ></v-text-field>

                <v-text-field
                  v-model="activeFood.nutrition.nf_sugars"
                  dense
                  label="Sugars"
                  outlined
                ></v-text-field>

                <v-text-field
                  v-model="activeFood.nutrition.nf_saturated_fat"
                  dense
                  outlined
                  label="Saturated Fat"
                  
                ></v-text-field>
                </div>
              </v-card-text>
            </v-card>
    </v-dialog>
  </v-container>
  
</template>

<script>
import { StreamBarcodeReader } from "vue-barcode-reader";
  export default {
    name: 'HelloWorld',
    components: {
        StreamBarcodeReader,
      },
    data: () => ({
      openScanner:false,
      foodClicked:false,
      activeFood: {          
          name: 'Not Found',
          upc: "",
          rank:"",
          category:"",
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
     items: [
        {
          name: 'This is a very long food name and I wonder if it will break the UI or if it will be okay I guess we will see',
          flagged:false,
          upc: "042563016272",
          rank:"rarely",
          category:"fruit-vegetable",
          nutrition: {
            nf_sodium:0,
            nf_saturated_fat:40,
            nf_sugars:40
          }
        },
        {
          name: 'Runa Amazon Guayusa Tea, Unsweetned Guava',
          flagged:false,
          upc: "042563016272",
          rank:"often",
          category:"fruit-vegetable",
          nutrition: {
            nf_sodium:0,
            nf_saturated_fat:40,
            nf_sugars:40
          }
        },
        {
          name: 'Almost Breeze Almont Milk, Unsweetened Vanilla',
          flagged:false,
          upc: "042563016272",
          rank:"unranked",
          category:"non-dairy-alternative",
          nutrition: {
            nf_sodium:0,
            nf_saturated_fat:40,
            nf_sugars:40
          }
        },
        {
          name: 'Izze Sparkling Clementine',
          flagged:false,
          upc: "042563016272",
          rank:"rarely",
          category:"beverage",
          nutrition: {
            nf_sodium:0,
            nf_saturated_fat:40,
            nf_sugars:40
          }
        },
        {
          name: "Bell's Traditional Stuffing",
          flagged:false,
          upc: "042563016272",
          rank:"sometimes",
          category:"mixed-dish",
          nutrition: {
            nf_sodium:0,
            nf_saturated_fat:40,
            nf_sugars:40
          }
        },
        {
          name: 'Iberia Arroz Amarillo Yellow Rice',
          flagged:false,
          upc: "042563016272",
          rank:"rarely",
          category:"grain",
          nutrition: {
            nf_sodium:0,
            nf_saturated_fat:40,
            nf_sugars:40
          }
        },
        {
          name: 'Unknown',
          flagged:false,
          upc: "042563016272",
          rank:"unranked",
          category:"fruit-vegetable",
          nutrition: {
            nf_sodium:0,
            nf_saturated_fat:0,
            nf_sugars:0
          }
        },                     
      ],
    }),

    methods: {
      removeItem(index) {
        this.items.splice(index,1);
      },
 
      loadSingleFood(index) {
        this.foodClicked = !this.foodClicked;
        this.activeFood = this.items[index];
      },

      flagFood() {
        this.activeFood.flagged = !this.activeFood.flagged;
      }
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

.v-card__title + .v-card__subtitle {
  margin-top:10px;
}



.v-toolbar--flat {
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



</style>