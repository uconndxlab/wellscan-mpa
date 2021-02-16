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
        <h2>Saved Snapshots</h2>
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
                    <a class="nav-link  inbox-toggle" data-status="snapshots" href="inventory-flagged.php">Flagged Items</a>
                </li>

                
            </ul>
            </div>
            <div class="card-body">
                <table id="inventory_table" class="table">
                    <thead>
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Date Saved</th>
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
    .collection("reports");

    listRef.onSnapshot(querySnapshot => {
        document.querySelector("#table_body").innerHTML = "";
        //doc.data() is never undefined for query doc snapshots
        ret = [];
        querySnapshot.forEach(function(doc) {
            var item = doc.data();
            item.DateSaved = item.DateSaved.toDate();
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
            <td><a href="single-report.php?id=${item.id}">${item.name}</a></td>
            <td>${item.DateSaved}</td>
        `;       

        var deleteButton = document.createElement("button");
            deleteButton.classList.add("btn", "btn-sm", "btn-danger");
            deleteButton.innerHTML =  "delete";
            deleteButton.addEventListener("click", function(){
                deleteSnapshot(item);
            });
        

        var deleteTd = document.createElement("td");
            deleteTd.appendChild(deleteButton);
        tr.appendChild(deleteTd);

    document.querySelector("#table_body").appendChild(tr);
}


function deleteSnapshot(item) {
    var db = firebase.firestore();
    var ret = [];
    var listRef = db.collection("organizations")
    .doc(firebaseUserOrg)
    .collection("reports").doc(item.id);

    listRef.delete(item).then(function(querySnapshot) {
        console.log("item deleted");
    });
}


checkAuthThen(function(){
    getItems("active");
})

//attachToggleEvents();

</script>


<?php include 'inc/layout/footer.php'; ?>

