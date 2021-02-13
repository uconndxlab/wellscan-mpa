<?php 
  $api_url = "http://localhost:8000/api/"
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Welcome to WellSCAN</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">

<!-- The core Firebase JS SDK is always required and must be listed first -->
<script src="https://www.gstatic.com/firebasejs/8.2.7/firebase-app.js"></script>

<!-- Add Firebase products that you want to use -->
<script src="https://www.gstatic.com/firebasejs/8.2.7/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.2.7/firebase-firestore.js"></script>


<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

<script src="quagga.js"></script>

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
</script>

<?php if (basename($_SERVER['PHP_SELF']) == "login.php"): ?>
    <script src="https://www.gstatic.com/firebasejs/ui/4.6.1/firebase-ui-auth.js"></script>
    <link type="text/css" rel="stylesheet" href="https://www.gstatic.com/firebasejs/ui/4.6.1/firebase-ui-auth.css" />
<?php endif; ?>

<style>
body,html{
    height:100%;
}

video {
  width:100%;
}

.sometimes {
    background-color: yellow !important;
}

.often {
    background-color: green !important;
}

.rarely {
    background-color: red !important;
    color:white;
}

.unranked {
    background-color: #ccc; !important;
}

</style>
</head>
<body class="bg-light">
<main role="main" class="container h-100">