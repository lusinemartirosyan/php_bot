<?php
include_once 'config/database.php';
include_once 'objects/SearchPage.php';

$database = new Database();
$db = $database->getConnection();

$objSearch = new SearchPage($db);

$page_title = "Search page";
include_once "layout_header.php";


if ($_POST) {
    $keyword = $_POST['word'];
    $stmt = $objSearch->Search($keyword);
    $num = $stmt->rowCount();
}

?>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <div class="form-group">
        <label for="usr">Keyword:</label>
        <input type="text" class="form-control" id="word" name="word">
    </div>
    <div id="searchContent"><?php
        if ($_POST && $num > 0) {
            echo '<table class="table">';
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                extract($row);

                echo "<tr>";
                echo "<td>" . preg_replace("/\w*?$keyword\w*/i", "<b style='background:yellow;'>$keyword</b>", $Title) . "</td>";
                echo "<td style='width:300px;'>" . preg_replace("/\w*?$keyword\w*/i", "<b style='background:yellow;'>$keyword</b>", substr($Content, strpos($Content, $keyword) - 15, strlen($keyword) + 50)) . "</td>";
                echo "<td><a href='" . $Url . "' target='_blank'>{$Url}</a></td>";
                echo "</tr>";
            }
            echo '</table>';
        }
        ?></div>
    <input type="submit" class="btn btn-outline-secondary" value="Process">

</form>
<?php
include_once "layout_footer.php";
?>

