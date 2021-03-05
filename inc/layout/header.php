<?php 
  $api_url = "https://v2.api.wellscan.io/api/"
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Welcome to WellSCAN</title>
<!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous"> -->
<link rel="stylesheet" href="bootstrap.min.css">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">

<!-- The core Firebase JS SDK is always required and must be listed first -->
<script src="https://www.gstatic.com/firebasejs/8.2.7/firebase-app.js"></script>

<!-- Add Firebase products that you want to use -->
<script src="https://www.gstatic.com/firebasejs/8.2.7/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.2.7/firebase-firestore.js"></script>


<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

<script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/@ericblade/quagga2@1.2.6/dist/quagga.js"></script>

<script>
  
  // Your web app's Firebase configuration
  var firebaseConfig = {
    apiKey: "AIzaSyCeZsQqloriPNrPUaVsEYtvAGmgdYPiA1Q",
    authDomain: "wellscan.firebaseapp.com",
    databaseURL: "https://wellscan.firebaseio.com",
    projectId: "wellscan",
    storageBucket: "wellscan.appspot.com",
    messagingSenderId: "477728865423",
    appId: "1:477728865423:web:40f0f068ad41181c7c44fa"
  };
  // Initialize Firebase
  firebase.initializeApp(firebaseConfig);
  var firebaseUserName;
  var firebaseEmail;
  var firebaseUserOrg;
</script>



<script>
<?php if (basename($_SERVER['PHP_SELF']) !== "login.php"): ?>

  async function checkAuthThen(callback) {
      firebase.auth().onAuthStateChanged(firebaseUser => {
          if(firebaseUser){
              console.log("User is logged in as " + firebaseUser.displayName + ` (${firebaseUser.email})`);
              firebaseUserName = firebaseUser.displayName;
              firebaseEmail = firebaseUser.email;

              var db = firebase.firestore();

              var userRef = db.collection("users")
              .doc(firebaseUser.email);

              userRef.get().then((doc) => {
              if (doc.exists) {
                  var info = doc.data();
                  var org = info.organization;
                  console.log("User is associated with: " + org)  
                  firebaseUserOrg = org;

                  var authItems = document.querySelectorAll(".auth-only")
                  authItems.forEach(function(item, i) {
                    item.classList.remove("d-none");
                  });

                  document.querySelector("#showUserEmail").innerHTML = `${firebaseEmail} (${org})`;
                  
                  callback();

              } else {
                  // doc.data() will be undefined in this case
                  console.log("No such document!");
                  }
              }).catch((error) => {
                  console.log("Error getting document:", error);
              });

          } else{
              window.location = "login.php";
          }
      });
  }    

  <?php else: ?>

      firebase.auth().onAuthStateChanged(firebaseUser => {
          if(firebaseUser){
              console.log("User is logged in as " + firebaseUser.displayName + ` (${firebaseUser.email})`);
              window.location = "index.php"
          } else{
              // stay here
              
          }
      });
 

      
      <?php endif; ?>
</script>


<?php if (basename($_SERVER['PHP_SELF']) == "login.php"): ?>
    <script src="https://www.gstatic.com/firebasejs/ui/4.6.1/firebase-ui-auth.js"></script>
    <link type="text/css" rel="stylesheet" href="https://www.gstatic.com/firebasejs/ui/4.6.1/firebase-ui-auth.css" />
<?php endif; ?>

<style>

html {
  overflow-y:scroll;
}

body,html{
    min-height:100%;
}

body {
  padding-bottom:100px;
}

video {
  width:100%;
}

.sometimes, .yellow {
    background-color: #ffc107 !important;
    color:#333;
}

.often, .green {
    background-color: #28a745 !important;
    color:#fff;
}

.rarely, .red {
    background-color: #dc3545 !important;
    color:white;
}

.unranked {
    background-color: #ccc; !important;
}



.drawingBuffer {
  display:none;
}

[role="main"] {
  padding-top: 40px; /* Space for fixed navbar */
}

th {
  cursor:pointer;
}

.table td, .table th {
  vertical-align:middle;
}

</style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg sticky-top navbar-dark bg-dark">
  <a class="navbar-brand" href="index.php">WellSCAN</a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation" >
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarText">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item">
        <a class="nav-link" href="index.php">Search<span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item d-none d-md-block">
        <a class="nav-link" href="inventory.php">Inventory</a>
      </li>
    </ul>
    <span style="font-size:0.75em;" class="auth-only navbar-text d-none">
      Logged in as <span id="showUserEmail"></span> <a href="logout.php">Log Out</a>
    </span>
  </div>
</nav>
<main role="main" class="wrap-all container">
<div class="row"><div class="col-md-12 text-right"><a href="bug-report.php" data-toggle="modal" data-target="#bugReportModal" class="ml-auto btn-sm btn-info"><i class="bi bi-bug"></i> Report a Bug</a></div></div>