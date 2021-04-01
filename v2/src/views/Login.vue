<template>
<div>
    <img style="max-width:100%;text-align:center;" src="@/assets/logo.png">
  <v-card>
    <v-tabs
      v-model="tab"
      centered
      light
      icons-and-text
    >
      <v-tabs-slider></v-tabs-slider>

      <v-tab href="#tab-login">
        Log In
        <v-icon>mdi-account</v-icon>
      </v-tab>

      <v-tab href="#tab-register">
        Register
        <v-icon>mdi-account-outline</v-icon>
      </v-tab>

    </v-tabs>

    <v-tabs-items v-model="tab">
      <v-tab-item
        :value="'tab-login'"
      >
        <v-card light>
          <v-form>

               <v-container>
                <v-row>



                    <v-col
                    cols="12"
                    md="12"
                    >
                        <v-text-field
                            v-model="loginform.email"
                            label="E-mail"
                            required
                        ></v-text-field>
                </v-col>

                <v-col
                    cols="12"
                    md="12"
                    >
                        <v-text-field
                            v-model="loginform.password"
                            :append-icon="showLoginPassword ? 'mdi-eye' : 'mdi-eye-off'"
                            :type="showLoginPassword ? 'text' : 'password'"
                            name="login-password"
                            label="Password"
                            counter
                            @click:append="showLoginPassword = !showLoginPassword"
                            ></v-text-field>
                </v-col>

                </v-row>

                <v-row v-if="loginform.response.attempted && loginform.response.success == false">
                  <v-col>
                    <v-alert type="error" text>
                      {{loginform.response.message}}
                    </v-alert>
                  </v-col>
                </v-row>
            </v-container>
          </v-form>
          <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn block large color="primary" @click="attemptLogin()">Log In</v-btn>
            </v-card-actions>

        </v-card>
      </v-tab-item>

     <v-tab-item
        :value="'tab-register'"
        
      >
        <v-card light>
          <v-container>
            <v-row>
              <v-col>
                <v-alert v-if="newUser.createdNewUser" type="success" text>
                  Your user account has been created. You may now log in.
                </v-alert>
              </v-col>
            </v-row>
          </v-container>
          <v-form v-if="!newUser.createdNewUser" @submit.prevent="register">
            <v-container>
                <v-row>
                    <v-col
                    cols="6"
                    md="6"
                    >
                        <v-text-field
                            v-model="regform.firstname"
                            
                            
                            label="First name"
                            required
                        ></v-text-field>
                    </v-col>

                    <v-col
                    cols="6"
                    md="6"
                    >
                        <v-text-field
                            v-model="regform.lastname"
                            
                            
                            label="Last name"
                            required
                        ></v-text-field>
                    </v-col>

                    <v-col
                    cols="12"
                    md="12"
                    >
                        <v-text-field
                            v-model="regform.email"
                           
                            label="E-mail"
                            required
                        ></v-text-field>
                </v-col>

                <v-col
                    cols="12"
                    md="12"
                    >
                        <v-text-field
                            v-model="regform.password"
                            :append-icon="showRegPassword ? 'mdi-eye' : 'mdi-eye-off'"
                            :type="showRegPassword ? 'text' : 'password'"
                            name="reg-password"
                            label="Password"
                            
                            counter
                            @click:append="showRegPassword = !showRegPassword"
                            ></v-text-field>
                </v-col>
                <v-col cols="12">

                    <v-select
                    :items="orgs"
                    filled
                    label="Associated Organization"
                    v-model="associatedOrg"
                    hint="Will require approval by your organization"
                    ></v-select>
                    

                </v-col>


                </v-row>
                <v-row v-if="newUser.failed">
                  <v-col>
                    <v-alert type="error" text>
                      {{newUser.message}}
                    </v-alert>
                  </v-col>
                </v-row>
            </v-container>
            </v-form>
            <v-card-actions>
                <v-btn v-if="!newUser.createdNewUser" @click="register()" block large color="primary">Register</v-btn>
            </v-card-actions>
        </v-card>
      </v-tab-item>
    </v-tabs-items>
  </v-card>
</div>
</template>

<script>
import firebase from "firebase";
  export default {
    data () {
      return {
        showRegPassword: false,
        showLoginPassword:false,
        associatedOrg:"",
        orgs: [
            "pha",
            "uconn",
            "pittsburgh",
            "maryland"
        ],

        regform: {
          firstname:"",
          lastname:"",
          email:"",
          password:""
        },

        loginform: {
          email: "",
          password: "",
          response: {
            attempted:false,
            success:false,
            message:""
          }
        },

        newUser: {
          createdNewUser:false,
          message:"",
          failed:false,
          displayName:"",
          email:""
        },

      }
    },
    props:['tab'],

    methods: {
      register() {
        var that = this;
        firebase
          .auth()
          .createUserWithEmailAndPassword(this.regform.email, this.regform.password)
          .then(data => {
            data.user
              .updateProfile({
                displayName: this.regform.firstname + " " + this.regform.lastname,
                associatedOrg:that.associatedOrg
              })
              .then(() => {

                this.handleNewUserCreation(data.user)
            
              });
          })
          .catch(err => {
            this.handleUserCreationError(err);
          });
      },

      handleNewUserCreation(usr) {
        var that = this;
        this.newUser.createdNewUser = true;
        this.newUser.displayName = usr.displayName;
        this.newUser.email = usr.email;
        
        var db = firebase.firestore();
        //var that = this;
        
        
        
        
        db.collection("users")
          .doc(usr.email)
            .set({
                name: usr.email,
                usr_type: "user",
                organization: that.associatedOrg
            })
            .then(() => {
                console.log("Document successfully written!");
            })
            .catch((error) => {
                console.error("Error writing document: ", error);
            });

      },

      handleUserCreationError(err) {
        this.newUser.failed = true;
        this.newUser.message = err.message;
        console.log(this.error)
      },

      attemptLogin() {
        firebase
          .auth()
          .signInWithEmailAndPassword(this.loginform.email, this.loginform.password)
          .then(data => {
            console.log(data);
            this.$router.replace({ name: "Recently Scanned" });
          })
          .catch(err => {
            this.handleLoginError(err)
          });
      },

      handleLoginError(err) {
        this.loginform.response.attempted = true;
        this.loginform.response.success = false;
        this.loginform.response.message = err.message;
      }
  }
  }
</script>