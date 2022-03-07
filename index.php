<?php
class Database
{
    private static $dbName = 'domain_system';
    private static $dbHost = 'localhost';
    private static $dbUsername = 'domain_user';
    private static $dbUserPassword = 'Jbp=aZb1QZ={96Pi';

    private static $cont = null;

    public function __construct()
    {
        exit('Init function is not allowed');
    }

    public static function connect()
    {
        // One connection through whole application
        if (null == self::$cont) {
            try {
                self::$cont = new PDO("mysql:host=" . self::$dbHost . ";" . "dbname=" . self::$dbName, self::$dbUsername, self::$dbUserPassword, [PDO::MYSQL_ATTR_LOCAL_INFILE => true]);
            } catch (PDOException $e) {
                die($e->getMessage());
            }
        }
        return self::$cont;
    }

    public static function disconnect()
    {
        self::$cont = null;
    }
}

$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$domain = $_SERVER['HTTP_HOST'];
$clist_sql = "SELECT wildcard_url FROM `domains_wildcard_ssl` WHERE `domain_name` = ?";
$result_clist = $pdo->prepare($clist_sql);
$result_clist->execute(array($domain));
$list = $result_clist->fetch();

if(isset($list['wildcard_url'])){
	$queryString =  http_build_query($_GET);
	if (strpos($list['wildcard_url'], '?') == false) {
		$redirect_link = $list['wildcard_url']."?".$queryString;
	}elseif (strpos($list['wildcard_url'], '&') !== false) {
		$redirect_link = $list['wildcard_url']."&".$queryString;
	}	
	header("Location:".$redirect_link);
}else{
	echo "Link not found.";
}
