<?php include 'inc/layout/header.php'; ?>

<h1>Log In to WellSCAN</h1>
<div id="firebaseui-auth-container"></div>
<div id="loader">Loading...</div>





<script>
        var ui = new firebaseui.auth.AuthUI(firebase.auth());

        var uiConfig = {
        callbacks: {
            signInSuccessWithAuthResult: function(authResult, redirectUrl) {
            // User successfully signed in.
            // Return type determines whether we continue the redirect automatically
            // or whether we leave that to developer to handle.
            return true;
            },
            uiShown: function() {
            // The widget is rendered.
            // Hide the loader.
            document.getElementById('loader').style.display = 'none';
            }
        },
        // Will use popup for IDP Providers sign-in flow instead of the default, redirect.
        signInFlow: 'popup',
        signInSuccessUrl: 'index.php',
        signInOptions: [
            // Leave the lines as is for the providers you want to offer your users.

            firebase.auth.EmailAuthProvider.PROVIDER_ID,

        ],
        // Privacy policy url.
        privacyPolicyUrl: 'index.php'
        };

        // The start method will wait until the DOM is loaded.
        ui.start('#firebaseui-auth-container', uiConfig);


    </script>
<?php include 'inc/layout/footer.php';?>