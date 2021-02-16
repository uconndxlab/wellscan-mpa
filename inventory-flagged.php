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
   
        <h2>Flagged Items</h2>
        <div class="card">
            <div class="card-header">
            <ul class="nav nav-pills card-header-pills w-100">
                <li class="nav-item">
                    <a class="nav-link inbox-toggle" data-status="active"  href="inventory.php">Inbox</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link inbox-toggle" data-status="archived" href="inventory-archive.php">Archived</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link inbox-toggle" data-status="snapshots" href="all-reports.php">Saved Snapshots</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active inbox-toggle" data-status="snapshots" href="inventory-flagged.php">Flagged Items</a>
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
                            <th scope="col">Flagged By</th>
                            <th scope="actions">Remove Flag</th>
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
      
<script>
 var items = [];
function getItems(status) {
    console.log(firebaseUserOrg);
    var that = this;
    var db = firebase.firestore();
    var ret = [];
    var listRef = db.collection("organizations")
    .doc(firebaseUserOrg)
    .collection("flagged").orderBy("date_scanned");

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


       
    })
   
}


function generateTableRow(item){
    var tr = document.createElement("tr");
        tr.innerHTML = `

            <td><a href="food.php?upc=${item.upc}">${item.upc}</a></td>
            <td>${item.name}</td>
            <td class='${item.rank}'>${item.rank}</td>
            <td>${item.scanned_by}</td>

        `;

        var archiveButton = document.createElement('button');
            archiveButton.classList.add("btn", "btn-sm", "btn-secondary");
            archiveButton.innerHTML = 'remove flag'
            archiveButton.addEventListener("click", function () {
                removeFlag(item);
            });

        var viewLink = document.createElement("a");
            viewLink.classList.add("btn", "btn-sm", "btn-primary" )
            viewLink.href="food.php?upc="+ item.upc;
            viewLink.innerHTML = 'view';

        var buttonTD = document.createElement("td");
            buttonTD.appendChild(archiveButton);
            //buttonTD.appendChild(viewLink);

        tr.appendChild(buttonTD);



    document.querySelector("#table_body").appendChild(tr);
}


function removeFlag(item){

    var db = firebase.firestore();
    var ret = [];
    var listRef = db.collection("organizations")
    .doc(firebaseUserOrg)
    .collection("flagged").doc(item.id);

    listRef.delete(item).then(function(querySnapshot) {
        console.log("item deleted");
    });
}

checkAuthThen(function(){
    getItems("active");
});

//attachToggleEvents();
</script>


<?php include 'inc/layout/footer.php'; ?>

