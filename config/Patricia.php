<?php

namespace Kingsley\Vanillaphp;

use PDO;
use PDOException;

require_once 'Config.php';


class Patricia
{
    private $dbh;
    private $stmt;

    public function __construct()
    {
        $this->dbConnect();
    }

    private function dbConnect()
    {
        try{
            $this->dbh = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8", DB_USER, DB_PASS,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        }
        catch (PDOException $exception){
            echo $exception->getMessage();
        }
    }

    public function query($sql){
        if(empty($sql)) return false;
        $this->stmt = $this->dbh->prepare($sql);
        return $this->stmt;
    }

    public function dbquery($sql){
        if(empty($sql)) return false;
        $this->stmt = $this->dbh->query($sql);
        return $this->stmt;
    }


    public function exec($param=array()){
        return $this->stmt->execute($param);
    }

    public function dbcountchanges($sql){
        if(empty($sql)) return false;
        $this->dbquery($sql);
        $affected_rows = $this->rowCount();
        if($affected_rows > 0)return $affected_rows;
        else return false;
    }

    public function dbarray($sql){
        if(empty($sql)) return false;
        if(is_null($this->dbh)) $this->dbconnect(); //dbxnect to database
        return $this->dbquery($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function dbrow($sql){
        if(empty($sql)) return false;
//        if(is_null($this->dbh)) $this->dbconnect(); //dbxnect to database
        return $this->dbquery($sql)->fetch(PDO::FETCH_ASSOC);
    }

    public function dbval($sql){
        if(empty($sql)) return false;
        if(is_null($this->dbh)) $this->dbconnect(); //dbxnect to database
        return $this->dbquery($sql)->fetchColumn();
    }

    public function rowCount(){
        return $this->stmt->rowCount();
    }

    public function cleanInput($string, $length = null, $html = false, $striptags = true)
    {
        $length = 0 + $length;

        if(!$html) return ($length > 0) ? substr(addslashes(trim(preg_replace('/<[^>]*>/', '', $string))),0,$length) : addslashes(trim(preg_replace('/<[^>]*>/', '', $string)));
        $allow  = "<b><h1><h2><h3><h4><h5><h6><br><br /><hr><hr /><em><strong><a><ul><ol><li><dl><dt><dd><table><tr><th><td><blockquote><address><div><p><span><i><u><s><sup><sub><style><tbody>";
        $string = utf8_decode(trim($string));// avoid unicode codec issues
        if($striptags) $string = strip_tags($string, $allow);

        $aDisabledAttributes = array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavaible', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragdrop', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterupdate', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmoveout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
        $string = str_ireplace($aDisabledAttributes,'x',$string);

        //remove javascript from tags
        while( preg_match("/<(.*)?javascript.*?\(.*?((?>[^()]+)|(?R)).*?\)?\)(.*)?>/i", $string))
            $string = preg_replace("/<(.*)?javascript.*?\(.*?((?>[^()]+)|(?R)).*?\)?\)(.*)?>/i", "<$1$3$4$5>", $string);

        // dump expressions from contibuted content
        $string = preg_replace("/:expression\(.*?((?>[^(.*?)]+)|(?R)).*?\)\)/i", "", $string);

        while( preg_match("/<(.*)?:expr.*?\(.*?((?>[^()]+)|(?R)).*?\)?\)(.*)?>/i", $string))
            $string = preg_replace("/<(.*)?:expr.*?\(.*?((?>[^()]+)|(?R)).*?\)?\)(.*)?>/i", "<$1$3$4$5>", $string);

        // convert HTML characters
        $string = str_replace("#", "#", htmlentities($string));
        $string = addslashes(str_replace("%", "%", $string));

        if($length > 0) $string = substr($string, 0, $length);
        return $string;
    }//end of cleanInput function

    public function pageReload() {
        ob_end_clean();
        die('<meta http-equiv="refresh" content="0"/> <script type="text/javascript">window.location.href=window.location.href;</script>');
    }//end function pageReload

    public function isValidEmail($email="",$checkDomain=false){
        if($checkDomain){
            //list($userName, $mailDomain) = split("@", $email);
            $mailDomain = explode("@", $email);
            reset($mailDomain);
            if (gethostbyname($mailDomain[0]))  {
                // this is a valid email domain!
            }
            else {
                return false;
            }
        }
        if(!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $email)) {
            return false;
        } else {
            return true;
        }
    }//end function isValidEmail

}

$patricia = new Patricia();