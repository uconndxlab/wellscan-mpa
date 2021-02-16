<?php include 'inc/layout/header.php'; ?>

<style>
.dataTables_filter {
    float:right;
}

#inventory_table_length  {
    float:left;
}

</style>

<div class="row">
    <div class="col-md-12">
        <h2>Inventory</h2>
        <div class="card">
            <div class="card-header">
            <ul class="nav nav-pills card-header-pills">
                <li class="nav-item">
                    <a class="nav-link inbox-toggle" data-status="active"  href="inventory.php">Inbox</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active inbox-toggle" data-status="archived" href="inventory-archive.php">Archived</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link inbox-toggle" data-status="snapshots" href="all-reports.php">Saved Snapshots</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link inbox-toggle" data-status="snapshots" href="inventory-flagged.php">Flagged Items</a>
                </li>
            </ul>
            </div>
            <div class="card-body">
                <table id="inventory_table" class="table">
                    <thead>
                        <tr>
                            <th scope="col">UPC</th>
                            <th scope="col">Name</th>
                            <th scope="col">Rank</th>
                            <th scope="col">BlameID</th>
                            <th scope="actions">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="table_body">
 
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>



<script>

function getItems(status) {
    
    var items = [];
    var that = this;
    var db = firebase.firestore();
    var ret = [];
    var listRef = db.collection("organizations")
    .doc(firebaseUserOrg)
    .collection("inventory").where("status","==", status);

    listRef.onSnapshot(querySnapshot => {
        document.querySelector("#table_body").innerHTML = "";
        //doc.data() is never undefined for query doc snapshots
        ret = [];
        querySnapshot.forEach(function(doc) {
            var item = doc.data();
            item.date_scanned = item.date_scanned.toDate();
            item.id = doc.id;
            ret.push(item);
            generateTableRow(item);
        });
        items = ret;
        console.log(items);
        
        
        $("#inventory_table").DataTable();
    })
   
}


function generateTableRow(item){
    var tr = document.createElement("tr");
        tr.innerHTML = `

            <td>${item.upc}</td>
            <td>${item.name}</td>
            <td class='${item.rank}'>${item.rank}</td>
            <td>${item.scanned_by}</td>

        `;

        var deleteButton = document.createElement("button");
            deleteButton.classList.add("btn", "btn-sm", "btn-danger");
            deleteButton.innerHTML =  "delete";
            deleteButton.addEventListener("click", function(){
                deleteItemFromInventory(item);
            });
        

        var deleteTd = document.createElement("td");
            deleteTd.appendChild(deleteButton);
        tr.appendChild(deleteTd);
        

    document.querySelector("#table_body").appendChild(tr);
}

function deleteItemFromInventory(item) {
    var db = firebase.firestore();
    var ret = [];
    var listRef = db.collection("organizations")
    .doc(firebaseUserOrg)
    .collection("inventory").doc(item.id);

    listRef.delete(item).then(function(querySnapshot) {
        console.log("item deleted");
    });
}




checkAuthThen(function(){
    getItems("archived");
})

//attachToggleEvents();

</script>


<?php include 'inc/layout/footer.php'; ?>

