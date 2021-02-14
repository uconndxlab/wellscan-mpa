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
                <input class="form-control"  required type = "text" name="upc" id="search_upc">
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
    document.querySelector("#search_upc").focus();  
</script>

<?php include 'inc/layout/footer.php';?>