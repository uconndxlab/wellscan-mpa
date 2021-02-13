<?php include 'inc/layout/header.php'; ?>

<?php if($_GET['src'] == "search"): ?>
    <a href="index.php"> << Back to Search </a>
<?php endif; ?>

<div id="food_display">
    <form id="foodNutritionInfo" method="POST">
        <p><label for="name">Food Name: </label><input id="name" name="name" type="text" value="Loading..."></label></p>

        <p><label for="upc">UPC: </label><input disabled type="text" value="Loading..." id="upc" name="upc"></p>
        <p><label for="saturated_fat">Saturated Fat: </label><input name="saturated_fat" id="saturated_fat" type="text" value="Loading..."></p>
        <p><label for="sodium">Sodium: </label> <input id="sodium" name="sodium" type="text" value="Loading..."></p>
        <p><label for="sugars">Sugars: </label> <input id="sugars" name="sugars" type="text" value="Loading..."></p>

        <div id="all_categories">
            <label for="fruit-veg"><input type="radio" name="category" id="fruit-veg" value="fruit-vegetable">Fruit/Vegetable</label>
            <label for="protein"><input type="radio" name="category" id="protein" value="protein">Protein</label>
            <label for="dairy"><input type="radio" name="category" id="dairy" value="dairy">Dairy</label>
            <label for="nonDairy"><input type="radio" name='category' id="nonDairy" value="non-dairy-alternative">Non-Dairy Alternative</label>
            
            <label for="beverage"><input type="radio" name='category' id="beverage" value="beverage">Beverage</label>
            <label for="mixed-dish"><input type="radio" name='category' id="mixed-dish" value="mixed-dish">Mixed Dish</label>
            <label for="snack-whole-grain" ><input type="radio" name='category' id="snack-whole-grain" value="snack-whole-grain">Whole Grain Snack</label>
            <label for="grain-whole" ><input type="radio" name='category' id="grain-whole" value="grain-whole">Whole Grain</label>
            <label for="grain"><input type="radio" name='category' id="grain" value="grain">Non-Whole Grain</label>


        </div>
        <p><label for="rank">Rank: </label> 
            <select name="rank" id="rank">
                <option value="unranked">--</option>
                <option value="rarely">Rarely</option>
                <option value="sometimes">Sometimes</option>
                <option value="often">Often</option>
            </select>
            <a style="display:none;" class="btn btn-secondary btn-sm" id="calculate_rank" href="javascript:void(0);">Calculate Rank</a>
        </p>
        <p><label for="nutrition_source">Nutrition Source: </label> <input disabled id="nutrition_source" name="nutrition_source" type="text" value="{nutrition_source}" /></p>
         <input type="hidden" name="saveFromForm" value="true">
        <button id="save" type="submit" class="btn btn-block btn-primary">Save Food</button>
    </form>
    <div class="d-none alert" id="alert"></div>
</div>
<script src="food.js"></script>
<script>

//let api_url = "https://v2.api.wellscan.io/api/";

let api_url = "http://localhost:8000/api/"
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
            });
            }
        )
        .catch(function(err) {
            console.log('Fetch Error :-S', err);
        });
}

function catChanged() {
    console.log("Category Changed...");
    document.querySelector("#calculate_rank").style.display="inline";
    document.querySelector("#calculate_rank").addEventListener("click", calcRank);
    document.querySelector("#rank").selectedIndex = 0;
}

function showAlert(msg, cl) {
    var alertDiv = document.querySelector("#alert");
    alertDiv.classList.remove("d-none");
    alertDiv.classList.add(cl);
    alertDiv.innerHTML = msg;
}

function hideAlert() {
    var alertDiv = document.querySelector("#alert");
    alertDiv.classList.add('d-none');
}

function formChanged() {
    showAlert("You have unsaved changes", "alert-info");
    nutrition_source_input.value = firebaseEmail || "manual";
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
                }
                    

                var catRad = document.querySelectorAll("[name='category']");
                
                catRad.forEach(function(item,index) {
                    if (typeof foodObj.rankings.swap !== "undefined") {
                        if (item.id == foodObj.rankings.swap.category){
                            item.checked = true;
                        }
                    }
                
                    item.addEventListener("click", function() {
                        catChanged(); 
                        formChanged();
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

function saveFood() {
    // fetch("actions/updateFood.php?" + upc, 
    //     {
    //         method: 'POST',
    //         body: JSON.stringify({
    //             name: name_input.value,
    //             saturated_fat: satfat_input.value,
    //             sodium: sodium_input.value,
    //             sugars: sugars_input.value,
    //             rank: rank_select.value,
    //             nutrition_source:nutrition_source_input.value
    //         }),
    //         headers: {
    //             'Content-type': 'application/json; charset=UTF-8'
    //         }
    //     }
    // )        
    // .then(
    //     function(response) {
    //     if (response.status !== 200) {
    //         console.log('Looks like there was a problem. Status Code: ' +
    //         response.status);
    //         return;
    //     }
    //     // Examine the text in the response
    //     response.json().then(function(data) {
    //         if (data.status == 200) {
                
    //             console.log("we got there bro");

    //         }
    //     });
    //     }
    // )
    // .catch(function(err) {
    //     console.log('Fetch Error :-S', err);
    // });
}

window.addEventListener("DOMContentLoaded", (e) => {

    getFood();

    nutritionForm.addEventListener("change", function() {
        formChanged();
    });

    nutritionForm.addEventListener("submit", function(e){
        nutrition_source_input.disabled = false;
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
        echo $response;
        if (!$response) 
        {
            echo "no response";
        }
        
?>

    

<?php endif ?>

<?php include 'inc/layout/footer.php';?>