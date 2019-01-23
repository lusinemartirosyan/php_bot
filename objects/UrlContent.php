<?php
class UrlContent{

    // database connection and table name
    private $conn;
    private $table = "sitecontent";

    // object properties
//    public $url;
//    public $depth = 5;
    public $id;
    public $title;
    public $pid;
    public $level;
    public $content;
    static $i=0;

    public function __construct($db){
        $this->conn = $db;
    }

    public function runPage($url, $parentId=0,$level=0, $depth = 2){

        $parse = parse_url($url);
        $domainName = $parse['host'];
        if (strpos($url, 'http') === false) {
            $url = 'http://' . $url;
        }

        if($depth > 0) {

            $this->content = file_get_contents($url);
            preg_match ("/<title>([^`]*?)<\/title>/", $this->content, $match);
            $this->title = isset($match[1]) ? $match[1] : '';

//            $query = "INSERT INTO
//                    " . $this->table . "
//                SET
//                    Title=:title, Content=:content, PID=:pid, Level=:level";
//
//            $stmt = $this->conn->prepare($query);
//
//            // posted values
//        //    $this->title=htmlspecialchars(strip_tags($this->title));
//        //    $this->content=htmlspecialchars(strip_tags($this->content));
//
//            $stmt->bindParam(":title", $this->title);
//            $stmt->bindParam(":content", $this->content);
//            $stmt->bindParam(":level", $level);
//            $stmt->bindParam(":pid", $parentId);
//
//            $stmt->execute();
//            $parentUniqueID = $this->conn->lastInsertId();

            $values = array('title'=>$this->title,'content'=>$this->content, 'level'=>$level,'pid'=>$parentId);
            $parentUniqueID = $this->insert($values);

            preg_match_all('~<a.*?href="(.*?)".*?>~', $this->content, $matches);

            foreach ($matches[1] as $newurl) {

                $parseNewUrl = parse_url($newurl);
                if (isset($parseNewUrl['host']) && $parseNewUrl['host'] == $domainName) {
                    echo $level.'---';
                    $this->runPage($newurl, $parentUniqueID, $level+1, $depth - 1);
                }
            }
        }

    }

    public function insert($values = array())
    {
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->conn->exec("SET CHARACTER SET utf8");

        foreach ($values as $field => $v)
            $ins[] = ':' . $field;

        $ins = implode(',', $ins);
        $fields = implode(',', array_keys($values));
        $sql = "INSERT INTO " . $this->table . " ($fields) VALUES ($ins)";

        $sth = $this->conn->prepare($sql);
        foreach ($values as $f => $v)
        {
            $sth->bindValue(':' . $f, $v);
        }
        $sth->execute();
        return $this->lastId = $this->conn->lastInsertId();
    }


    public function emptyTable(){
       $sql = "TRUNCATE TABLE   " . $this->table;

       $statement = $this->conn->prepare($sql);
       $statement->execute();

        $sql = "ALTER TABLE " . $this->table . " AUTO_INCREMENT = 1";

        $statement = $this->conn->prepare($sql);
        $statement->execute();

   }
}
?>