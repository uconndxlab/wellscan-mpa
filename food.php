<?php include 'inc/layout/header.php'; ?>


<style>
  div.radio {
      display:inline-block;
  }
  input[type="radio"]{
    display: none;
  }

 .radio input + label {
    box-shadow: 2px 2px 12px #44444414;

    display: inline-block;
    border: 1px solid #24474c;
    padding: 3px 10px;
    border-radius: 36px;
    position: relative;
    cursor: pointer;
    transition: .1s ease-in-out;
}

   
.radio input:checked + label{
    border-color: #24474c;
    background: #30595F;
    color: #fff;
  }

</style>
 
    <form id="foodNutritionInfo" method="POST" autcomplete="off" class="row h-100 justify-content-center">
        
        <div class="col-md-6">
        
            
            <div class="form-group">
                <h5>Food Information</h5>
                <div class="form-group"><label for="name">Food Name: </label><input class="form-control" id="name" name="name" type="text" placeholder="Not found."></label></div>

                <label for="rank">Rank: </label> 
                <select class="form-control" disabled name="rank" id="rank">
                    <option value="unranked">--</option>
                    <option value="rarely">Rarely</option>
                    <option value="sometimes">Sometimes</option>
                    <option value="often">Often</option>
                </select>
            </div>
            <div class="form-group">
                <h5>Inventory Options</h5>
                <a href="#" id="inventory-add" class="btn btn-block btn-outline-info"><i class="bi bi-journal-plus"></i> Send to Inbox</a>
                <div class="d-none alert" id="inventoryAlert"></div>
            </div>     
        </div>
        <div class="col-md-6">


            <div class="col-12"><a class="d-none btn btn-secondary btn-block" id="calculate_rank" href="javascript:void(0);">Calculate Rank</a></div>

            <h5>Ranking Information</h5>
            <div class="form-group" id="all_categories">
                <div class="radio"><input type="radio" name="category" id="fruit-vegetable" value="fruit-vegetable"><label for="fruit-vegetable">Fruit/Vegetable</label></div>
                <div class="radio"><input type="radio" name="category" id="protein" value="protein"><label for="protein">Protein</label></div>
                <div class="radio"><input type="radio" name="category" id="dairy" value="dairy"><label for="dairy">Dairy</label></div>
                <div class="radio"><input type="radio" name='category' id="nonDairy" value="non-dairy-alternative"><label for="nonDairy">Non-Dairy Alternative</label></div>
                
                <div class="radio"><input type="radio" name='category' id="beverage" value="beverage"><label for="beverage">Beverage</label></div>
                <div class="radio"><input type="radio" name='category' id="mixed-dish" value="mixed-dish"><label for="mixed-dish">Mixed Dish</label></div>
                <div class="radio"><input type="radio" name='category' id="snack-whole-grain" value="snack-whole-grain"><label for="snack-whole-grain" >Whole Grain Snack</label></div>
                <div class="radio"><input type="radio" name='category' id="grain-whole" value="grain-whole"><label for="grain-whole" >Whole Grain</label></div>
                <div class="radio"><input type="radio" name='category' id="grain" value="grain"><label for="grain">Non-Whole Grain</label></div>
            </div>

            <h5>Nutrient Info</h5>
            <div class="form-group"><label for="upc">UPC: </label><input class="form-control" disabled type="text" value="<?php echo $_GET['upc']; ?>" id="upc" name="upc"></p></div>
            <div class="form-group"><label for="saturated_fat">Saturated Fat: </label><input class="form-control" name="saturated_fat" id="saturated_fat" type="text" placeholder="Not found."></div>
            <div class="form-group"><label for="sodium">Sodium: </label> <input class="form-control" id="sodium" name="sodium" type="text" placeholder="Not found."></div>
            <div class="form-group"><label for="sugars">Sugars: </label> <input class="form-control" id="sugars" name="sugars" type="text" placeholder="Not found."></div>

            <div class="form-group"><label for="nutrition_source">Nutrition Source: </label> <input disabled id="nutrition_source" class="form-control" name="nutrition_source" type="text" placeholder="Not found." /></div>


            <div class="col-12">
                <div class="d-none alert" id="catAlert"></div>
            </div>


            <div class="form-group">
                <button id="save" type="submit" class="d-none btn btn-block btn-primary"><i class="bi bi-file-arrow-up"></i> Save Changes to WellSCAN Global</button>
                <div class="d-none alert" id="saveAlert"></div>
            </div>
        
            </div>

        </div>

         <input type="hidden" name="saveFromForm" value="true">
     

    </form>

<script src="food.js"></script>
<script>

let api_url = "https://v2.api.wellscan.io/api/";
//let api_url = "http://localhost:8000/api/"

let lookup_endpoint = "foods/lookup/";
let calcRankNuts = "foods/rankFromNuts";
let upc = "<?php echo $_GET['upc']; ?>";
var nutritionForm = document.querySelector("#foodNutritionInfo");

var name_input = document.querySelector("#name");
var upc_input = document.querySelector("#upc");
var satfat_input = document.querySelector("#saturated_fat");
var sodium_input = document.querySelector("#sodium");
var sugars_input = document.querySelector("#sugars");
var rank_select = document.querySelector("#rank");
var nutrition_source_input = document.querySelector("#nutrition_source");
var save_button = document.querySelector("#save");


function calcRank() {
    console.log("Ready to calculate rank...");
    var category  = Array.from(document.getElementsByName("category")).find(r => r.checked).value;
    var satfat = document.querySelector("#saturated_fat").value;
    var sodium = document.querySelector("#sodium").value;
    var sugars = document.querySelector("#sugars").value;
    var foodObj = {};
    //showCatChangeAlert("Recalculating Ranking...", "alert-info");
    fetch(api_url + calcRankNuts + "/" + category + "/" + satfat + "/" + sodium + "/" + 0 + "/" + sugars)
    .then(
            function(response) {
            if (response.status !== 200) {
                console.log('Looks like there was a problem. Status Code: ' +
                response.status);
                return;
            }

            // Examine the text in the response
            response.json().then(function(data) {
                switch(data.rank) {
                    case "rarely":
                        document.querySelector("#rank").selectedIndex = 1;
                    break;

                    case "sometimes":
                        document.querySelector("#rank").selectedIndex = 2;
                    break;

                    case "often":
                        document.querySelector("#rank").selectedIndex = 3;
                    break;

                    default:
                        document.querySelector("#rank").selectedIndex = 0;
                    break;

   
                }

                rank_select.classList.remove("rarely");
                rank_select.classList.remove("sometimes");
                rank_select.classList.remove("often");
                rank_select.classList.remove("unranked");
                rank_select.classList.add(data.rank);

                hideCatAlert();
            });
            }
        )
        .catch(function(err) {
            console.log('Fetch Error :-S', err);
        });
}

function displayCalcRankButton() {
    document.querySelector("#calculate_rank").classList.remove("d-none");
    document.querySelector("#calculate_rank").addEventListener("click", calcRank);
    document.querySelector("#rank").selectedIndex = 0;
}

function hideCalcRankButton() {
    document.querySelector("#calculate_rank").classList.add("d-none");
}

function catChanged() {
    console.log("Category Changed...");
    showCatChangeAlert("Re-calculate the ranking.", "alert-warning");
    //displayCalcRankButton();
}

function showCatChangeAlert(msg, cl) {
    var alertDiv = document.querySelector("#catAlert");
    alertDiv.classList.remove("d-none");
    alertDiv.classList.add(cl);
    alertDiv.innerHTML = msg;
}

function hideCatAlert() {
    var alertDiv = document.querySelector("#catAlert");
    alertDiv.classList.add('d-none');
}

function showFormAlert(msg, cl) {
    var alertDiv = document.querySelector("#saveAlert");
    alertDiv.classList.remove("d-none");
    alertDiv.classList.add(cl);
    alertDiv.innerHTML = msg;
}

function hideFormAlert() {
    var alertDiv = document.querySelector("#saveAlert");
    alertDiv.classList.add('d-none');
}

function showInventoryAlert(msg, cl) {
    var alertDiv = document.querySelector("#inventoryAlert");
    alertDiv.classList.remove("d-none");
    alertDiv.classList.add(cl);
    alertDiv.innerHTML = msg;
}

function hideFormAlert() {
    var alertDiv = document.querySelector("#inventoryAlert");
    alertDiv.classList.add('d-none');
}

function formChanged() {
    save_button.classList.remove('d-none');
    showFormAlert("You have unsaved changes.", "alert-warning");
}

function addFoodToInventory() {
    var db = firebase.firestore();
   
    db.collection("organizations")
    .doc("uconn")
    .collection("inventory")
        .add({
            date_scanned: new Date(),
            name:name_input.value,
            status:"active",
            rank:rank.rank,
            upc: upc,
            scanned_by:firebaseEmail
        }
    )
    .then(function(docRef) {
        showInventoryAlert("Added to inventory.", "alert-success");
        console.log("Your item has been saved with ID " + docRef.id);
    })
    .catch(function(error) {
        console.error("Error adding document: ", error);
   });
}

function getFood() {
    fetch(api_url + lookup_endpoint + upc)        
    .then(
        function(response) {
        if (response.status !== 200) {
            console.log('Looks like there was a problem. Status Code: ' +
            response.status);
            return;
        }
        // Examine the text in the response
        response.json().then(function(data) {
            if (data.status == 200) {
                // we found food info
                foodObj = new Food(data.upc, data.name, data.rankings, data.nutrition, data.nutrition_method, data.nutrition_source, data.rankings);
                
                name_input.value = data.name;
                upc_input.value = data.upc;
                satfat_input.value = data.nutrition.nf_saturated_fat || 0;
                sodium_input.value = data.nutrition.nf_sodium || 0;
                sugars_input.value = data.nutrition.nf_sugars || 0;
                nutrition_source_input.value = data.nutrition_source;

                rank_select.selectedIndex = 0;
                if (typeof data.rankings.swap !== "undefined") {
                    console.log ("we have a rank");
                    switch(data.rankings.swap.rank) {
                        case "rarely":
                            rank = {value:1, rank: "rarely"}
                        break;
            
                        case "sometimes":
                            rank = {value:2, rank: "sometimes"}
                        break;
            
                        case "often":
                            rank = {value:3, rank:"often"}
                        break;
                    }

                    rank_select.selectedIndex = rank.value;
                    rank_select.classList.add(rank.rank);
                }
                    

                var catRad = document.querySelectorAll("[name='category']");
                
                catRad.forEach(function(item,index) {
                    if (typeof foodObj.rankings.swap !== "undefined") {
                        if (item.id == foodObj.rankings.swap.category){
                            item.checked = true;
                        }
                    }
                
                    item.addEventListener("click", function() {
                        // catChanged(); 
                        calcRank();
                       
                    });
                })

            }
        });
        }
    )
    .catch(function(err) {
        console.log('Fetch Error :-S', err);
    });
}

window.addEventListener("DOMContentLoaded", (e) => {

    getFood();

    nutritionForm.addEventListener("change", function() {
        formChanged();
    });

    nutritionForm.addEventListener("submit", function(e){
        nutrition_source_input.disabled = false;
        rank_select.disabled = false;
    });
    
    satfat_input.addEventListener("change" ,function() {
        //displayCalcRankButton();
        nutrition_source_input.value = firebaseEmail;
        calcRank();
    });

    sodium.addEventListener("change" ,function() {
        nutrition_source_input.value = firebaseEmail;
        calcRank();
    });

    sugars_input.addEventListener("change" ,function() {
        nutrition_source_input.value = firebaseEmail;
        calcRank();
    });

    document.querySelector("#inventory-add").addEventListener("click", function(e){
        e.preventDefault();
        addFoodToInventory();
    });
});
</script>


<?php 

    if(isset($_POST['saveFromForm'])):
        $data = array();
        $data['upc'] = $_GET['upc'];
        $data['name'] = $_POST['name'];
        $data['nf_saturated_fat'] = $_POST['saturated_fat'];
        $data['sodium'] = $_POST['sodium'];
        $data['sugars'] = $_POST['sugars'];
        $data['category'] = $_POST['category'];
        $data['rank'] = $_POST['rank'];
        $data["nutrition_source"] =  $_POST['nutrition_source'];
        $data["nutrition_method"] = "manual";

        
        $ch = curl_init($api_url . "foods/" . $_GET['upc']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));
        
        $response = curl_exec($ch);

        
        
        if (!$response) 
        {
            echo "no response";
        }
        
?>

<script>
    showFormAlert("Food updated", "alert-success");
</script>

<?php endif ?>

<?php include 'inc/layout/footer.php';?>