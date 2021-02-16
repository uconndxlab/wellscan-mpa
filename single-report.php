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
                    <a class="nav-link inbox-toggle" data-status="archived" href="inventory-archive.php">Archived</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active inbox-toggle" data-status="snapshots" href="all-reports.php">Saved Snapshots</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link inbox-toggle" data-status="snapshots" href="inventory-flagged.php">Flagged Items</a>
                </li>

                <li class="nav-item ml-auto">
                    <a href="#" class="btn btn-block btn-secondary">Export CSV</a>
                </li>
            </ul>
            </div>
            <div class="card-body">
                <h3 id="report_name">Report Name</h3>
                <p>Date Saved: <strong id="report_date">Report Date</strong></p>
                <div id="report_meta">

                </div>
                <div style="margin-top:50px;">
                    <h4>Items</h4>
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

</div>



<script>
var items = [];
function getItems() {
    

    var that = this;
    var db = firebase.firestore();
    var ret = [];
    var docRef = db.collection("organizations")
    .doc(firebaseUserOrg)
    .collection("reports")
    .doc('<?php echo $_GET['id'];?>');

    docRef.get().then((doc) => {
    if (doc.exists) {
        var info = doc.data();
        var meta = info.meta;
        
        document.querySelector("#report_name").innerHTML = info.name;
        document.querySelector("#report_date").innerHTML = info.DateSaved.toDate();

        populateMeta(meta);
        populateItems(info.items);
        
    } else {
        // doc.data() will be undefined in this case
        console.log("No such document!");
        }
    }).catch((error) => {
        console.log("Error getting document:", error);
    });
   
}

function populateMeta(meta) {
    var container = document.querySelector("#report_meta");

    var meta_keys = Object.keys(meta);

    for (var i = 0; i<meta_keys.length; i++) {
        var v = meta_keys[i];
        container.innerHTML += `
            <p><strong>${v}:</strong> ${meta[v]} </strong> </p>
        `;
    }
}

function populateItems(items) {
    document.querySelector("#table_body").innerHTML = "";
    if (typeof items !== "undefined") {
        for (var i = 0; i<items.length; i++) {
            var item = items[i];
            generateTableRow(item);
        }
    }
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
            deleteButton.innerHTML =  "remove from snapshot";
            deleteButton.addEventListener("click", function(){
                deleteItem(item);
            });
        

        var deleteTd = document.createElement("td");
            deleteTd.appendChild(deleteButton);
        tr.appendChild(deleteTd);
        

    document.querySelector("#table_body").appendChild(tr);
}

function deleteItem(item) {
   console.log(item);
   var db = firebase.firestore();
   var docRef = db.collection("organizations")
    .doc(firebaseUserOrg)
    .collection("reports")
    .doc('<?php echo $_GET['id'];?>')
    .update(
        {
            "items" : firebase.firestore.FieldValue.arrayRemove(item) 
        }
    ).then(function(querySnapshot) {
        populateItems();
        console.log("item deleted");
    });
}



checkAuthThen(function(){
    getItems();
});

//attachToggleEvents();

</script>


<?php include 'inc/layout/footer.php'; ?>

