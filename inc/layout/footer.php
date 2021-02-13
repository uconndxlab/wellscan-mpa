

</main>
<script>
<?php if (basename($_SERVER['PHP_SELF']) !== "login.php"): ?>
    firebase.auth().onAuthStateChanged(firebaseUser => {
        if(firebaseUser){
            console.log("User is logged in as " + firebaseUser.displayName + ` (${firebaseUser.email})`);
            firebaseUserName = firebaseUser.displayName;
            firebaseEmail = firebaseUser.email;
        } else{
            window.location = "login.php";
        }
    });

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
</body>
</html>