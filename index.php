<?php
    $title = "Food Search";
?>
<?php include 'inc/layout/header.php'; ?>

<style>
.drawingBuffer {display:none;}
.jumbotron {
    background-color:var(--orange);
    color:var(--white);
}
</style>
<div class="row h-100 justify-content-center align-items-center">
    <div class="col-12 text-center">
        <img src="logo.png" class="img-fluid">
        <div class="alert alert-info">Scan a barcode or enter a UPC to get started.</div>
    </div>
</div>






<script>
    document.querySelector("#search_upc").focus();  

    document.querySelector("#search_upc").addEventListener("keyup", function() {
        console.log("keyup");
        document.querySelector("#btn_search").classList.remove("d-none");
    });

</script>

<?php include 'inc/layout/footer.php';?>