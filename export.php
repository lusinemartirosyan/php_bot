<?php
include_once 'config/database.php';
include_once 'objects/ExportXml.php';

$database = new Database();
$db = $database->getConnection();

$objSearch = new ExportXml($db);
$allData = $objSearch->AllData();

$x = $objSearch->buildTree();
//echo '<pre>';
//print_r($x);
//echo '</pre>';exit;

$xml_user_info = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\" ?><websites></websites>");

$objSearch->array_to_xml($x, $xml_user_info);

$xml_file = $xml_user_info->asXML('storage/websites.xml');

$page_title = "Export pages as XML";
include_once "layout_header.php";

echo '<a href="storage/websites.xml" download>
            <div class>Download XML</div>
    </a>';

if($xml_file) {
    echo '<div class="alert alert-success">All pages were exported as XML file successfully.</div>';

} else {
    echo '<div class="alert alert-danger">Error during export.</div>';
}

include_once "layout_footer.php";
?>
