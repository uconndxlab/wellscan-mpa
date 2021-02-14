<?php include 'inc/layout/header.php'; ?>

<style>
.drawingBuffer {display:none;}
</style>
<h3>Food Search</h3>
<div class="row h-100 justify-content-center align-items-center">
    <form id="the_form" method="get" class="col-12" action="food.php">
        <div class="form-group">
            <label for="upc">UPC</label>
            <div class="input-group">
                <input class="form-control"  required type = "text" name="upc" id="upc">
                <div class="input-group-append">
                    <button type="button" id="activate_scan" class="btn btn-secondary"><i class="bi bi-upc-scan"></i></button>
                </div>
            </div>
        </div>
        <div class="form-group">
            <input type="hidden" name="src" value="search">
            <button type="submit" class="btn btn-block btn-primary" id="btn_search">Lookup</button>
        </div>
        <div id="interactive" style="position:relative;display:flex;" class="viewport justify-content-center align-items-center">
            <div style="position:absolute;border:5px solid white;width:80%;z-index:9999;opacity:0.75"></div>
        </div>
    </form>
</div>




<script>
    document.querySelector("#upc").focus();

    document.querySelector("#search_upc").addEventListener('focus', function() {
        this.value="";
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

            document.querySelector("#upc").value = code;
            document.querySelector("#interactive").classList.add("d-none");
            Quagga.stop();
        }
    });

document.querySelector("#activate_scan").addEventListener("click", function() {
    App.init();
})
                
                
   
                
            




</script>

<?php include 'inc/layout/footer.php';?>