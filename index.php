<?php include 'inc/layout/header.php'; ?>

<div class="row h-100 justify-content-center align-items-center">
    <form id="the_form" method="get" class="col-12" action="food.php">
        <div class="form-group">
            <label for="upc">UPC</label>
            <div class="input-group">
                <input class="form-control"  required type = "text" name="upc" id="upc">
                <div class="input-group-append">
                    <button type="button" id="activate_scan" data-toggle="modal" data-target="#exampleModal" class="btn btn-secondary"><i class="bi bi-upc-scan"></i></button>
                </div>
            </div>
            
        </div>
        <div class="form-group">
            <input type="hidden" name="src" value="search">
            <button type="submit" class="btn btn-block btn-primary" id="btn_search">Search For Food</button>
        </div>
    </form>
</div>

<div class="modal fade" id="exampleModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="interactive" class="modal-body">
        
      </div>
    </div>
  </div>
</div>


<script>
    document.querySelector("#upc").focus();

    document.querySelector("#upc").addEventListener('focus', function() {
        this.value="";
    });

    var Quagga = window.Quagga;
    var App = {
        _scanner: null,
        init: function() {
            this.attachListeners();
        },
        activateScanner: function() {
            var scanner = this.configureScanner('#interactive'),
                onDetected = function (result) {
                    document.querySelector('#upc').value = result.codeResult.code;
                    stop();
                }.bind(this),
                stop = function() {
                    scanner.stop();  // should also clear all event-listeners?
                    scanner.removeEventListener('detected', onDetected);
                    this.hideOverlay();
                    this.attachListeners();
                }.bind(this);

            //this.showOverlay(stop);
            scanner.addEventListener('detected', onDetected).start();
        },
        attachListeners: function() {
            var self = this,
                button = document.querySelector('#activate_scan');

            button.addEventListener("click", function onClick(e) {
                e.preventDefault();
                button.removeEventListener("click", onClick);
                self.activateScanner();
            });
        },
        showOverlay: function(cancelCb) {
            if (!this._overlay) {
                var content = document.createElement('div'),
                    closeButton = document.createElement('div');

                closeButton.appendChild(document.createTextNode('X'));
                content.className = 'overlay__content';
                closeButton.className = 'overlay__close';
                this._overlay = document.createElement('div');
                this._overlay.className = 'overlay';
                this._overlay.appendChild(content);
                content.appendChild(closeButton);
                closeButton.addEventListener('click', function closeClick() {
                    closeButton.removeEventListener('click', closeClick);
                    cancelCb();
                });
                document.body.appendChild(this._overlay);
            } else {
                var closeButton = document.querySelector('.overlay__close');
                closeButton.addEventListener('click', function closeClick() {
                    closeButton.removeEventListener('click', closeClick);
                    cancelCb();
                });
            }
            this._overlay.style.display = "block";
        },
        hideOverlay: function() {
            $('#exampleModal').modal('hide');
            document.querySelector("#the_form").submit();
        },
        configureScanner: function(selector) {
            if (!this._scanner) {
                this._scanner = Quagga
                    .decoder({readers: ['ean_reader']})
                    .locator({patchSize: 'medium'})
                    .fromSource({
                        target: selector,
                        constraints: {
                            width: 800,
                            height: 600,
                        }
                    });
            }
            return this._scanner;
        }
    };
App.init();


                
                
   
                
            




</script>

<?php include 'inc/layout/footer.php';?>