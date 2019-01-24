<?php

class ExportXml
{
    private $conn;
    private $table = "site_content";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function AllData()
    {
        $query = "SELECT ID, PID, Title, Content, Level, Url FROM " . $this->table . " Order By Level";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchall(PDO::FETCH_ASSOC);
        return $result;
    }


    function buildTree(array $elements=[], $parentId = 0) {
        $branch = array();

        $elements = $this->AllData();

        foreach ($elements as $element) {
            if ($element['PID'] == $parentId) {
                $children = $this->buildTree($elements, $element['ID']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }


//    function array_to_xml($array, &$xml_user_info)
//    {
//        foreach ($array as $key => $value) {
//            if (is_array($value)) {
//                if (!is_numeric($key)) {
//                    $subnode = $xml_user_info->addChild("$key");
//                    array_to_xml($value, $subnode);
//                } else {
//                    $subnode = $xml_user_info->addChild("website$key");
//                    $this->array_to_xml($value, $subnode);
//                }
//            } else {
//                $xml_user_info->addChild("$key", htmlspecialchars("$value"));
//            }
//        }
//    }

    function array_to_xml($array, &$xml_user_info)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                if (!is_numeric($key)) {
                    $subnode = $xml_user_info->addChild("$key");
                    $this->array_to_xml($value, $subnode);
                } else {
                    $subnode = $xml_user_info->addChild("website$key");
                    $this->array_to_xml($value, $subnode);
                }
            } else {
                $xml_user_info->addChild("$key", htmlspecialchars("$value"));
            }
        }
    }

}

?>