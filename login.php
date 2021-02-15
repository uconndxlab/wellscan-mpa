<?php include 'inc/layout/header.php'; ?>

<div class="row justify-content-center align-items-center">
    <div class="col-md-12">
        <div id="firebaseui-auth-container"></div>
        <div id="loader">Loading...</div>
    </div>
</div>

<div class="row" style="margin-top:25px;">
    <div class="col-md-12"><div class="alert alert-info text-center">Send an email to joel@uconn.edu to set up an account.</div></div>
</div>




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

        // Required to enable one-tap sign-up credential helper.
        credentialHelper: firebaseui.auth.CredentialHelper.GOOGLE_YOLO,
        
        // Privacy policy url.
        privacyPolicyUrl: 'index.php'
        };

        // The start method will wait until the DOM is loaded.
        ui.start('#firebaseui-auth-container', uiConfig);


    </script>
<?php include 'inc/layout/footer.php';?>