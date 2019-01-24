<?php

class SearchPage
{
    private $conn;
    private $table = "site_content";

    public $keyword;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function Search($keyword)
    {
        $query = "SELECT ID, Title, Content,PID, Level, Url FROM " . $this->table . " Where Title LIKE '%" . $keyword . "%'
                     UNION ALL
                    SELECT ID, Title, Content,PID, Level, Url FROM " . $this->table . " Where Content LIKE '%" . $keyword . "%'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}

?>