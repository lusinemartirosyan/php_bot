<?php

class UrlContent
{
    private $conn;
    private $table = "site_content";

    public $id;
    public $title;
    public $pid;
    public $level;
    public $content;
    static $i = 0;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function runPage($url, $parentId = 0, $level = 0, $depth = 3)
    {

        $parse = parse_url($url);
        $domainName = $parse['host'];
        if (strpos($url, 'http') === false) {   //check if url contain http or not
            $url = 'http://' . $url;
        }

        if ($depth > 0) {

            $curl_handle = curl_init();                  //request to given url
            curl_setopt($curl_handle, CURLOPT_URL, $url);
            curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
            curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
            $this->content = curl_exec($curl_handle);
            curl_close($curl_handle);

            preg_match("/<title>([^`]*?)<\/title>/", $this->content, $match);   //getting title of the page
            $this->title = isset($match[1]) ? $match[1] : '';

            $values = array('title' => $this->title, 'content' => $this->content, 'level' => $level, 'pid' => $parentId, 'url' => $url);
            $parentUniqueID = $this->insert($values);

            preg_match_all('~<a.*?href="(.*?)".*?>~', $this->content, $matches);

            foreach ($matches[1] as $newurl) {

                $parseNewUrl = parse_url($newurl);
                if (isset($parseNewUrl['host']) && $parseNewUrl['host'] == $domainName) {   //exclude links from other domains
                    $this->runPage($newurl, $parentUniqueID, $level + 1, $depth - 1);
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
        foreach ($values as $f => $v) {
            $sth->bindValue(':' . $f, $v);
        }
        $sth->execute();
        return $this->lastId = $this->conn->lastInsertId();
    }


    public function emptyTable()
    {
        $sql = "TRUNCATE TABLE   " . $this->table;

        $statement = $this->conn->prepare($sql);
        $statement->execute();

        $sql = "ALTER TABLE " . $this->table . " AUTO_INCREMENT = 1";

        $statement = $this->conn->prepare($sql);
        $statement->execute();

    }
}

?>