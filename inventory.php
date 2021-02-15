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
            <ul class="nav nav-pills card-header-pills w-100">
                <li class="nav-item">
                    <a class="nav-link active inbox-toggle" data-status="active"  href="inventory.php">Inbox</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link inbox-toggle" data-status="archived" href="inventory-archive.php">Archived</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link inbox-toggle" data-status="snapshots" href="all-reports.php">Saved Snapshots</a>
                </li>

                <li class="nav-item ml-auto">
                    <a id="save_snapshot_button" href="#" data-toggle="modal" data-target="#save_snapshot_modal"  class="btn btn-block btn-secondary">Save Snapshot</a>
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

<div id="save_snapshot_modal" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    <form id="save_snapshot_form" action="">
      <div class="modal-header">
        <h5 class="modal-title">Save a Snapshot (<span id="items_count"></span> items)</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      
      <div class="modal-body">
        
        <div class="form-group">
            <input id="snapshot_name" type="text" class="form-control" required placeholder="snapshot name">
        </div>

        <div class="form-group">
            <h6>Custom Fields</h6>
            <table class='table'>
                <thead>
                    <th>Field Name</th>
                    <th>Field Value</th>
                </thead>
                <tbody id="snapshot_custom_fields">
                    <tr><td><input class="form-control snapshot_custom_field_name" type="text"></td><td><input class="form-control snapshot_custom_field_value" type="text"></td></tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" style="text-align: left;">
                            <a href="#" class="btn btn-lg btn-block btn-secondary" id="addRow">Add Field</a>
                        </td>
                    </tr>
                    <tr>
                    </tr>
                </tfoot>
            </table>
        </div>

      </div>
      <div class="modal-footer">
        <button id="submit_snapshot" type="submit" class="btn btn-primary">Save Snapshot</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
      </div>
      
    </div>
    </form>
  </div>
</div>



<script>
 var items = [];
function getItems(status) {
    console.log(firebaseUserOrg);
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
        
        
        
        
        $("#inventory_table").DataTable();

        document.querySelector("#save_snapshot_button").addEventListener("click", function() {
            document.querySelector("#items_count").innerHTML = items.length;
        });
       
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

        var archiveButton = document.createElement('button');
            archiveButton.classList.add("btn", "btn-sm", "btn-secondary");
            archiveButton.innerHTML = `<i class="bi bi-archive"></i> archive`
            archiveButton.addEventListener("click", function () {
                archiveItem(item);
            });

        var buttonTD = document.createElement("td");
            buttonTD.appendChild(archiveButton);

        tr.appendChild(buttonTD);
        

    document.querySelector("#table_body").appendChild(tr);
}


function archiveItem(item){
    item.status = "archived";
    var db = firebase.firestore();
        var ret = [];
        var listRef = db.collection("organizations")
        .doc(firebaseUserOrg)
        .collection("inventory").doc(item.id);

        listRef.set(item).then(function(querySnapshot) {
            console.log("item updated");
        });
}

checkAuthThen(function(){
    getItems("active");
});

//attachToggleEvents();

document.querySelector("#addRow").addEventListener("click", function () {
    var tbody = document.querySelector("#snapshot_custom_fields");
    var tr = document.createElement("tr");
        var cols = `
            <td><input class="form-control snapshot_custom_field_name" type="text"></td><td><input class="form-control snapshot_custom_field_value" type="text"></td>
        `;
    tr.innerHTML = cols;

    tbody.appendChild(tr);
});

document.querySelector("#save_snapshot_form").addEventListener("submit", function(e) {
    
    e.preventDefault();

    

    var meta = {
        savedBy:firebaseEmail
    };
    var snapshotName = document.querySelector("#snapshot_name").value;

    var allFieldNames = document.querySelectorAll(".snapshot_custom_field_name");
    var allFieldValues = document.querySelectorAll(".snapshot_custom_field_value");

    for (var i=0; i<allFieldNames.length; i++) {

        let n = allFieldNames[i].value;
        let v = allFieldValues[i].value;

        if(n !== '')
            meta[n] = v;
    }

    var snapshot = {
        name:snapshotName,
        DateSaved: new Date(),
        meta:meta,
        items:items
    }


    var db = firebase.firestore();

    var docRef = db.collection("organizations")
    .doc(firebaseUserOrg)
    .collection("reports")
    .doc().set(snapshot).then(() => {
    console.log("Document successfully updated!");
})
.catch((error) => {
    // The document probably doesn't exist.
    console.error("Error updating document: ", error);
});;


    
    
    
    
});

</script>


<?php include 'inc/layout/footer.php'; ?>

