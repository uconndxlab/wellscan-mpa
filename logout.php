<?php include 'inc/layout/header.php'; ?>

<script>

firebase.auth().signOut().then(() => {
  window.location = "login.php";
}).catch((error) => {
  // An error happened.
});

</script>

<?php include 'inc/layout/footer.php'; ?>