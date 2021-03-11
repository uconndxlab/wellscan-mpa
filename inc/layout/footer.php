

</main>






    <nav class="navbar navbar-dark fixed-bottom bg-dark flex-md-nowrap p-2 shadow">
        <form id="the_form" autocomplete="off" method="get" class="w-100 m-1" action="food.php">
            <div class="input-group">
                <input  class="form-control form-control-dark"  required type = "text" placeholder="scan a new food" name="upc" id="search_upc">
                <div class="input-group-append">
                    <button class="btn-primary btn" id="activate_scan"><i class="bi bi-upc-scan"></i></button>
                </div>
            </div>        
            <input type="hidden" name="src" value="search">
            
            <div id="interactive" style="max-width:100%;z-index:9998;" class="d-none viewport justify-content-center align-items-center">
                <div style="position:absolute;top:50%;left:10%; border:5px solid white;width:80%;z-index:9999;opacity:0.75"></div>
            </div>
        </form>
    </nav>



<div class="modal fade" id="bugReportModal">
  <div class="modal-dialog" role="document">
    <form action="#" id="bugReport_form">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Report a Bug</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="current_page" id="input_current_page" value="<?php echo basename($_SERVER['PHP_SELF']); ?>">
        <div class="form-group">
                <label for="bugText">Description <small>(your email address, current page, and organization will be automatically included in the bug report)</small></label>
                <textarea class="form-control" id="bugText" rows="3"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-block btn-primary">Submit Bug Report</button>
      </div>
    </div>
    </form>
  </div>
</div>

<script>
<?php if (basename($_SERVER['PHP_SELF']) !== "login.php"): ?>
checkAuthThen(function() {

    document.querySelector("#bugReport_form").addEventListener("submit", function(e){
        e.preventDefault();
        var db = firebase.firestore();
        console.log(firebaseUserOrg);
        db.collection("bugReports")
            .add({
                date_reported: new Date(),
                current_page:document.querySelector("#input_current_page").value,
                org:firebaseUserOrg,
                usr:firebaseEmail,
                description:document.querySelector("#bugText").value
            }
        )
        .then(function(docRef) {
            $("#bugReportModal").modal('hide');
            document.querySelector("#bugReport_form").reset();
            console.log("Your bug has been saved with ID " + docRef.id);
        })
        .catch(function(error) {
            console.error("Error adding document: ", error);
    });
    });

    document.querySelector("#search_upc").addEventListener('focus', function() {
        this.value="";
        App.init();
    });

    document.querySelector("#search_upc").addEventListener('blur', function() {
        
        document.querySelector("#interactive").classList.add("d-none");
        //Quagga.stop();
    });

    if (!navigator.mediaDevices || !navigator.mediaDevices.enumerateDevices) {
    alert("enumerateDevices() not supported.");
    
    }

    var backCamID;

    navigator.mediaDevices.enumerateDevices()
    .then(function(devices) {
    devices.forEach(function(device) {
        //alert( JSON.stringify(device) );
        if( device.kind == "videoinput" && device.label.match(/back/) != null ){
        //alert("Back found!");
        backCamID = device.deviceId;
        }
    });
    })
    .catch(function(err) {
    //alert(err.name + ": " + err.message);
    });

    if(typeof(backCamID)=="undefined"){
        console.log("back camera not found.");
    }



var App = {
    init: function() {
        var self = this;
        document.querySelector("#interactive").classList.remove("d-none");
        Quagga.init(this.state, function(err) {
            if (err) {
                return self.handleError(err);
            }
            App.attachListeners();

            Quagga.start();
        });
    },
    handleError: function(err) {
        console.log(err);
    },
    initCameraSelection: function(){
        var streamLabel = Quagga.CameraAccess.getActiveStreamLabel();

        return Quagga.CameraAccess.enumerateVideoDevices()
        .then(function(devices) {
            function pruneText(text) {
                return text.length > 30 ? text.substr(0, 30) : text;
            }
            var $deviceSelection = document.getElementById("deviceSelection");
            while ($deviceSelection.firstChild) {
                $deviceSelection.removeChild($deviceSelection.firstChild);
            }
            devices.forEach(function(device) {
                var $option = document.createElement("option");
                $option.value = device.deviceId || device.id;
                $option.appendChild(document.createTextNode(pruneText(device.label || device.deviceId || device.id)));
                $option.selected = streamLabel === device.label;
                $deviceSelection.appendChild($option);
            });
        });
    },
    attachListeners: function() {
        var self = this;

        // self.initCameraSelection();
        $(".controls").on("click", "button.stop", function(e) {
            e.preventDefault();
            Quagga.stop();
            
        });

    },

    detachListeners: function() {
        $(".controls").off("click", "button.stop");
        $(".controls .reader-config-group").off("change", "input, select");
    },

    state: {
        inputStream: {
            type : "LiveStream",
            constraints: {
                width: {min: 640},
                height: {min: 480},
                facingMode: "environment",
                aspectRatio: {min: 1, max: 2},
                deviceId:backCamID
            }
        },
        locator: {
            patchSize: "medium",
            halfSample: true
        },
        numOfWorkers: 2,
        frequency: 10,
        decoder: {
            readers : [{
                format: "upc_reader",
                config: {}
            }]
        },
        locate: true
    },
    lastResult : null
};

    Quagga.onProcessed(function(result) {

    });

    Quagga.onDetected(function(result) {
        var code = result.codeResult.code;

        if (App.lastResult !== code) {
            App.lastResult = code;
            var $node = null, canvas = Quagga.canvas.dom.image;
                actualImage = canvas.toDataURL();
            
            //Quagga.stop();
            document.querySelector("#search_upc").value = code;
            document.querySelector("#interactive").classList.add("d-none");
            
            document.querySelector("#the_form").submit();
        }
    });

    // document.querySelector("#activate_scan").addEventListener("click", function() {
    //     App.init();
    // })

    document.querySelector("#activate_scan").addEventListener("click", function() {
        App.init();
    })
});



</script>
<?php endif;?>


</body>
</html>