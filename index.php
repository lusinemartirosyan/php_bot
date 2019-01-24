<?php
// include database and object files
include_once 'config/database.php';
include_once 'objects/UrlContent.php';

$database = new Database();
$db = $database->getConnection();

$urlContent = new UrlContent($db);

$page_title = "Home page";
include_once "layout_header.php";

if ($_POST) {
    $url = $_POST['url'];
    $urlContent->emptyTable();
    $urlContent->runPage($url);

    echo '<div class="alert alert-success">All pages were imported.</div>';
}

?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label for="usr">Your URL:</label>
            <input type="text" class="form-control" id="url" name="url" value="http://" required>
        </div>
        <input type="submit" class="btn btn-outline-secondary" value="Process">

    </form>

<?php
include_once "layout_footer.php";
?>