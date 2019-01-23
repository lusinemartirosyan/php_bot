<?php
// include database and object files
include_once 'config/database.php';
include_once 'objects/UrlContent.php';



$database = new Database();
$db = $database->getConnection();

$urlContent = new UrlContent($db);
//$category = new Category($db);

// query products
//$stmt = $product->readAll($from_record_num, $records_per_page);
//$num = $stmt->rowCount();

$page_title = "Home page";
include_once "layout_header.php";



if($_POST){
    $url = $_POST['url'];
    $urlContent->runPage($url);

//    if($product->create()){
//        echo '<div class="alert alert-success">Product was created.</div>';
//    }
//    else{
//        echo '<div class="alert alert-danger">Unable to create product.</div>';
//    }
}

?>
<!-- HTML form for creating a product -->
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
    <div class="form-group">
        <label for="usr">Your URL:</label>
        <input type="text" class="form-control" id="url" name="url" value="http://" required>
    </div>
    <input type="submit" class="btn btn-outline-secondary" value="Process">

</form>

<?php
include_once "layout_footer.php";
?>